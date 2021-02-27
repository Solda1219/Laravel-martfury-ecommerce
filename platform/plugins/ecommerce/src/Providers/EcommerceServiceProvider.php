<?php

namespace Botble\Ecommerce\Providers;

use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Ecommerce\Facades\CartFacade;
use Botble\Ecommerce\Facades\EcommerceHelperFacade;
use Botble\Ecommerce\Facades\OrderHelperFacade;
use Botble\Ecommerce\Http\Middleware\RedirectIfCustomer;
use Botble\Ecommerce\Http\Middleware\RedirectIfNotCustomer;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\Currency;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\FlashSale;
use Botble\Ecommerce\Models\GroupedProduct;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductCollection;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\ProductVariationItem;
use Botble\Ecommerce\Models\Review;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Models\ShipmentHistory;
use Botble\Ecommerce\Models\Shipping;
use Botble\Ecommerce\Models\ShippingRule;
use Botble\Ecommerce\Models\ShippingRuleItem;
use Botble\Ecommerce\Models\StoreLocator;
use Botble\Ecommerce\Models\Tax;
use Botble\Ecommerce\Models\Wishlist;
use Botble\Ecommerce\Repositories\Caches\AddressCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\BrandCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\CurrencyCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\CustomerCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\DiscountCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\FlashSaleCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\GroupedProductCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\OrderAddressCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\OrderCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\OrderHistoryCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\OrderProductCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductAttributeCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductAttributeSetCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductCategoryCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductCollectionCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductTagCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductVariationCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ProductVariationItemCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ReviewCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShipmentCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShipmentHistoryCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShippingCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShippingRuleCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\ShippingRuleItemCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\StoreLocatorCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\TaxCacheDecorator;
use Botble\Ecommerce\Repositories\Caches\WishlistCacheDecorator;
use Botble\Ecommerce\Repositories\Eloquent\AddressRepository;
use Botble\Ecommerce\Repositories\Eloquent\BrandRepository;
use Botble\Ecommerce\Repositories\Eloquent\CurrencyRepository;
use Botble\Ecommerce\Repositories\Eloquent\CustomerRepository;
use Botble\Ecommerce\Repositories\Eloquent\DiscountRepository;
use Botble\Ecommerce\Repositories\Eloquent\FlashSaleRepository;
use Botble\Ecommerce\Repositories\Eloquent\GroupedProductRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderAddressRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderHistoryRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderProductRepository;
use Botble\Ecommerce\Repositories\Eloquent\OrderRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeSetRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductCategoryRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductCollectionRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductTagRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductVariationItemRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductVariationRepository;
use Botble\Ecommerce\Repositories\Eloquent\ReviewRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShipmentHistoryRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShipmentRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShippingRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShippingRuleItemRepository;
use Botble\Ecommerce\Repositories\Eloquent\ShippingRuleRepository;
use Botble\Ecommerce\Repositories\Eloquent\StoreLocatorRepository;
use Botble\Ecommerce\Repositories\Eloquent\TaxRepository;
use Botble\Ecommerce\Repositories\Eloquent\WishlistRepository;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\CurrencyInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\FlashSaleInterface;
use Botble\Ecommerce\Repositories\Interfaces\GroupedProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductTagInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingRuleInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingRuleItemInterface;
use Botble\Ecommerce\Repositories\Interfaces\StoreLocatorInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Ecommerce\Repositories\Interfaces\WishlistInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Cart;
use EcommerceHelper;
use EmailHandler;
use Event;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;
use SeoHelper;
use SlugHelper;

class EcommerceServiceProvider extends ServiceProvider
{

    use LoadAndPublishDataTrait;

