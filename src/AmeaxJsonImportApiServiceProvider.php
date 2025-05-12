<?php

namespace Ameax\AmeaxJsonImportApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AmeaxJsonImportApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ameax-json-import-api')
            ->hasConfigFile();
    }
    
    public function packageRegistered(): void
    {
        $this->app->singleton('ameax-json-import-api', function () {
            return new AmeaxJsonImportApi(
                config('ameax-json-import-api.api_key'),
                config('ameax-json-import-api.database_name')
            );
        });
    }
}
