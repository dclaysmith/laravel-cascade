<?php

namespace Dclaysmith\LaravelCascade;

use Dclaysmith\LaravelCascade\Commands\LaravelCascadeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCascadeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cascade')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_cascade_table')
            ->hasCommand(LaravelCascadeCommand::class);
    }
}
