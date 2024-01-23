<?php

namespace Denngarr\Seat\Fitting;

use Seat\Services\AbstractSeatPlugin;

class FittingServiceProvider extends AbstractSeatPlugin
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->add_routes();
        $this->add_views();
        $this->add_translations();

        $this->addPublications();

        $this->addMigrations();
    }

    private function addPublications(): void
    {
        $this->publishes([
            __DIR__ . '/Config/fitting.exportlinks.php' => config_path('fitting.exportlinks.php'),
        ],
            ["config", "seat"],
        );

        $this->publishes([
            __DIR__ . '/resources/assets/css' => public_path('web/css'),
            __DIR__ . '/resources/assets/js' => public_path('web/js'),
        ]);
    }

    /**
     * Include the routes.
     */
    public function add_routes(): void
    {
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    public function add_translations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'fitting');
    }

    /**
     * Set the path and namespace for the views.
     */
    public function add_views(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'fitting');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/fitting.config.php', 'fitting.config');

        $this->mergeConfigFrom(
            __DIR__ . '/Config/fitting.sidebar.php',
            'package.sidebar'
        );

        $this->registerPermissions(
            __DIR__ . '/Config/Permissions/fitting.permissions.php', 'fitting');
    }

    private function addMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
    }

    private function addCommands(): void
    {
    }

    /**
     * Return the plugin public name as it should be displayed into settings.
     *
     * @return string
     * @example SeAT Web
     *
     */
    public function getName(): string
    {
        return 'Fitting';
    }


    /**
     * Return the plugin repository address.
     *
     * @example https://github.com/eveseat/web
     *
     * @return string
     */
    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/hermesdj/seat-fitting';
    }

    /**
     * Return the plugin technical name as published on package manager.
     *
     * @return string
     * @example web
     *
     */
    public function getPackagistPackageName(): string
    {
        return 'seat-fitting';
    }

    /**
     * Return the plugin vendor tag as published on package manager.
     *
     * @return string
     * @example eveseat
     *
     */
    public function getPackagistVendorName(): string
    {
        return 'hermesdj';
    }
}