    public function register()
    {
        config([
            'auth.guards.customer'     => [
                'driver'   => 'session',
                'provider' => 'customers',
            ],
            'auth.providers.customers' => [
                'driver' => 'eloquent',
                'model'  => Customer::class,
            ],
            'auth.passwords.customers' => [
                'provider' => 'customers',
                'table'    => 'ec_customer_password_resets',
                'expire'   => 60,
            ],
        ]);

        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $router->aliasMiddleware('customer', RedirectIfNotCustomer::class);
        $router->aliasMiddleware('customer.guest', RedirectIfCustomer::class);

        $this->app->bind(ProductInterface::class, function () {
            return new ProductCacheDecorator(
                new ProductRepository(new Product)
            );
        });

        $this->app->bind(ProductCategoryInterface::class, function () {
            return new ProductCategoryCacheDecorator(
                new ProductCategoryRepository(new ProductCategory)
            );
        });

        $this->app->bind(ProductTagInterface::class, function () {
            return new ProductTagCacheDecorator(
                new ProductTagRepository(new ProductTag)
            );
        });


        $this->app->bind(BrandInterface::class, function () {
            return new BrandCacheDecorator(
                new BrandRepository(new Brand)
            );
        });

        $this->app->bind(ProductCollectionInterface::class, function () {
            return new ProductCollectionCacheDecorator(
                new ProductCollectionRepository(new ProductCollection)
            );
        });

        $this->app->bind(CurrencyInterface::class, function () {
            return new CurrencyCacheDecorator(
                new CurrencyRepository(new Currency)
            );
        });

        $this->app->bind(ProductAttributeSetInterface::class, function () {
            return new ProductAttributeSetCacheDecorator(
                new ProductAttributeSetRepository(new ProductAttributeSet),
                ECOMMERCE_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(ProductAttributeInterface::class, function () {
            return new ProductAttributeCacheDecorator(
                new ProductAttributeRepository(new ProductAttribute),
                ECOMMERCE_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(ProductVariationInterface::class, function () {
            return new ProductVariationCacheDecorator(
                new ProductVariationRepository(new ProductVariation),
                ECOMMERCE_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(ProductVariationItemInterface::class, function () {
            return new ProductVariationItemCacheDecorator(
                new ProductVariationItemRepository(new ProductVariationItem),
                ECOMMERCE_GROUP_CACHE_KEY
            );
        });

        $this->app->bind(TaxInterface::class, function () {
            return new TaxCacheDecorator(
                new TaxRepository(new Tax)
            );
        });

        $this->app->bind(ReviewInterface::class, function () {
            return new ReviewCacheDecorator(
                new ReviewRepository(new Review)
            );
        });

        $this->app->bind(ShippingInterface::class, function () {
            return new ShippingCacheDecorator(
                new ShippingRepository(new Shipping)
            );
        });

        $this->app->bind(ShippingRuleInterface::class, function () {
            return new ShippingRuleCacheDecorator(
                new ShippingRuleRepository(new ShippingRule)
            );
        });

        $this->app->bind(ShippingRuleItemInterface::class, function () {
            return new ShippingRuleItemCacheDecorator(
                new ShippingRuleItemRepository(new ShippingRuleItem)
            );
        });

        $this->app->bind(ShipmentInterface::class, function () {
            return new ShipmentCacheDecorator(
                new ShipmentRepository(new Shipment)
            );
        });

        $this->app->bind(ShipmentHistoryInterface::class, function () {
            return new ShipmentHistoryCacheDecorator(
                new ShipmentHistoryRepository(new ShipmentHistory)
            );
        });

        $this->app->bind(OrderInterface::class, function () {
            return new OrderCacheDecorator(
                new OrderRepository(new Order)
            );
        });

        $this->app->bind(OrderHistoryInterface::class, function () {
            return new OrderHistoryCacheDecorator(
                new OrderHistoryRepository(new OrderHistory)
            );
        });

        $this->app->bind(OrderProductInterface::class, function () {
            return new OrderProductCacheDecorator(
                new OrderProductRepository(new OrderProduct)
            );
        });

        $this->app->bind(OrderAddressInterface::class, function () {
            return new OrderAddressCacheDecorator(
                new OrderAddressRepository(new OrderAddress)
            );
        });

        $this->app->bind(DiscountInterface::class, function () {
            return new DiscountCacheDecorator(
                new DiscountRepository(new Discount)
            );
        });

        $this->app->bind(WishlistInterface::class, function () {
            return new WishlistCacheDecorator(
                new WishlistRepository(new Wishlist)
            );
        });

        $this->app->bind(AddressInterface::class, function () {
            return new AddressCacheDecorator(
                new AddressRepository(new Address)
            );
        });
        $this->app->bind(CustomerInterface::class, function () {
            return new CustomerCacheDecorator(
                new CustomerRepository(new Customer)
            );
        });

        $this->app->bind(GroupedProductInterface::class, function () {
            return new GroupedProductCacheDecorator(
                new GroupedProductRepository(new GroupedProduct)
            );
        });

        $this->app->bind(StoreLocatorInterface::class, function () {
            return new StoreLocatorCacheDecorator(
                new StoreLocatorRepository(new StoreLocator)
            );
        });

        $this->app->bind(FlashSaleInterface::class, function () {
            return new FlashSaleCacheDecorator(
                new FlashSaleRepository(new FlashSale)
            );
        });

        Helper::autoload(__DIR__ . '/../../helpers');

        $loader = AliasLoader::getInstance();
        $loader->alias('Cart', CartFacade::class);
        $loader->alias('OrderHelper', OrderHelperFacade::class);
        $loader->alias('EcommerceHelper', EcommerceHelperFacade::class);

        Event::listen(Logout::class, function () {
            if (config('cart.destroy_on_logout')) {
                $this->app->make(SessionManager::class)->forget('cart');
            }
        });
    }

    public function boot()
    {
        SlugHelper::registerModule(Product::class, 'Products');
        SlugHelper::registerModule(Brand::class, 'Brands');
        SlugHelper::registerModule(ProductCategory::class, 'Product Categories');
        SlugHelper::registerModule(ProductTag::class, 'Product Tags');
        SlugHelper::setPrefix(Product::class, 'products');
        SlugHelper::setPrefix(Brand::class, 'brands');
        SlugHelper::setPrefix(ProductTag::class, 'product-tags');
        SlugHelper::setPrefix(ProductCategory::class, 'product-categories');

        $this->setNamespace('plugins/ecommerce')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes([
                'base',
                'tax',
                'review',
                'shipping',
                'order',
                'discount',
                'customer',
                'payment',
                'cart',
                'shipment',
                'wishlist',
                'compare',
            ])
            ->loadAndPublishConfigurations([
                'general',
                'shipping',
                'order',
                'cart',
                'email',
            ])
            ->loadAndPublishViews()
            ->loadMigrations()
            ->publishAssets();

        $this->app->register(HookServiceProvider::class);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()
                ->registerItem([
                    'id'          => 'cms-plugins-ecommerce',
                    'priority'    => 8,
                    'parent_id'   => null,
                    'name'        => 'plugins/ecommerce::ecommerce.name',
                    'icon'        => 'fa fa-shopping-cart',
                    'url'         => route('products.index'),
                    'permissions' => ['plugins.ecommerce'],
                ])
                ->registerItem([
                    'id'        => 'cms-plugins-ecommerce-report',
                    'priority'  => 0,
                    'parent_id' => 'cms-plugins-ecommerce',
                    'name'      => 'plugins/ecommerce::reports.name',
                    'icon'      => 'far fa-chart-bar',
                    'url'       => route('ecommerce.report.index'),
                    'permissions' => ['ecommerce.report.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-flash-sale',
                    'priority'    => 0,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::flash-sale.name',
                    'icon'        => 'fa fa-bolt',
                    'url'         => route('flash-sale.index'),
                    'permissions' => ['flash-sale.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-ecommerce-order',
                    'priority'    => 1,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::order.name',
                    'icon'        => 'fa fa-shopping-bag',
                    'url'         => route('orders.index'),
                    'permissions' => ['orders.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-ecommerce-incomplete-order',
                    'priority'    => 2,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::order.incomplete_order',
                    'icon'        => 'fas fa-shopping-basket',
                    'url'         => route('orders.incomplete-list'),
                    'permissions' => ['orders.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-ecommerce.product',
                    'priority'    => 3,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::products.name',
                    'icon'        => 'fa fa-camera',
                    'url'         => route('products.index'),
                    'permissions' => ['products.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-product-categories',
                    'priority'    => 4,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::product-categories.name',
                    'icon'        => 'fa fa-archive',
                    'url'         => route('product-categories.index'),
                    'permissions' => ['product-categories.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-product-tag',
                    'priority'    => 4,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::product-tag.name',
                    'icon'        => 'fa fa-tag',
                    'url'         => route('product-tag.index'),
                    'permissions' => ['product-tag.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-product-attribute',
                    'priority'    => 5,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::product-attributes.name',
                    'icon'        => 'fas fa-glass-martini',
                    'url'         => route('product-attribute-sets.index'),
                    'permissions' => ['product-attribute-sets.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-brands',
                    'priority'    => 6,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::brands.name',
                    'icon'        => 'fa fa-registered',
                    'url'         => route('brands.index'),
                    'permissions' => ['brands.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-product-collections',
                    'priority'    => 7,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::product-collections.name',
                    'icon'        => 'fa fa-file-excel',
                    'url'         => route('product-collections.index'),
                    'permissions' => ['product-collections.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-ecommerce-review',
                    'priority'    => 9,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::review.name',
                    'icon'        => 'fa fa-comments',
                    'url'         => route('reviews.index'),
                    'permissions' => ['reviews.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-ecommerce-shipping-provider',
                    'priority'    => 10,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::shipping.shipping',
                    'icon'        => 'fas fa-shipping-fast',
                    'url'         => route('shipping_methods.index'),
                    'permissions' => ['shipping_methods.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-ecommerce-discount',
                    'priority'    => 11,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::discount.name',
                    'icon'        => 'fa fa-gift',
                    'url'         => route('discounts.index'),
                    'permissions' => ['discounts.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-ecommerce-customer',
                    'priority'    => 13,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::customer.name',
                    'icon'        => 'fa fa-users',
                    'url'         => route('customer.index'),
                    'permissions' => ['customer.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-ecommerce.settings',
                    'priority'    => 999,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::ecommerce.settings',
                    'icon'        => 'fas fa-cogs',
                    'url'         => route('ecommerce.settings'),
                    'permissions' => ['ecommerce.settings'],
                ]);

            if (EcommerceHelper::isTaxEnabled()) {
                dashboard_menu()->registerItem([
                    'id'          => 'cms-plugins-ecommerce-tax',
                    'priority'    => 14,
                    'parent_id'   => 'cms-plugins-ecommerce',
                    'name'        => 'plugins/ecommerce::tax.name',
                    'icon'        => 'fas fa-money-check-alt',
                    'url'         => route('tax.index'),
                    'permissions' => ['tax.index'],
                ]);
            }

            EmailHandler::addTemplateSettings(ECOMMERCE_MODULE_SCREEN_NAME, config('plugins.ecommerce.email'));
        });

        $this->app->booted(function () {
            SeoHelper::registerModule([
                Product::class,
                Brand::class,
                ProductCategory::class,
                ProductTag::class,
            ]);
        });

        $this->app->register(EventServiceProvider::class);

        Event::listen(['cart.removed', 'cart.stored', 'cart.restored', 'cart.added', 'cart.updated'], function () {
            $coupon = session('applied_coupon_code');
            if ($coupon) {
                $this->app->make(HandleRemoveCouponService::class)->execute();
                if (Cart::count()) {
                    $this->app->make(HandleApplyCouponService::class)->execute($coupon);
                }
            }
        });
    }
}
