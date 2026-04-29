<?php
/**
 * fix_module_migrations.php
 *
 * Runs ALL module + core migrations that were previously just "marked as done"
 * without actually executing. Handles BOTH named-class and anonymous-class
 * (return new class extends Migration) migration styles.
 *
 * Safe to run multiple times — skips tables/permissions that already exist.
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
use Illuminate\Database\Migrations\Migration;

$totalRan   = 0;
$totalSkip  = 0;
$totalError = 0;

/**
 * Resolve a migration instance from a file.
 * Handles both:
 *   - Named class:     class FooBar extends Migration { ... }
 *   - Anonymous class: return new class extends Migration { ... };
 */
function loadMigration(string $file): ?Migration
{
    // Capture classes defined BEFORE and AFTER including the file
    $before = get_declared_classes();
    $result = include $file;   // 'include' (not require_once) so we always get the return value
    $after  = get_declared_classes();

    // Anonymous class: the file returned a Migration instance
    if ($result instanceof Migration) {
        return $result;
    }

    // Named class: find the newly declared class that extends Migration
    $new = array_diff($after, $before);
    foreach ($new as $className) {
        if (is_subclass_of($className, Migration::class)) {
            return new $className();
        }
    }

    return null;
}

/**
 * Run a single migration file with full error handling.
 */
function runMigration(string $file, int &$totalRan, int &$totalSkip, int &$totalError): void
{
    $filename = basename($file, '.php');

    // Remove any stale record so we can attempt a fresh run
    DB::table('migrations')->where('migration', $filename)->delete();

    $migration = loadMigration($file);

    if (!$migration) {
        echo "  [SKIP]  $filename — could not load migration class\n";
        $totalSkip++;
        return;
    }

    try {
        $migration->up();

        DB::table('migrations')->insert(['migration' => $filename, 'batch' => 99]);
        echo "  [OK]    $filename\n";
        $totalRan++;

    } catch (\Illuminate\Database\QueryException $e) {
        $msg = $e->getMessage();
        $code = $e->getCode();

        // 1050 = Table already exists, 1060 = Duplicate column, 1061 = Duplicate index
        if (in_array($code, ['42S01', '1050', '1060', '1061']) ||
            str_contains($msg, 'already exists') ||
            str_contains($msg, 'Duplicate column') ||
            str_contains($msg, 'Duplicate key')) {
            DB::table('migrations')->insertOrIgnore(['migration' => $filename, 'batch' => 99]);
            echo "  [SKIP]  $filename — already exists\n";
            $totalSkip++;
        } else {
            echo "  [ERROR] $filename — $msg\n";
            $totalError++;
            // Still mark as done so we don't fail repeatedly on the same broken migration
            DB::table('migrations')->insertOrIgnore(['migration' => $filename, 'batch' => 99]);
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
            DB::table('migrations')->insertOrIgnore(['migration' => $filename, 'batch' => 99]);
        }
    }
}

// ── 1. Module migrations ──────────────────────────────────────────────────────
$modulesPath = __DIR__ . '/Modules';
$modules = array_filter(
    scandir($modulesPath),
    fn($d) => $d !== '.' && $d !== '..' && is_dir("$modulesPath/$d")
);

foreach ($modules as $module) {
    $migrationsPath = "$modulesPath/$module/Database/Migrations";
    if (!is_dir($migrationsPath)) continue;

    $files = glob("$migrationsPath/*.php");
    if (empty($files)) continue;

    sort($files);
    echo "\n=== Module: $module (" . count($files) . " migrations) ===\n";

    foreach ($files as $file) {
        runMigration($file, $totalRan, $totalSkip, $totalError);
    }
}

// ── 2. Core 2026 migrations (cooler, etims, pesapal, kcb, etc.) ──────────────
$coreMigrations = glob(__DIR__ . '/database/migrations/2026_*.php');
sort($coreMigrations);

echo "\n=== Core 2026 Migrations (" . count($coreMigrations) . ") ===\n";
foreach ($coreMigrations as $file) {
    runMigration($file, $totalRan, $totalSkip, $totalError);
}

// ── Summary ───────────────────────────────────────────────────────────────────
echo "\n" . str_repeat('=', 52) . "\n";
echo "Done!  Ran: $totalRan  |  Skipped: $totalSkip  |  Errors: $totalError\n";
echo str_repeat('=', 52) . "\n";
