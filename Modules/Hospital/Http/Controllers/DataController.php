<?php

namespace Modules\Hospital\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{
    /**
     * Defines user permissions for the Hospital module.
     *
     * @return array
     */
    public function user_permissions(): array
    {
        return [
            [
                'value'   => 'hospital.admin',
                'label'   => 'Hospital Admin',
                'default' => false,
            ],
            [
                'value'   => 'hospital.receptionist',
                'label'   => 'Hospital Receptionist',
                'default' => false,
            ],
            [
                'value'   => 'hospital.doctor',
                'label'   => 'Hospital Doctor',
                'default' => false,
            ],
            [
                'value'   => 'hospital.lab_technician',
                'label'   => 'Hospital Lab Technician',
                'default' => false,
            ],
            [
                'value'   => 'hospital.pharmacist',
                'label'   => 'Hospital Pharmacist',
                'default' => false,
            ],
            [
                'value'   => 'hospital.mortuary',
                'label'   => 'Hospital Mortuary Officer',
                'default' => false,
            ],
        ];
    }

    /**
     * Adds Hospital menu items to the admin sidebar.
     *
     * @return void
     */
    public function modifyAdminMenu(): void
    {
        // Check framework-level enable/disable (modules_statuses.json)
        $modules_statuses = json_decode(@file_get_contents(base_path('modules_statuses.json')), true) ?? [];
        if (isset($modules_statuses['Hospital']) && !$modules_statuses['Hospital']) {
            return;
        }

        Menu::modify('admin-sidebar-menu', function ($menu) {
            $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 21l18 0" />
                        <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" />
                        <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                        <path d="M10 9l4 0" />
                        <path d="M12 7l0 4" />
                    </svg>';

            $hospital = $menu->add('Hospital', [
                'icon'   => $icon,
                'active' => request()->segment(1) === 'hospital',
            ])->order(55);

            $hospital->url(
                route('hospital.patients.index'),
                'Patients',
                ['icon' => '<i class="fa fa-users"></i>', 'active' => request()->is('hospital/patients*')]
            );

            $hospital->url(
                route('hospital.visits.index'),
                "Today's Visits",
                ['icon' => '<i class="fa fa-calendar"></i>', 'active' => request()->is('hospital/visits*')]
            );

            $hospital->url(
                route('hospital.lab.index'),
                'Lab Orders',
                ['icon' => '<i class="fa fa-flask"></i>', 'active' => request()->is('hospital/lab-orders*')]
            );

            $hospital->url(
                route('hospital.mortuary.index'),
                'Mortuary',
                ['icon' => '<i class="fa fa-bed"></i>', 'active' => request()->is('hospital/mortuary*')]
            );
        });
    }
}
