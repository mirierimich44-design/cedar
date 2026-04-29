<?php
/**
 * fix_module_migrations.php
 *
 * Runs ALL module migrations that were previously just "marked as done"
 * without actually executing. Safe to run multiple times — uses try/catch
 * so it skips tables/permissions that already exist.
 *
 * Usage:  php fix_module_migrations.php
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$modulesPath = __DIR__ . '/Modules';
$modules = array_filter(scandir($modulesPath), fn($d) => $d !== '.' && $d !== '..' && is_dir("$modulesPath/$d"));

$totalRan   = 0;
$totalSkip  = 0;
$totalError = 0;

foreach ($modules as $module) {
    $migrationsPath = "$modulesPath/$module/Database/Migrations";
    if (!is_dir($migrationsPath)) continue;

    $files = glob("$migrationsPath/*.php");
    if (empty($files)) continue;

    sort($files);

    echo "\n=== Module: $module (" . count($files) . " migrations) ===\n";

    foreach ($files as $file) {
        $filename  = basename($file, '.php');

        // Remove existing record so we can re-run it
        DB::table('migrations')->where('migration', $filename)->delete();

        // Load the migration class
        require_once $file;

        // Resolve class name from file
        $class = null;
        $tokens = token_get_all(file_get_contents($file));
        for ($i = 0; $i < count($tokens); $i++) {
            if (is_array($tokens[$i]) && $tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < count($tokens); $j++) {
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $class = $tokens[$j][1];
                        break 2;
                    }
                }
            }
        }

        if (!$class || !class_exists($class)) {
            echo "  [SKIP]  $filename — could not resolve class\n";
            $totalSkip++;
            continue;
        }

        try {
            $migration = new $class();
            $migration->up();

            // Record as ran
            DB::table('migrations')->insert([
                'migration' => $filename,
                'batch'     => 99,
            ]);

            echo "  [OK]    $filename\n";
            $totalRan++;

        } catch (\Illuminate\Database\QueryException $e) {
            $msg = $e->getMessage();

            // Table already exists — safe to skip
            if (str_contains($msg, 'already exists') || str_contains($msg, "1050") || str_contains($msg, "Duplicate")) {
                DB::table('migrations')->insertOrIgnore(['migration' => $filename, 'batch' => 99]);
                echo "  [SKIP]  $filename — already exists\n";
                $totalSkip++;

            } else {
                echo "  [ERROR] $filename — $msg\n";
                $totalError++;
            }

        } catch (\Spatie\Permission\Exceptions\PermissionAlreadyExists $e) {
            DB::table('migrations')->insertOrIgnore(['migration' => $filename, 'batch' => 99]);
            echo "  [SKIP]  $filename — permission already exists\n";
            $totalSkip++;

        } catch (\Spatie\Permission\Exceptions\RoleAlreadyExists $e) {
            DB::table('migrations')->insertOrIgnore(['migration' => $filename, 'batch' => 99]);
            echo "  [SKIP]  $filename — role already exists\n";
            $totalSkip++;

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate')) {
                DB::table('migrations')->insertOrIgnore(['migration' => $filename, 'batch' => 99]);
                echo "  [SKIP]  $filename — already exists\n";
                $totalSkip++;
            } else {
                echo "  [ERROR] $filename — $msg\n";
                $totalError++;
            }
        }
    }
}

// Also run core app migrations that might be missing (cooler, etims, pesapal etc.)
$coreMigrations = glob(__DIR__ . '/database/migrations/2026_*.php');
sort($coreMigrations);

echo "\n=== Core 2026 Migrations (" . count($coreMigrations) . ") ===\n";
foreach ($coreMigrations as $file) {
    $filename = basename($file, '.php');

    DB::table('migrations')->where('migration', $filename)->delete();
    require_once $file;

    $class = null;
    $tokens = token_get_all(file_get_contents($file));
    for ($i = 0; $i < count($tokens); $i++) {
        if (is_array($tokens[$i]) && $tokens[$i][0] === T_CLASS) {
            for ($j = $i + 1; $j < count($tokens); $j++) {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                    $class = $tokens[$j][1];
                    break 2;
                }
            }
        }
    }

    if (!$class || !class_exists($class)) {
        echo "  [SKIP]  $filename — could not resolve class\n";
        $totalSkip++;
        continue;
    }

    try {
        $migration = new $class();
        $migration->up();
        DB::table('migrations')->insert(['migration' => $filename, 'batch' => 99]);
        echo "  [OK]    $filename\n";
        $totalRan++;
    } catch (\Exception $e) {
        $msg = $e->getMessage();
        if (str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate') || str_contains($msg, '1050') || str_contains($msg, '1060')) {
            DB::table('migrations')->insertOrIgnore(['migration' => $filename, 'batch' => 99]);
            echo "  [SKIP]  $filename — already exists\n";
            $totalSkip++;
        } else {
            echo "  [ERROR] $filename — $msg\n";
            $totalError++;
        }
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "Done!  Ran: $totalRan  |  Skipped: $totalSkip  |  Errors: $totalError\n";
echo str_repeat('=', 50) . "\n";
