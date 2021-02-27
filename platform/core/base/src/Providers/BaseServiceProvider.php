<?php

namespace Botble\Base\Providers;

use Botble\Base\Exceptions\Handler;
use Botble\Base\Facades\MacroableModelsFacade;
use Botble\Base\Http\Middleware\CoreMiddleware;
use Botble\Base\Http\Middleware\DisableInDemoModeMiddleware;
use Botble\Base\Http\Middleware\HttpsProtocolMiddleware;
use Botble\Base\Http\Middleware\LocaleMiddleware;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Base\Repositories\Caches\MetaBoxCacheDecorator;
use Botble\Base\Repositories\Eloquent\MetaBoxRepository;
use Botble\Base\Repositories\Interfaces\MetaBoxInterface;
use Botble\Base\Supports\BreadcrumbsManager;
use Botble\Base\Supports\CustomResourceRegistrar;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\Providers\SettingServiceProvider;
use Botble\Setting\Supports\SettingStore;
use DateTimeZone;
use Event;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use MetaBox;

class BaseServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register()
    {
        $this->app->bind(ResourceRegistrar::class, function ($app) {
            return new CustomResourceRegistrar($app['router']);
        });

        Helper::autoload(__DIR__ . '/../../helpers');

        $this->setNamespace('core/base')
            ->loadAndPublishConfigurations(['general']);

        $this->app->register(SettingServiceProvider::class);

        $this->app->singleton(ExceptionHandler::class, Handler::class);

        $this->app->singleton(BreadcrumbsManager::class, BreadcrumbsManager::class);

        $this->app->bind(MetaBoxInterface::class, function () {
            return new MetaBoxCacheDecorator(new MetaBoxRepository(new MetaBoxModel));
        });

        $this->app->make('config')->set([
            'session.cookie'                                     => 'botble_session',
            'purifier.settings.default.AutoFormat.AutoParagraph' => false,
            'purifier.settings.default.AutoFormat.RemoveEmpty'   => false,
            'ziggy.except'                                       => ['debugbar.*'],
            'app.debug_blacklist'                                => [
                '_ENV'    => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                ],
                '_SERVER' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                ],
                '_POST'   => [
                    'password',
                ],
            ],
            'datatables-buttons.pdf_generator'                   => 'excel',
            'excel.exports.csv.use_bom'                          => true,
        ]);
    }

    public function boot()
    {
        $this->setNamespace('core/base')
            ->loadAndPublishConfigurations(['permissions', 'assets'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes(['web'])
            ->loadMigrations()
            ->publishAssets();

        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $router->pushMiddlewareToGroup('web', LocaleMiddleware::class);
        $router->pushMiddlewareToGroup('web', HttpsProtocolMiddleware::class);
        $router->aliasMiddleware('preventDemo', DisableInDemoModeMiddleware::class);
        $router->middlewareGroup('core', [CoreMiddleware::class]);

        $this->app->booted(function () {
            do_action(BASE_ACTION_INIT);
            add_action(BASE_ACTION_META_BOXES, [MetaBox::class, 'doMetaBoxes'], 8, 2);

            $config = $this->app->make('config');
            $setting = $this->app->make(SettingStore::class);
            $timezone = $setting->get('time_zone', $config->get('app.timezone'));
            $locale = $setting->get('locale', $config->get('core.base.general.locale', $config->get('app.locale')));

            $config->set([
                'app.locale'   => $locale,
                'app.timezone' => $timezone,
            ]);

            $this->app->setLocale($locale);

            if (in_array($timezone, DateTimeZone::listIdentifiers())) {
                date_default_timezone_set($timezone);
            }
        });

        Event::listen(RouteMatched::class, function () {
            $this->registerDefaultMenus();
        });

        AliasLoader::getInstance()->alias('MacroableModels', MacroableModelsFacade::class);

        Paginator::useBootstrap();
    }

    /**
     * Add default dashboard menu for core
     */
    public function registerDefaultMenus()
    {
        dashboard_menu()
            ->registerItem([
                'id'          => 'cms-core-platform-administration',
                'priority'    => 999,
                'parent_id'   => null,
                'name'        => 'core/base::layouts.platform_admin',
                'icon'        => 'fa fa-user-shield',
                'url'         => null,
                'permissions' => ['users.index'],
            ])
            ->registerItem([
                'id'          => 'cms-core-system-information',
                'priority'    => 5,
                'parent_id'   => 'cms-core-platform-administration',
                'name'        => 'core/base::system.info.title',
                'icon'        => null,
                'url'         => route('system.info'),
                'permissions' => [ACL_ROLE_SUPER_USER],
            ])
            ->registerItem([
                'id'          => 'cms-core-system-cache',
                'priority'    => 6,
                'parent_id'   => 'cms-core-platform-administration',
                'name'        => 'core/base::cache.cache_management',
                'icon'        => null,
                'url'         => route('system.cache'),
                'permissions' => [ACL_ROLE_SUPER_USER],
            ]);
    }

    /**
     * @return array|string[]
     */
    public function provides(): array
    {
        return [BreadcrumbsManager::class];
    }
}
