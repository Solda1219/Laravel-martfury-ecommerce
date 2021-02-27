<?php

namespace Botble\Ecommerce\Providers;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Html;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Menu;
use Throwable;

class HookServiceProvider extends ServiceProvider
{

    /**
     * @var Collection
     */
    protected $pendingOrders = [];

    public function boot()
    {
        if (defined('MENU_ACTION_SIDEBAR_OPTIONS')) {
            Menu::addMenuOptionModel(Brand::class);
            Menu::addMenuOptionModel(ProductCategory::class);
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 12);
        }

        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 208, 2);

        if (function_exists('theme_option')) {
            add_action(RENDERING_THEME_OPTIONS_PAGE, [$this, 'addThemeOptions'], 35);
        }

        add_filter(BASE_FILTER_TOP_HEADER_LAYOUT, [$this, 'registerTopHeaderNotification'], 121);
        add_filter(BASE_FILTER_APPEND_MENU_NAME, [$this, 'getPendingOrders'], 130, 2);
    }

    public function addThemeOptions()
    {
        theme_option()
            ->setSection([
                'title'      => trans('plugins/ecommerce::ecommerce.theme_options.name'),
                'desc'       => trans('plugins/ecommerce::ecommerce.theme_options.description'),
                'id'         => 'opt-text-subsection-ecommerce',
                'subsection' => true,
                'icon'       => 'fa fa-shopping-cart',
                'fields'     => [
                    [
                        'id'         => 'number_of_products_per_page',
                        'type'       => 'number',
                        'label'      => trans('plugins/ecommerce::ecommerce.theme_options.number_products_per_page'),
                        'attributes' => [
                            'name'    => 'number_of_products_per_page',
                            'value'   => 12,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id'         => 'max_filter_price',
                        'type'       => 'number',
                        'label'      => trans('plugins/ecommerce::ecommerce.theme_options.max_price_filter'),
                        'attributes' => [
                            'name'    => 'max_filter_price',
                            'value'   => 100000,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Register sidebar options in menu
     *
     * @throws Throwable
     */
    public function registerMenuOptions()
    {
        if (Auth::user()->hasPermission('brands.index')) {
            Menu::registerMenuOptions(Brand::class, trans('plugins/ecommerce::brands.menu'));
        }

        if (Auth::user()->hasPermission('product-categories.index')) {
            Menu::registerMenuOptions(ProductCategory::class, trans('plugins/ecommerce::product-categories.menu'));
        }

        return true;
    }

    /**
     * @param array $widgets
     * @param Collection $widgetSettings
     * @return array
     * @throws Throwable
     */
    public function registerDashboardWidgets($widgets, $widgetSettings)
    {
        if (!Auth::user()->hasPermission('ecommerce.report.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly(['vendor/core/plugins/ecommerce/js/report.js']);

        return (new DashboardWidgetInstance)
            ->setPermission('ecommerce.report.index')
            ->setKey('widget_ecommerce_report_general')
            ->setTitle(trans('plugins/ecommerce::ecommerce.name'))
            ->setIcon('fas fa-shopping-basket')
            ->setColor('#7ad03a')
            ->setRoute(route('ecommerce.report.dashboard-widget.general'))
            ->setBodyClass('scroll-table')
            ->setColumn('col-md-6 col-sm-6')
            ->init($widgets, $widgetSettings);
    }

    /**
     * @param string $options
     * @return string
     *
     * @throws Throwable
     */
    public function registerTopHeaderNotification($options)
    {
        if (Auth::user()->hasPermission('orders.edit')) {
            $orders = $this->setPendingOrders();

            if ($orders->count() == 0) {
                return null;
            }

            return $options . view('plugins/ecommerce::orders.notification', compact('orders'))->render();
        }

        return $options;
    }

    /**
     * @param int $number
     * @param string $menuId
     * @return string
     * @throws BindingResolutionException
     */
    public function getPendingOrders($number, $menuId)
    {
        if (Auth::user()->hasPermission('orders.index')) {
            if (in_array($menuId, ['cms-plugins-ecommerce', 'cms-plugins-ecommerce-order'])) {

                $this->setPendingOrders();
                if (count($this->pendingOrders) > 0) {
                    return Html::tag('span', (string)count($this->pendingOrders), ['class' => 'badge badge-success'])
                        ->toHtml();
                }
            }
        }

        return $number;
    }

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    protected function setPendingOrders(): Collection
    {
        if (!$this->pendingOrders) {
            $this->pendingOrders = $this->app->make(OrderInterface::class)->allBy([
                'status'                => BaseStatusEnum::PENDING,
                'ec_orders.is_finished' => 1,
            ], ['address']);
        }

        return $this->pendingOrders;
    }
}
