<?php

namespace App\Http\Middleware;

use App\Utils\ModuleUtil;
use Closure;
use Illuminate\Support\Facades\Route;
use Menu;

class AdminSidebarMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            return $next($request);
        }

        Menu::create('admin-sidebar-menu', function ($menu) {
            $enabled_modules = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];

            $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];
            $pos_settings = !empty(session('business.pos_settings')) ? json_decode(session('business.pos_settings'), true) : [];

            $is_admin = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
            //Home
            //     $menu->url(action([\App\Http\Controllers\HomeController::class, 'index']), __('home.home'), ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            //     <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //     <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            //     <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
            //     <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
            //     <path d="M10 12h4v4h-4z"></path>
            //   </svg>', 'active' => request()->segment(1) == 'home'])->order(5);

            
                  
            $menu->url(action([\App\Http\Controllers\HomeController::class, 'index']), __('home.home'), ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
            <path d="M10 12h4v4h-4z" />
          </svg>', 'active' => request()->segment(1) == 'home'])->order(5);

            if (in_array('ai_analytics', $enabled_modules) && Route::has('dashboard.bi')) {
                $menu->url(route('dashboard.bi'), 'AI Analytics', ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
            <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
            <path d="M12 7l0 10" />
            <path d="M7 12l10 0" />
          </svg>', 'active' => request()->segment(2) == 'bi'])->order(6);
            }

            //User management dropdown
            if (auth()->user()->can('user.view') || auth()->user()->can('user.create') || auth()->user()->can('roles.view')) {
                $menu->dropdown(
                    __('user.user_management'),
                    function ($sub) {
                        if (auth()->user()->can('user.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ManageUserController::class, 'index']),
                                __('user.users'),
                                ['icon' => '', 'active' => request()->segment(1) == 'users']
                            );
                        }
                        if (auth()->user()->can('roles.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\RoleController::class, 'index']),
                                __('user.roles'),
                                ['icon' => '', 'active' => request()->segment(1) == 'roles']
                            );
                        }
                        if (auth()->user()->can('user.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\SalesCommissionAgentController::class, 'index']),
                                __('lang_v1.sales_commission_agents'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sales-commission-agents']
                            );
                        }
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                  </svg>', ]
                )->order(35);
            }

            //Contacts dropdown
            if (auth()->user()->can('supplier.view') || auth()->user()->can('customer.view') || auth()->user()->can('supplier.view_own') || auth()->user()->can('customer.view_own')) {
                $menu->dropdown(
                    __('contact.contacts'),
                    function ($sub) {
                        if (auth()->user()->can('supplier.view') || auth()->user()->can('supplier.view_own')) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'supplier']),
                                __('report.supplier'),
                                ['icon' => '', 'active' => request()->input('type') == 'supplier']
                            );
                        }
                        if (auth()->user()->can('customer.view') || auth()->user()->can('customer.view_own')) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'customer']),
                                __('report.customer'),
                                ['icon' => '', 'active' => request()->input('type') == 'customer']
                            );
                            $sub->url(
                                action([\App\Http\Controllers\CustomerGroupController::class, 'index']),
                                __('lang_v1.customer_groups'),
                                ['icon' => '', 'active' => request()->segment(1) == 'customer-group']
                            );
                        }
                        if (auth()->user()->can('supplier.create') || auth()->user()->can('customer.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactController::class, 'getImportContacts']),
                                __('lang_v1.import_contacts'),
                                ['icon' => '', 'active' => request()->segment(1) == 'contacts' && request()->segment(2) == 'import']
                            );
                        }

                        if (!empty(env('GOOGLE_MAP_API_KEY'))) {
                            $sub->url(
                                action([\App\Http\Controllers\ContactController::class, 'contactMap']),
                                __('lang_v1.map'),
                                ['icon' => 'fa fas fa-map-marker-alt', 'active' => request()->segment(1) == 'contacts' && request()->segment(2) == 'map']
                            );
                        }
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M20 6v12a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2z"></path>
                    <path d="M10 16h6"></path>
                    <path d="M13 11m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M4 8h3"></path>
                    <path d="M4 12h3"></path>
                    <path d="M4 16h3"></path>
                  </svg>', 'id' => 'tour_step4']
                )->order(14);
            }

            //Jobs dropdown
            if (in_array('jobs', $enabled_modules) && (auth()->user()->can('job.view') || auth()->user()->can('job.create'))) {
                $menu->dropdown(
                    __('job.jobs'),
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\JobController::class, 'index']),
                            __('job.all_your_jobs'),
                            ['icon' => '', 'active' => request()->segment(1) == 'jobs']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\JobCategoryController::class, 'index']),
                            __('job.category'),
                            ['icon' => '', 'active' => request()->segment(1) == 'job-categories']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\JobTemplateController::class, 'index']),
                            __('job.job_card') . ' Templates',
                            ['icon' => '', 'active' => request()->segment(1) == 'job-templates']
                        );
                    },
                    ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 5h10a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2z" />
                <path d="M13 13h4" />
                <path d="M13 9h4" />
                <path d="M13 17h4" />
                <path d="M5 9h-2v10a2 2 0 0 0 2 2h10v-2" />
              </svg>', 'active' => request()->segment(1) == 'jobs' || request()->segment(1) == 'job-categories' || request()->segment(1) == 'job-templates']
                )->order(20);
            }

            // Cooler Management
            if (
                in_array('cooler', $enabled_modules)
                && Route::has('cooler.assets.index')
                && (auth()->user()->can('cooler.asset.view') || auth()->user()->can('cooler.dealer.view') || auth()->user()->can('cooler.compliance.view') || auth()->user()->can('cooler.agent.portal'))
            ) {
                $isCoolerActive = in_array(request()->segment(2), ['assets', 'dealers', 'agreements', 'retrievals', 'compliance', 'reports', 'agent']) && request()->segment(1) == 'cooler';
                $menu->dropdown(
                    'Cooler Management',
                    function ($sub) {
                        // Agent portal — shown only to agents
                        if (auth()->user()->can('cooler.agent.portal') && Route::has('cooler.agent.dashboard')) {
                            $sub->url(route('cooler.agent.dashboard'), 'My Portal',
                                ['icon' => '', 'active' => request()->is('cooler/agent*')]);
                        }
                        if (auth()->user()->can('cooler.compliance.view') && Route::has('cooler.compliance.dashboard')) {
                            $sub->url(route('cooler.compliance.dashboard'), 'Dashboard',
                                ['icon' => '', 'active' => request()->is('cooler/compliance*')]);
                        }
                        if (auth()->user()->can('cooler.asset.view') && Route::has('cooler.assets.index')) {
                            $sub->url(route('cooler.assets.index'), 'Cooler Assets',
                                ['icon' => '', 'active' => request()->is('cooler/assets*')]);
                        }
                        if (auth()->user()->can('cooler.dealer.view') && Route::has('cooler.dealers.index')) {
                            $sub->url(route('cooler.dealers.index'), 'Customers',
                                ['icon' => '', 'active' => request()->is('cooler/dealers*')]);
                        }
                        if (auth()->user()->can('cooler.agreement.view') && Route::has('cooler.agreements.index')) {
                            $sub->url(route('cooler.agreements.index'), 'Agreements',
                                ['icon' => '', 'active' => request()->is('cooler/agreements*')]);
                        }
                        if (auth()->user()->can('cooler.retrieval.view') && Route::has('cooler.retrievals.index')) {
                            $sub->url(route('cooler.retrievals.index'), 'Retrievals',
                                ['icon' => '', 'active' => request()->is('cooler/retrievals*')]);
                        }
                        if (auth()->user()->can('cooler.report.view') && Route::has('cooler.reports')) {
                            $sub->url(route('cooler.reports'), 'Reports',
                                ['icon' => '', 'active' => request()->is('cooler/reports*')]);
                        }
                    },
                    ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2z" />
                <path d="M10 7h4" />
                <path d="M12 20v-13" />
                <path d="M5 10h14" />
              </svg>', 'active' => $isCoolerActive]
                )->order(15);
            }

            //eTIMS
            if (in_array('etims', $enabled_modules) && auth()->user()->can('access_etims_report')) {
                $menu->dropdown(
                    'eTIMS',
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\EtimsReportController::class, 'index']),
                            __('job.etims_invoices'),
                            ['icon' => '', 'active' => request()->segment(1) == 'etims-report' && request()->segment(2) != 'settings']
                        );
                        $sub->url(
                            route('etims.settings'),
                            'eTIMS Settings',
                            ['icon' => '', 'active' => request()->segment(1) == 'etims-settings']
                        );
                    },
                    ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M7 18a4.6 4.402 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" />
                <path d="M9 15l3 -3l3 3" />
                <path d="M12 12l0 9" />
              </svg>', 'active' => request()->segment(1) == 'etims-report' || request()->segment(1) == 'etims-settings']
                )->order(18);
            }

            //Products dropdown
            if (auth()->user()->can('product.view') || auth()->user()->can('product.create') ||
                auth()->user()->can('brand.view') || auth()->user()->can('unit.view') ||
                auth()->user()->can('category.view') || auth()->user()->can('brand.create') ||
                auth()->user()->can('unit.create') || auth()->user()->can('category.create')) {
                $menu->dropdown(
                    __('sale.products'),
                    function ($sub) {
                        if (auth()->user()->can('product.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\ProductController::class, 'index']),
                                __('lang_v1.list_products'),
                                ['icon' => '', 'active' => request()->segment(1) == 'products' && request()->segment(2) == '']
                            );
                        }

                        if (auth()->user()->can('product.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\ProductController::class, 'create']),
                                __('product.add_product'),
                                ['icon' => '', 'active' => request()->segment(1) == 'products' && request()->segment(2) == 'create']
                            );
                        }
                        if (auth()->user()->can('product.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellingPriceGroupController::class, 'updateProductPrice']),
                                __('lang_v1.update_product_price'),
                                ['icon' => '', 'active' => request()->segment(1) == 'update-product-price']
                            );
                        }
                        if (auth()->user()->can('product.view')) {
                            $sub->url(
                                action([\App\Http\Controllers\LabelsController::class, 'show']),
                                __('barcode.print_labels'),
                                ['icon' => '', 'active' => request()->segment(1) == 'labels' && request()->segment(2) == 'show']
                            );
                        }
                        if (auth()->user()->can('product.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\VariationTemplateController::class, 'index']),
                                __('product.variations'),
                                ['icon' => '', 'active' => request()->segment(1) == 'variation-templates']
                            );
                            $sub->url(
                                action([\App\Http\Controllers\ImportProductsController::class, 'index']),
                                __('product.import_products'),
                                ['icon' => '', 'active' => request()->segment(1) == 'import-products']
                            );
                        }
                        if (auth()->user()->can('product.opening_stock')) {
                            $sub->url(
                                action([\App\Http\Controllers\ImportOpeningStockController::class, 'index']),
                                __('lang_v1.import_opening_stock'),
                                ['icon' => '', 'active' => request()->segment(1) == 'import-opening-stock']
                            );
                        }
                        if (auth()->user()->can('product.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellingPriceGroupController::class, 'index']),
                                __('lang_v1.selling_price_group'),
                                ['icon' => '', 'active' => request()->segment(1) == 'selling-price-group']
                            );
                        }
                        if (auth()->user()->can('unit.view') || auth()->user()->can('unit.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\UnitController::class, 'index']),
                                __('unit.units'),
                                ['icon' => '', 'active' => request()->segment(1) == 'units']
                            );
                        }
                        if (auth()->user()->can('category.view') || auth()->user()->can('category.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=product',
                                __('category.categories'),
                                ['icon' => '', 'active' => request()->segment(1) == 'taxonomies' && request()->get('type') == 'product']
                            );
                        }
                        if (auth()->user()->can('brand.view') || auth()->user()->can('brand.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\BrandController::class, 'index']),
                                __('brand.brands'),
                                ['icon' => '', 'active' => request()->segment(1) == 'brands']
                            );
                        }

                        $sub->url(
                            action([\App\Http\Controllers\WarrantyController::class, 'index']),
                            __('lang_v1.warranties'),
                            ['icon' => '', 'active' => request()->segment(1) == 'warranties']
                        );
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 3l8 4.5v9l-8 4.5l-8 -4.5v-9l8 -4.5"></path>
                    <path d="M12 12l8 -4.5"></path>
                    <path d="M8.2 9.8l7.6 -4.6"></path>
                    <path d="M12 12v9"></path>
                    <path d="M12 12l-8 -4.5"></path>
                  </svg>', 'id' => 'tour_step5']
                )->order(26);
            }

            //Purchase dropdown
            if (in_array('purchases', $enabled_modules) && (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create') || auth()->user()->can('purchase.update'))) {
                $menu->dropdown(
                    __('purchase.purchases'),
                    function ($sub) use ($common_settings) {
                        if (!empty($common_settings['enable_purchase_requisition']) && (auth()->user()->can('purchase_requisition.view_all') || auth()->user()->can('purchase_requisition.view_own'))) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseRequisitionController::class, 'index']),
                                __('lang_v1.purchase_requisition'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchase-requisition']
                            );
                        }

                        if (!empty($common_settings['enable_purchase_order']) && (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own'))) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseOrderController::class, 'index']),
                                __('lang_v1.purchase_order'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchase-order']
                            );
                        }
                        if (auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase')) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseController::class, 'index']),
                                __('purchase.list_purchase'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == null && empty(request()->get('payment_status'))]
                            );
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseController::class, 'index'], ['payment_status' => 'due']),
                                __('lang_v1.purchase_payment_dues'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchases' && request()->get('payment_status') == 'due']
                            );
                        }
                        if (auth()->user()->can('purchase.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseController::class, 'create']),
                                __('purchase.add_purchase'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == 'create']
                            );
                            $sub->url(
                                route('purchases.drafts'),
                                'Drafts',
                                ['icon' => '', 'active' => request()->segment(1) == 'purchases' && request()->segment(2) == 'drafts']
                            );
                        }
                        if (auth()->user()->can('purchase.update')) {
                            $sub->url(
                                action([\App\Http\Controllers\PurchaseReturnController::class, 'index']),
                                __('lang_v1.list_purchase_return'),
                                ['icon' => '', 'active' => request()->segment(1) == 'purchase-return']
                            );
                        }
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 3v12"></path>
                    <path d="M16 11l-4 4l-4 -4"></path>
                    <path d="M3 12a9 9 0 0 0 18 0"></path>
                  </svg>', 'id' => 'tour_step6']
                )->order(27);
            }
            //Sell dropdown
            if ($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping', 'access_sell_return', 'direct_sell.view', 'direct_sell.update', 'access_own_sell_return'])) {
                $menu->dropdown(
                    __('sale.sale'),
                    function ($sub) use ($enabled_modules, $is_admin, $pos_settings) {
                        if (!empty($pos_settings['enable_sales_order']) && ($is_admin || auth()->user()->hasAnyPermission(['so.view_own', 'so.view_all', 'so.create']))) {
                            $sub->url(
                                action([\App\Http\Controllers\SalesOrderController::class, 'index']),
                                __('lang_v1.sales_order'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sales-order']
                            );
                        }

                        if ($is_admin || auth()->user()->hasAnyPermission(['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view', 'view_own_sell_only', 'view_commission_agent_sell', 'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'index']),
                                __('lang_v1.all_sales'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == null && empty(request()->get('payment_status')) && empty(request()->get('only_pending_shipments'))]
                            );
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'index'], ['payment_status' => 'due']),
                                __('lang_v1.sales_payment_dues'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->get('payment_status') == 'due']
                            );
                        }
                        if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'create']),
                                __('sale.add_sale'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'create' && empty(request()->get('status'))]
                            );
                        }
                        if (auth()->user()->can('sell.create')) {
                            if (in_array('pos_sale', $enabled_modules)) {
                                if (auth()->user()->can('sell.view')) {
                                    $sub->url(
                                        action([\App\Http\Controllers\SellPosController::class, 'index']),
                                        __('sale.list_pos'),
                                        ['icon' => '', 'active' => request()->segment(1) == 'pos' && request()->segment(2) == null]
                                    );
                                }

                                $sub->url(
                                    action([\App\Http\Controllers\SellPosController::class, 'create']),
                                    __('sale.pos_sale'),
                                    ['icon' => '', 'active' => request()->segment(1) == 'pos' && request()->segment(2) == 'create']
                                );
                            }
                        }

                        if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'create'], ['status' => 'draft']),
                                __('lang_v1.add_draft'),
                                ['icon' => '', 'active' => request()->get('status') == 'draft']
                            );
                        }
                        if (in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['draft.view_all', 'draft.view_own']))) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'getDrafts']),
                                __('lang_v1.list_drafts'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'drafts']
                            );
                        }
                        if (in_array('add_sale', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'create'], ['status' => 'quotation']),
                                __('lang_v1.add_quotation'),
                                ['icon' => '', 'active' => request()->get('status') == 'quotation']
                            );
                        }
                        if (in_array('add_sale', $enabled_modules) && ($is_admin || auth()->user()->hasAnyPermission(['quotation.view_all', 'quotation.view_own']))) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'getQuotations']),
                                __('lang_v1.list_quotations'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->segment(2) == 'quotations']
                            );
                        }

                        if (auth()->user()->can('access_sell_return') || auth()->user()->can('access_own_sell_return')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellReturnController::class, 'index']),
                                __('lang_v1.list_sell_return'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sell-return' && request()->segment(2) == null]
                            );
                        }

                        if ($is_admin || auth()->user()->hasAnyPermission(['access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'])) {
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'shipments']),
                                __('lang_v1.shipments'),
                                ['icon' => '', 'active' => request()->segment(1) == 'shipments']
                            );
                            $sub->url(
                                action([\App\Http\Controllers\SellController::class, 'index'], ['only_pending_shipments' => 'true']),
                                __('lang_v1.pending_shipments'),
                                ['icon' => '', 'active' => request()->segment(1) == 'sells' && request()->get('only_pending_shipments') == 'true']
                            );
                        }

                        if (auth()->user()->can('discount.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\DiscountController::class, 'index']),
                                __('lang_v1.discounts'),
                                ['icon' => '', 'active' => request()->segment(1) == 'discount']
                            );
                        }
                        if (in_array('subscription', $enabled_modules) && auth()->user()->can('direct_sell.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\SellPosController::class, 'listSubscriptions']),
                                __('lang_v1.subscriptions'),
                                ['icon' => '', 'active' => request()->segment(1) == 'subscriptions']
                            );
                        }

                        if (auth()->user()->can('sell.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\ImportSalesController::class, 'index']),
                                __('lang_v1.import_sales'),
                                ['icon' => '', 'active' => request()->segment(1) == 'import-sales']
                            );
                        }
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 15v-12"></path>
                    <path d="M16 7l-4 -4l-4 4"></path>
                    <path d="M3 12a9 9 0 0 0 18 0"></path>
                  </svg>', 'id' => 'tour_step7']
                )->order(29);
            }

            // ── Customer Orders (standalone) ───────────────────────────────
            // Shop orders menu: all logged-in staff (not admin-only)
            if (in_array('customer_orders', $enabled_modules) && auth()->check()) {
                $menu->dropdown(
                    __('Customer Orders'),
                    function ($sub) use ($is_admin) {
                        $sub->url(
                            route('orders.index'),
                            __('All Orders'),
                            ['icon' => '', 'active' => request()->segment(1) == 'pos-customer-orders' && request()->segment(2) == null]
                        );
                        $sub->url(
                            route('customer_order.links'),
                            __('Order Links'),
                            ['icon' => '', 'active' => request()->segment(1) == 'customer-order-links']
                        );
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 15l6 -6"></path>
                    <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"></path>
                    <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"></path>
                  </svg>']
                )->order(16);
            }

            //Stock transfer dropdown
            if (in_array('stock_transfers', $enabled_modules) && (auth()->user()->can('stock_transfer.view') || auth()->user()->can('stock_transfer.create') || auth()->user()->can('stock_transfer.view_own'))) {
                $menu->dropdown(
                    __('lang_v1.stock_transfers'),
                    function ($sub) {
                        if (auth()->user()->can('stock_transfer.view') || auth()->user()->can('stock_transfer.view_own')) {
                            $sub->url(
                                action([\App\Http\Controllers\StockTransferController::class, 'index']),
                                __('lang_v1.list_stock_transfers'),
                                ['icon' => '', 'active' => request()->segment(1) == 'stock-transfers' && request()->segment(2) == null]
                            );
                        }
                        if (auth()->user()->can('stock_transfer.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\StockTransferController::class, 'create']),
                                __('lang_v1.add_stock_transfer'),
                                ['icon' => '', 'active' => request()->segment(1) == 'stock-transfers' && request()->segment(2) == 'create']
                            );
                        }
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
                    <path d="M3 9l4 0"></path>
                  </svg>']
                )->order(33);
            }

            //stock adjustment dropdown
            if (in_array('stock_adjustment', $enabled_modules) && (auth()->user()->can('stock_adjustment.view') || auth()->user()->can('stock_adjustment.create') || auth()->user()->can('view_own_stock_adjustment'))) {
                $menu->dropdown(
                    __('stock_adjustment.stock_adjustment'),
                    function ($sub) {
                        if (auth()->user()->can('stock_adjustment.view')  || auth()->user()->can('view_own_stock_adjustment')) {
                            $sub->url(
                                action([\App\Http\Controllers\StockAdjustmentController::class, 'index']),
                                __('stock_adjustment.list'),
                                ['icon' => '', 'active' => request()->segment(1) == 'stock-adjustments' && request()->segment(2) == null]
                            );
                        }
                        if (auth()->user()->can('stock_adjustment.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\StockAdjustmentController::class, 'create']),
                                __('stock_adjustment.add'),
                                ['icon' => '', 'active' => request()->segment(1) == 'stock-adjustments' && request()->segment(2) == 'create']
                            );
                        }
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"></path>
                    <path d="M4 6v6a8 3 0 0 0 16 0v-6"></path>
                    <path d="M4 12v6a8 3 0 0 0 16 0v-6"></path>
                  </svg>']
                )->order(32);
            }

            // Stocktake menu
            if (in_array('stocktake', $enabled_modules) && auth()->user()->can('stocktake.view')) {
                $menu->url(
                    action([\App\Http\Controllers\StocktakeController::class, 'index']),
                    __('Stocktake'),
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                    <path d="M9 14l2 2l4 -4"></path>
                  </svg>', 'active' => request()->segment(1) == 'stocktake']
                )->order(34);
            }

            //Expense dropdown
            if (in_array('expenses', $enabled_modules) && (auth()->user()->can('all_expense.access') || auth()->user()->can('view_own_expense'))) {
                $menu->dropdown(
                    __('expense.expenses'),
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\ExpenseController::class, 'index']),
                            __('lang_v1.list_expenses'),
                            ['icon' => '', 'active' => request()->segment(1) == 'expenses' || request()->segment(1) == 'import-expense' && request()->segment(2) == null]
                        );

                        if (auth()->user()->can('expense.add')) {
                            $sub->url(
                                action([\App\Http\Controllers\ExpenseController::class, 'create']),
                                __('expense.add_expense'),
                                ['icon' => '', 'active' => request()->segment(1) == 'expenses' && request()->segment(2) == 'create']
                            );
                        }

                        if (auth()->user()->can('expense.add') || auth()->user()->can('expense.edit')) {
                            $sub->url(
                                action([\App\Http\Controllers\ExpenseCategoryController::class, 'index']),
                                __('expense.expense_categories'),
                                ['icon' => '', 'active' => request()->segment(1) == 'expense-categories']
                            );
                        }
                    },
                    ['icon' => ' <svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2"></path>
                    <path d="M14.8 8a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"></path>
                    <path d="M12 6v10"></path>
                  </svg>']
                )->order(19);
            }
            //Accounts dropdown
            if (auth()->user()->can('account.access') && in_array('account', $enabled_modules)) {
                $menu->dropdown(
                    __('lang_v1.payment_accounts'),
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\AccountController::class, 'index']),
                            __('account.list_accounts'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'account']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\AccountReportsController::class, 'balanceSheet']),
                            __('account.balance_sheet'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'balance-sheet']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\AccountReportsController::class, 'trialBalance']),
                            __('account.trial_balance'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'trial-balance']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\AccountController::class, 'cashFlow']),
                            __('lang_v1.cash_flow'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'cash-flow']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\AccountReportsController::class, 'paymentAccountReport']),
                            __('account.payment_account_report'),
                            ['icon' => '', 'active' => request()->segment(1) == 'account' && request()->segment(2) == 'payment-account-report']
                        );
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z"></path>
                    <path d="M3 10l18 0"></path>
                    <path d="M7 15l.01 0"></path>
                    <path d="M11 15l2 0"></path>
                  </svg>']
                )->order(25);
            }

            // IP Access Control — admin only, and only if the module is enabled
            $businessId = session('business.id');
            $ipModuleEnabled = $businessId && \App\Models\BusinessFeatureSetting::isEnabled('ip_restriction', $businessId);
            if ($ipModuleEnabled && Route::has('ip-access.settings') && ($is_admin || auth()->user()->can('ip_access.access'))) {
                $menu->dropdown(
                    'IP Access Control',
                    function ($sub) {
                        if (Route::has('ip-access.settings')) {
                            $sub->url(
                                route('ip-access.settings'),
                                'Whitelist & Toggle',
                                ['icon' => '', 'active' => request()->segment(1) == 'ip-access' && request()->segment(2) == 'settings']
                            );
                        }
                        if (Route::has('ip-access.logs')) {
                            $sub->url(
                                route('ip-access.logs'),
                                'Access Logs',
                                ['icon' => '', 'active' => request()->segment(1) == 'ip-access' && request()->segment(2) == 'logs']
                            );
                        }
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 18a12 12 0 0 1 -8.5 -18a12 12 0 0 0 8.5 -3"></path>
                    <path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                    <path d="M12 12l0 2.5"></path>
                    </svg>', 'active' => request()->segment(1) == 'ip-access']
                );
            }

            // M-Pesa Payments menu
            if ($is_admin || auth()->user()->can('mpesa.access')) {
                $menu->dropdown(
                    __('lang_v1.mpesa_settings'),
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\MpesaController::class, 'settings']),
                            __('lang_v1.mpesa_configuration'),
                            ['icon' => '', 'active' => request()->segment(1) == 'mpesa' && request()->segment(2) == 'settings']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\MpesaController::class, 'transactions']),
                            __('lang_v1.mpesa_transactions'),
                            ['icon' => '', 'active' => request()->segment(1) == 'mpesa' && request()->segment(2) == 'transactions']
                        );
                        $sub->url(
                            action([\App\Http\Controllers\MpesaController::class, 'c2bPayments']),
                            __('lang_v1.mpesa_c2b_payments'),
                            ['icon' => '', 'active' => request()->segment(1) == 'mpesa' && request()->segment(2) == 'c2b-payments']
                        );
                        $sub->url(
                            '#',
                            __('lang_v1.check_mpesa_balance'),
                            ['icon' => '', 'id' => 'check_mpesa_balance_btn']
                        );
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2"></path>
                    <path d="M12 3v3m0 12v3"></path>
                  </svg>']
                )->order(22);
            }

            // Pesapal menu — only if module enabled (routes registered)
            if (
                Route::has('pesapal.settings')
                && ($is_admin || auth()->user()->can('pesapal.manage_settings') || auth()->user()->can('pesapal.view_transactions'))
            ) {
                $menu->dropdown(
                    'Pesapal',
                    function ($sub) use ($is_admin) {
                        if (($is_admin || auth()->user()->can('pesapal.manage_settings')) && Route::has('pesapal.settings')) {
                            $sub->url(route('pesapal.settings'), 'Settings',
                                ['icon' => '', 'active' => request()->segment(1) == 'pesapal' && request()->segment(2) == 'settings']);
                        }
                        if (($is_admin || auth()->user()->can('pesapal.view_transactions')) && Route::has('pesapal.transactions')) {
                            $sub->url(route('pesapal.transactions'), 'Transactions',
                                ['icon' => '', 'active' => request()->segment(1) == 'pesapal' && request()->segment(2) == 'transactions']);
                        }
                    },
                    ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <rect x="3" y="5" width="18" height="14" rx="3"/>
                    <path d="M3 10h18"/><path d="M7 15h2"/>
                  </svg>', 'active' => request()->segment(1) == 'pesapal']
                )->order(23);
            }

            // MegaPay menu — only if module enabled
            if (
                Route::has('megapay.settings')
                && ($is_admin || auth()->user()->can('megapay.manage_settings') || auth()->user()->can('megapay.view_transactions'))
            ) {
                $menu->dropdown(
                    'MegaPay',
                    function ($sub) use ($is_admin) {
                        if (($is_admin || auth()->user()->can('megapay.manage_settings')) && Route::has('megapay.settings')) {
                            $sub->url(route('megapay.settings'), 'Settings',
                                ['icon' => '', 'active' => request()->segment(1) == 'megapay' && request()->segment(2) == 'settings']);
                        }
                        if (($is_admin || auth()->user()->can('megapay.view_transactions')) && Route::has('megapay.transactions')) {
                            $sub->url(route('megapay.transactions'), 'Transactions',
                                ['icon' => '', 'active' => request()->segment(1) == 'megapay' && request()->segment(2) == 'transactions']);
                        }
                        if (($is_admin || auth()->user()->can('megapay.view_transactions')) && Route::has('megapay.quick-pay')) {
                            $sub->url(route('megapay.quick-pay'), 'Quick Pay Checkout',
                                ['icon' => '', 'active' => request()->segment(1) == 'megapay' && request()->segment(2) == 'quick-pay']);
                        }
                    },
                    ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <rect x="3" y="5" width="18" height="14" rx="3"/>
                    <path d="M3 10h18"/><path d="M7 15h2"/>
                  </svg>', 'active' => request()->segment(1) == 'megapay']
                )->order(24);
            }

            // KCB Buni menu — only if module enabled
            if (
                Route::has('kcb-buni.settings')
                && ($is_admin || auth()->user()->can('kcb_buni.manage_settings') || auth()->user()->can('kcb_buni.view_transactions'))
            ) {
                $menu->dropdown(
                    'KCB Buni',
                    function ($sub) use ($is_admin) {
                        if (($is_admin || auth()->user()->can('kcb_buni.manage_settings')) && Route::has('kcb-buni.settings')) {
                            $sub->url(route('kcb-buni.settings'), 'Settings',
                                ['icon' => '', 'active' => request()->segment(1) == 'kcb-buni' && request()->segment(2) == 'settings']);
                        }
                        if (($is_admin || auth()->user()->can('kcb_buni.view_transactions')) && Route::has('kcb-buni.transactions')) {
                            $sub->url(route('kcb-buni.transactions'), 'Transactions',
                                ['icon' => '', 'active' => request()->segment(1) == 'kcb-buni' && request()->segment(2) == 'transactions']);
                        }
                    },
                    ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 3v18" />
                    <path d="M16 7l-4 -4l-4 4" />
                    <path d="M16 17l-4 4l-4 -4" />
                  </svg>', 'active' => request()->segment(1) == 'kcb-buni']
                )->order(24);
            }

            //Reports dropdown
            if (auth()->user()->can('purchase_n_sell_report.view') || auth()->user()->can('contacts_report.view')
                || auth()->user()->can('stock_report.view') || auth()->user()->can('tax_report.view')
                || auth()->user()->can('trending_product_report.view') || auth()->user()->can('sales_representative.view') || auth()->user()->can('register_report.view')
                || auth()->user()->can('expense_report.view')) {
                $menu->dropdown(
                    __("report.reports"),
                    function ($sub) use ($enabled_modules, $is_admin) {

                        // Hub home (plain-language report groups)
                        $sub->url(route('reports.hub'), 'Reports home', [
                            'icon' => '',
                            'active' => request()->segment(1) == 'reports' && (request()->segment(2) == null || request()->segment(2) == 'hub'),
                        ]);

                        // --- Daily ---
                        if (auth()->user()->can("profit_loss_report.view")) {
                            $sub->dropdown('Daily', function ($s) {
                                $s->url(route("reports.day_close"), __("Close the day"), ["icon" => "", "active" => request()->segment(2) == "day-close"]);
                                $s->url(route("reports.daily_summary"), __("Daily Summary"), ["icon" => "", "active" => request()->segment(2) == "daily-summary"]);
                                $s->url(route("reports.daily_reconciliation"), __("Daily Reconciliation"), ["icon" => "", "active" => request()->segment(2) == "daily-reconciliation"]);
                                $s->url(route("reports.lost_sales"), __("Lost Sales"), ["icon" => "", "active" => request()->segment(2) == "lost-sales"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getProfitLoss"]), __("report.profit_loss"), ["icon" => "", "active" => request()->segment(2) == "profit-loss"]);
                            }, ["icon" => ""]);
                        }

                        // --- Finance & control (advanced pack) ---
                        if (auth()->user()->can("profit_loss_report.view") || auth()->user()->can("account.access") || auth()->user()->can("purchase_n_sell_report.view") || auth()->user()->can("stock_report.view")) {
                            $sub->dropdown('Finance & control', function ($s) {
                                if (auth()->user()->can("profit_loss_report.view") || auth()->user()->can("account.access") || auth()->user()->can("purchase_n_sell_report.view")) {
                                    $s->url(route("reports.financial_statements"), "Financial statements", ["icon" => "", "active" => request()->segment(2) == "financial-statements"]);
                                    $s->url(route("reports.bank_mpesa_recon"), "Bank / M-Pesa recon", ["icon" => "", "active" => request()->segment(2) == "bank-mpesa-recon"]);
                                    $s->url(route("reports.multi_period"), "Multi-period dashboard", ["icon" => "", "active" => request()->segment(2) == "multi-period"]);
                                    $s->url(route("reports.discount_abuse"), "Discount abuse", ["icon" => "", "active" => request()->segment(2) == "discount-abuse"]);
                                }
                                if (auth()->user()->can("stock_report.view")) {
                                    $s->url(route("reports.fefo_compliance"), "FEFO / batch log", ["icon" => "", "active" => request()->segment(2) == "fefo-compliance"]);
                                    $s->url(route("reports.inventory_valuation"), "Inventory valuation", ["icon" => "", "active" => request()->segment(2) == "inventory-valuation"]);
                                }
                                if (auth()->user()->can("sell.view") || auth()->user()->can("account.access") || auth()->user()->can("business_settings.access") || auth()->user()->can("user.view")) {
                                    $s->url(route("reports.audit_export"), "Audit log export", ["icon" => "", "active" => request()->segment(2) == "audit-export"]);
                                }
                            }, ["icon" => ""]);
                        }

                        // --- Sales ---
                        $sub->dropdown('Sales', function ($s) use ($enabled_modules) {
                            if ((in_array("purchases", $enabled_modules) || in_array("add_sale", $enabled_modules) || in_array("pos_sale", $enabled_modules)) && auth()->user()->can("purchase_n_sell_report.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getPurchaseSell"]), __("report.purchase_sell_report"), ["icon" => "", "active" => request()->segment(2) == "purchase-sell"]);
                            }
                            if (auth()->user()->can("tax_report.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getTaxReport"]), __("report.tax_report"), ["icon" => "", "active" => request()->segment(2) == "tax-report"]);
                            }
                            if (auth()->user()->can("customer_report.view") || auth()->user()->can("contacts_report.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getCustomerReport"]), __("report.customer_report"), ["icon" => "", "active" => request()->segment(2) == "customer-report"]);
                            }
                            if (auth()->user()->can("supplier_report.view") || auth()->user()->can("contacts_report.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getSupplierReport"]), __("report.supplier_report"), ["icon" => "", "active" => request()->segment(2) == "supplier-report"]);
                            }
                            if (auth()->user()->can("contacts_report.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getCustomerGroup"]), __("lang_v1.customer_groups_report"), ["icon" => "", "active" => request()->segment(2) == "customer-group"]);
                            }
                            if (auth()->user()->can("followups.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getFollowupReport"]), __("lang_v1.followup_report"), ["icon" => "", "active" => request()->segment(2) == "followup-report"]);
                            }
                            if (auth()->check()) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getOrdersReport"]), __("lang_v1.pos_orders_report"), ["icon" => "", "active" => request()->segment(2) == "orders-report"]);
                            }
                            if (auth()->user()->can("tax_report.view") && !empty(config("constants.enable_gst_report_india"))) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "gstSalesReport"]), __("lang_v1.gst_sales_report"), ["icon" => "", "active" => request()->segment(2) == "gst-sales-report"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "gstPurchaseReport"]), __("lang_v1.gst_purchase_report"), ["icon" => "", "active" => request()->segment(2) == "gst-purchase-report"]);
                            }
                            if (config("constants.show_report_606") == true) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "purchaseReport"]), "Report 606 (" . __("lang_v1.purchase") . ")", ["icon" => "", "active" => request()->segment(2) == "purchase-report"]);
                            }
                            if (config("constants.show_report_607") == true) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "saleReport"]), "Report 607 (" . __("business.sale") . ")", ["icon" => "", "active" => request()->segment(2) == "sale-report"]);
                            }
                        }, ["icon" => ""]);

                        // --- Stock ---
                        if (auth()->user()->can("stock_report.view")) {
                            $sub->dropdown('Stock', function ($s) use ($enabled_modules) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getStockReport"]), __("Stock on hand"), ["icon" => "", "active" => request()->segment(2) == "stock-report"]);
                                if (session("business.enable_product_expiry") == 1) {
                                    $s->url(action([\App\Http\Controllers\ReportController::class, "getStockExpiryReport"]), __("Expiring medicines"), ["icon" => "", "active" => request()->segment(2) == "stock-expiry"]);
                                    $s->url(route("reports.expiry_smart"), __("Expiry 30/60/90"), ["icon" => "", "active" => request()->segment(2) == "expiry-smart"]);
                                }
                                $s->url(route("reports.ledger_gap"), __("Stock vs purchase ledger"), ["icon" => "", "active" => request()->segment(2) == "ledger-gap"]);
                                $s->url(route("reports.reorder_list"), __("Reorder list"), ["icon" => "", "active" => request()->segment(2) == "reorder-list"]);
                                if (in_array("stock_adjustment", $enabled_modules)) {
                                    $s->url(action([\App\Http\Controllers\ReportController::class, "getStockAdjustmentReport"]), __("Stock count fixes"), ["icon" => "", "active" => request()->segment(2) == "stock-adjustment-report"]);
                                }
                                if (session("business.enable_lot_number") == 1) {
                                    $s->url(action([\App\Http\Controllers\ReportController::class, "getLotReport"]), __("lang_v1.lot_report"), ["icon" => "", "active" => request()->segment(2) == "lot-report"]);
                                }
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getLowStockVelocityReport"]), __("Running out soon"), ["icon" => "", "active" => request()->segment(2) == "low-stock-velocity"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getDeadStockReport"]), __("Not selling"), ["icon" => "", "active" => request()->segment(2) == "dead-stock"]);
                            }, ["icon" => ""]);
                        }

                        // --- Products ---
                        if (auth()->user()->can("purchase_n_sell_report.view")) {
                            $sub->dropdown('Products', function ($s) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "itemsReport"]), __("lang_v1.items_report"), ["icon" => "", "active" => request()->segment(2) == "items-report"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getproductPurchaseReport"]), __("lang_v1.product_purchase_report"), ["icon" => "", "active" => request()->segment(2) == "product-purchase-report"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getproductSellReport"]), __("lang_v1.product_sell_report"), ["icon" => "", "active" => request()->segment(2) == "product-sell-report"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getDailyProductProfitReport"]), "Daily Product Profit", ["icon" => "", "active" => request()->segment(2) == "daily-product-profit"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "purchasePaymentReport"]), __("lang_v1.purchase_payment_report"), ["icon" => "", "active" => request()->segment(2) == "purchase-payment-report"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "sellPaymentReport"]), __("lang_v1.sell_payment_report"), ["icon" => "", "active" => request()->segment(2) == "sell-payment-report"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getPurchasePriceVarianceReport"]), "PPV Report", ["icon" => "", "active" => request()->segment(2) == "purchase-price-variance"]);
                                if (auth()->user()->can("trending_product_report.view")) {
                                    $s->url(action([\App\Http\Controllers\ReportController::class, "getTrendingProducts"]), __("report.trending_products"), ["icon" => "", "active" => request()->segment(2) == "trending-products"]);
                                }
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getFastMoversReport"]), "Fast Movers (Top 100)", ["icon" => "", "active" => request()->segment(2) == "fast-movers"]);
                            }, ["icon" => ""]);
                        }

                        // --- Finance & Operations ---
                        $sub->dropdown('Finance & Operations', function ($s) use ($enabled_modules, $is_admin) {
                            if (in_array("expenses", $enabled_modules) && auth()->user()->can("expense_report.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getExpenseReport"]), __("report.expense_report"), ["icon" => "", "active" => request()->segment(2) == "expense-report"]);
                            }
                            if (auth()->user()->can("register_report.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getRegisterReport"]), __("report.register_report"), ["icon" => "", "active" => request()->segment(2) == "register-report"]);
                            }
                            if (auth()->user()->can("sales_representative.view")) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getSalesRepresentativeReport"]), __("report.sales_representative"), ["icon" => "", "active" => request()->segment(2) == "sales-representative-report"]);
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getSellerDailyReport"]), "Seller Daily Report", ["icon" => "", "active" => request()->segment(2) == "seller-daily-report"]);
                            }
                            if (auth()->user()->can("purchase_n_sell_report.view") && in_array("tables", $enabled_modules)) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getTableReport"]), __("restaurant.table_report"), ["icon" => "", "active" => request()->segment(2) == "table-report"]);
                            }
                            if (auth()->user()->can("sales_representative.view") && in_array("service_staff", $enabled_modules)) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "getServiceStaffReport"]), __("restaurant.service_staff_report"), ["icon" => "", "active" => request()->segment(2) == "service-staff-report"]);
                            }
                            if ($is_admin) {
                                $s->url(action([\App\Http\Controllers\ReportController::class, "activityLog"]), __("lang_v1.activity_log"), ["icon" => "", "active" => request()->segment(2) == "activity-log"]);
                            }
                        }, ["icon" => ""]);
                    },
                    ["icon" => "<svg aria-hidden=\"true\" class=\"tw-size-5 tw-shrink-0\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\">
                    <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>
                    <path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"></path>
                    <path d=\"M8 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h5.697\"></path>
                    <path d=\"M18 14v4h4\"></path>
                    <path d=\"M18 11v-4a2 2 0 0 0 -2 -2h-2\"></path>
                    <path d=\"M8 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z\"></path>
                    <path d=\"M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0\"></path>
                    <path d=\"M8 11h4\"></path>
                    <path d=\"M8 15h3\"></path>
                  </svg>", "id" => "tour_step8"]
                )->order(28);
            }

            //Hospital dropdown
            if (in_array('hospital_module', $enabled_modules)) $menu->dropdown(
                'Hospital',
                function ($sub) {
                    $seg1 = request()->segment(1) == 'hospital';
                    $seg2 = request()->segment(2);
                    $loc  = request()->get('location');

                    $links = [
                        // ── Overview ────────────────────────────────────────
                        ['hospital.flow',               '⚡ Patient Flow Board', 'flow',          null],
                        ['hospital.patients.index',     '👤 Patients',           'patients',      null],
                        ['hospital.index',              '📅 Appointments',       null,            null],

                        // ── Outpatient Journey ──────────────────────────────
                        ['hospital.queue.index',        '📋 All Queues',         'queue',         null],
                        // Stage-filtered queue links (pass ?location= param)
                        ['hospital.queue.index',        '🌡️  Triage',             'queue',         'triage'],
                        ['hospital.queue.index',        '🩺 Consultation',        'queue',         'consultation'],
                        ['hospital.queue.index',        '🔬 Laboratory',          'queue',         'laboratory'],
                        ['hospital.queue.index',        '💊 Pharmacy Dispensing', 'queue',         'pharmacy'],
                        ['hospital.queue.index',        '💰 Billing',             'queue',         'billing'],

                        // ── Departments ─────────────────────────────────────
                        ['hospital.lab.index',          '🔬 Lab Management',     'lab',           null],
                        ['hospital.pharmacy.index',     '💊 Pharmacy',           'pharmacy',      null],
                        ['hospital.billing.index',      '💰 Billing',            'billing',       null],
                        ['hospital.radiography.index',  '📷 Radiology (X-Ray)',  'radiography',   null],
                        ['hospital.theatre.index',      '🏥 Theatre (Surgery)',  'theatre',       null],
                        ['hospital.physio.index',       '🦴 Physiotherapy',      'physio',        null],
                        ['hospital.maternity.index',    '🤰 Maternity & ANC',    'maternity',     null],
                        ['hospital.mortuary.index',     '⚰️  Mortuary',           'mortuary',      null],

                        // ── Inpatient ───────────────────────────────────────
                        ['hospital.inpatient.index',    '🛏️  Inpatient (IPD)',    'inpatient',     null],

                        // ── Reports & Assets ────────────────────────────────
                        ['hospital.reports.moh705Index','📊 MoH Reports',        'reports',       null],
                        ['hospital.assets.index',       '🔧 Hospital Assets',    'assets',        null],
                    ];

                    foreach ($links as [$name, $label, $seg2check, $locParam]) {
                        try {
                            $params = $locParam ? ['location' => $locParam] : [];
                            $url    = route($name, $params);
                            $active = $seg1
                                && request()->segment(2) == $seg2check
                                && ($locParam ? $loc == $locParam : true);
                            $sub->url($url, $label, ['icon' => '', 'active' => $active]);
                        } catch (\Exception $e) {
                            // Route not in cache yet — skip silently
                        }
                    }
                },
                ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M3 21h18" />
                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" />
                <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
                <path d="M10 9h4" />
                <path d="M12 7v4" />
              </svg>', 'active' => request()->segment(1) == 'hospital']
            )->order(58);

            //Backup menu
            if (auth()->user()->can('backup')) {
                $menu->url(action([\App\Http\Controllers\BackUpController::class, 'index']), __('lang_v1.backup'), ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 18.004h-5.343c-2.572 -.004 -4.657 -2.011 -4.657 -4.487c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.38 0 2.57 .811 3.128 1.986"></path>
                <path d="M19 22v-6"></path>
                <path d="M22 19l-3 -3l-3 3"></path>
              </svg>', 'active' => request()->segment(1) == 'backup'])->order(11);
            }

            // Cloud Sync
            if (in_array('cloud_sync', $enabled_modules)) {
                $menu->url(
                    action([\App\Http\Controllers\CloudSyncController::class, 'dashboard']),
                    'Cloud Sync',
                    ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" />
                <path d="M9 15l3 -3l3 3" />
                <path d="M12 12l0 9" />
              </svg>', 'active' => request()->segment(1) == 'sync']
                )->order(13);
            }

            //Modules menu
            if (auth()->user()->can('manage_modules')) {
                $menu->url(action([\App\Http\Controllers\Install\ModulesController::class, 'index']), __('lang_v1.modules'), ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
              <path d="M12 4l-8 4l8 4l8 -4l-8 -4"></path>
              <path d="M4 12l8 4l8 -4"></path>
              <path d="M4 16l8 4l8 -4"></path>
            </svg>', 'active' => request()->segment(1) == 'manage-modules'])->order(23);
            }

            // Per-client Business Features menu (superadmin or manage_modules)
            if (empty(auth()->user()->business_id) || auth()->user()->hasRole('Superadmin') || auth()->user()->can('manage_modules')) {
                $menu->dropdown(
                    'SaaS Management',
                    function ($sub) {
                        $sub->url(url('saas-admin'), 'Dashboard', ['icon' => '', 'active' => request()->is('saas-admin')]);
                        $sub->url(url('saas-admin/business'), 'Manage Businesses', ['icon' => '', 'active' => request()->is('saas-admin/business*')]);
                        $sub->url(url('saas-admin/features'), 'Features & Pricing', ['icon' => '', 'active' => request()->is('saas-admin/features*')]);
                        $sub->url(url('saas-admin/bundles'), 'Bundles', ['icon' => '', 'active' => request()->is('saas-admin/bundles*')]);
                        $sub->url(url('saas-admin/subscriptions'), 'Subscriptions', ['icon' => '', 'active' => request()->is('saas-admin/subscriptions*')]);
                        $sub->url(url('saas-admin/saas-settings'), 'SaaS Settings', ['icon' => '', 'active' => request()->is('saas-admin/saas-settings*')]);
                    },
                    ['icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="tw-size-5 tw-shrink-0" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 4h6v6h-6z" /><path d="M14 4h6v6h-6z" />
                        <path d="M4 14h6v6h-6z" />
                        <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                    </svg>',
                    'active' => request()->is('saas-admin*')]
                )->order(10);
            }

            //Booking menu
            if (in_array('booking', $enabled_modules) && (auth()->user()->can('crud_all_bookings') || auth()->user()->can('crud_own_bookings'))) {
                $menu->url(action([\App\Http\Controllers\Restaurant\BookingController::class, 'index']), __('restaurant.bookings'), ['icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11.5 21h-5.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v6" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M15 19l2 2l4 -4" /></svg>', 'active' => request()->segment(1) == 'bookings'])->order(12);
            }

            //Kitchen menu
            if (in_array('kitchen', $enabled_modules)) {
                $menu->url(action([\App\Http\Controllers\Restaurant\KitchenController::class, 'index']), __('restaurant.kitchen'), ['icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-flame"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12c2 -2.96 0 -7 -1 -8c0 3.038 -1.773 4.741 -3 6c-1.226 1.26 -2 3.24 -2 5a6 6 0 1 0 12 0c0 -1.532 -1.056 -3.94 -2 -5c-1.786 3 -2.791 3 -4 2z" /></svg>', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'kitchen'])->order(21);
            }

            //Service Staff menu
            if (in_array('service_staff', $enabled_modules)) {
                $menu->url(action([\App\Http\Controllers\Restaurant\OrderController::class, 'index']), __('restaurant.orders'), ['icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="18"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-baseline-density-medium"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h16" /><path d="M4 12h16" /><path d="M4 4h16" /></svg>', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'orders'])->order(30);
            }

            //Notification template menu
            if (auth()->user()->can('send_notifications')) {
                $menu->url(action([\App\Http\Controllers\NotificationTemplateController::class, 'index']), __('lang_v1.notification_templates'), ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
                    <path d="M3 7l9 6l9 -6"></path>
                  </svg>', 'active' => request()->segment(1) == 'notification-templates'])->order(24);
            }

            // SMS dropdown
            if (in_array('sms', $enabled_modules) && auth()->user()->can('send_notifications') && Route::has('sms.send')) {
                $menu->dropdown(
                    'SMS',
                    function ($sub) {
                        if (Route::has('sms.send')) {
                            $sub->url(
                                route('sms.send'),
                                'Send SMS',
                                ['icon' => '', 'active' => request()->segment(1) == 'sms' && request()->segment(2) == 'send']
                            );
                        }
                        if (Route::has('sms.automated')) {
                            $sub->url(
                                route('sms.automated'),
                                'Automated Messages',
                                ['icon' => '', 'active' => request()->segment(1) == 'sms' && request()->segment(2) == 'automated']
                            );
                        }
                        if (Route::has('sms.history')) {
                            $sub->url(
                                route('sms.history'),
                                'SMS History',
                                ['icon' => '', 'active' => request()->segment(1) == 'sms' && request()->segment(2) == 'history']
                            );
                        }
                        $sub->url(
                            action([\App\Http\Controllers\BusinessController::class, 'getBusinessSettings']),
                            'SMS Settings',
                            ['icon' => '', 'active' => false]
                        );
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M8 9h8"></path>
                    <path d="M8 13h6"></path>
                    <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"></path>
                  </svg>', 'active' => request()->segment(1) == 'sms']
                )->order(31);
            }

            // WhatsApp dropdown
            if (in_array('whatsapp', $enabled_modules) && auth()->user()->can('send_notifications')) {
                $menu->dropdown(
                    'WhatsApp',
                    function ($sub) {
                        $sub->url(
                            action([\App\Http\Controllers\NotificationTemplateController::class, 'index']),
                            'Automated Messages',
                            ['icon' => '', 'active' => false]
                        );
                        $sub->url(
                            action([\App\Http\Controllers\BusinessController::class, 'getBusinessSettings']),
                            'WhatsApp Settings',
                            ['icon' => '', 'active' => false]
                        );
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9"></path>
                    <path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1"></path>
                  </svg>', 'active' => false]
                )->order(36);
            }

            // DDA (Dangerous Drugs Act) Dropdown
            if (in_array('dda', $enabled_modules) && auth()->user()->can('dda.view') && Route::has('dda.dashboard')) {
                $menu->dropdown(
                    'DDA Register',
                    function ($sub) {
                        if (Route::has('dda.dashboard')) {
                            $sub->url(
                                route('dda.dashboard'),
                                'Dashboard',
                                ['icon' => '', 'active' => request()->is('dda') && !request()->segment(2)]
                            );
                        }
                        if (Route::has('dda.drugs')) {
                            $sub->url(
                                route('dda.drugs'),
                                'DDA Drug List',
                                ['icon' => '', 'active' => request()->segment(1) == 'dda' && request()->segment(2) == 'drugs']
                            );
                        }
                        if (Route::has('dda.prescriptions')) {
                            $sub->url(
                                route('dda.prescriptions'),
                                'Prescriptions',
                                ['icon' => '', 'active' => request()->segment(1) == 'dda' && request()->segment(2) == 'prescriptions']
                            );
                        }
                        if (Route::has('dda.dispense')) {
                            $sub->url(
                                route('dda.dispense'),
                                'Dispense Register',
                                ['icon' => '', 'active' => request()->segment(1) == 'dda' && request()->segment(2) == 'dispense']
                            );
                        }
                        if (Route::has('dda.stock')) {
                            $sub->url(
                                route('dda.stock'),
                                'Stock Balance',
                                ['icon' => '', 'active' => request()->segment(1) == 'dda' && request()->segment(2) == 'stock']
                            );
                        }
                        if (Route::has('dda.sales')) {
                            $sub->url(
                                route('dda.sales'),
                                'DDA Sales',
                                ['icon' => '', 'active' => request()->segment(1) == 'dda' && request()->segment(2) == 'sales']
                            );
                        }
                        if (Route::has('dda.destruction')) {
                            $sub->url(
                                route('dda.destruction'),
                                'Destruction Log',
                                ['icon' => '', 'active' => request()->segment(1) == 'dda' && request()->segment(2) == 'destruction']
                            );
                        }
                        if (Route::has('dda.expired')) {
                            $sub->url(
                                route('dda.expired'),
                                'Expired Drugs',
                                ['icon' => '', 'active' => request()->segment(1) == 'dda' && request()->segment(2) == 'expired']
                            );
                        }
                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 3h6l3 7l-6 2l-3 -2z"></path>
                    <path d="M9 3l-3 7l6 2"></path>
                    <path d="M12 12v9"></path>
                    <path d="M6 21h12"></path>
                  </svg>', 'active' => request()->segment(1) == 'dda']
                )->order(17);
            }

            //Settings Dropdown
            if (auth()->user()->can('business_settings.access') ||
                auth()->user()->can('barcode_settings.access') ||
                auth()->user()->can('invoice_settings.access') ||
                auth()->user()->can('tax_rate.view') ||
                auth()->user()->can('tax_rate.create') ||
                auth()->user()->can('access_package_subscriptions')) {
                $menu->dropdown(
                    __('business.settings'),
                    function ($sub) use ($enabled_modules) {
                        if (auth()->user()->can('business_settings.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\BusinessController::class, 'getBusinessSettings']),
                                __('business.business_settings'),
                                ['icon' => '', 'active' => request()->segment(1) == 'business', 'id' => 'tour_step2']
                            );
                            $sub->url(
                                action([\App\Http\Controllers\BusinessLocationController::class, 'index']),
                                __('business.business_locations'),
                                ['icon' => '', 'active' => request()->segment(1) == 'business-location']
                            );
                        }
                        if (auth()->user()->can('invoice_settings.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\InvoiceSchemeController::class, 'index']),
                                __('invoice.invoice_settings'),
                                ['icon' => '', 'active' => in_array(request()->segment(1), ['invoice-schemes', 'invoice-layouts'])]
                            );
                        }
                        if (auth()->user()->can('barcode_settings.access')) {
                            $sub->url(
                                action([\App\Http\Controllers\BarcodeController::class, 'index']),
                                __('barcode.barcode_settings'),
                                ['icon' => '', 'active' => request()->segment(1) == 'barcodes']
                            );
                        }
                        if (auth()->user()->can('access_printers')) {
                            $sub->url(
                                action([\App\Http\Controllers\PrinterController::class, 'index']),
                                __('printer.receipt_printers'),
                                ['icon' => '', 'active' => request()->segment(1) == 'printers']
                            );
                        }

                        if (auth()->user()->can('tax_rate.view') || auth()->user()->can('tax_rate.create')) {
                            $sub->url(
                                action([\App\Http\Controllers\TaxRateController::class, 'index']),
                                __('tax_rate.tax_rates'),
                                ['icon' => '', 'active' => request()->segment(1) == 'tax-rates']
                            );
                        }

                        if (in_array('tables', $enabled_modules) && auth()->user()->can('access_tables')) {
                            $sub->url(
                                action([\App\Http\Controllers\Restaurant\TableController::class, 'index']),
                                __('restaurant.tables'),
                                ['icon' => '', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'tables']
                            );
                        }

                        if (in_array('modifiers', $enabled_modules) && (auth()->user()->can('product.view') || auth()->user()->can('product.create'))) {
                            $sub->url(
                                action([\App\Http\Controllers\Restaurant\ModifierSetsController::class, 'index']),
                                __('restaurant.modifiers'),
                                ['icon' => '', 'active' => request()->segment(1) == 'modules' && request()->segment(2) == 'modifiers']
                            );
                        }

                        if (in_array('types_of_service', $enabled_modules) && auth()->user()->can('access_types_of_service')) {
                            $sub->url(
                                action([\App\Http\Controllers\TypesOfServiceController::class, 'index']),
                                __('lang_v1.types_of_service'),
                                ['icon' => '', 'active' => request()->segment(1) == 'types-of-service']
                            );
                        }

                    },
                    ['icon' => '<svg aria-hidden="true" class="tw-size-5 tw-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path>
                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                  </svg>', 'id' => 'tour_step3']
                )->order(85);
            }
        });

        // Approval System
        $enabled_modules_for_approvals = !empty(session('business.enabled_modules')) ? session('business.enabled_modules') : [];
        if (in_array('approvals', $enabled_modules_for_approvals)) {
            $pending_count = \App\Approval::where('business_id', request()->session()->get('user.business_id'))
                ->where('status', 'pending')->count();
            \Menu::modify('admin-sidebar-menu', function ($menu) use ($pending_count) {
                $menu->url(
                    route('approvals.index'),
                    '<i class="fa fa-check-square-o"></i> <span>Approvals</span>'
                    . ($pending_count ? ' <span class="pull-right-container"><span class="label label-primary pull-right">' . $pending_count . '</span></span>' : ''),
                    ['active' => request()->is('approvals*'), 'id' => 'approvals_menu']
                )->order(86);
            });
        }

        //Add menus from modules (never crash the whole app if a module menu fails)
        try {
            $moduleUtil = new ModuleUtil;
            $moduleUtil->getModuleData('modifyAdminMenu');
        } catch (\Throwable $e) {
            \Log::warning('modifyAdminMenu failed: ' . $e->getMessage());
        }

        return $next($request);
    }
}

