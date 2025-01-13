<?php

namespace App\Providers;

use Native\Laravel\Facades\ChildProcess;
use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open()
            ->width(1400)
            ->height(800);

        // ChildProcess::start(
        //     cmd: ['sh', '-c', 'cd ' . base_path() . ' && docker compose up postgres'],
        //     alias: 'postgres',
        //     persistent: true
        // );

        // TODO: make this flexible by OS
        // Important: php-custom-bin/bin/linux/x64/php-8.3
        ChildProcess::start(
            cmd: ['sh', '-c', 'cd ' . base_path() . ' && ./php-custom-bin/bin/linux/x64/php-8.3/php artisan queue:work -q --timeout=60 --tries 3'],
            alias: 'queue-worker',
            persistent: true
        );
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
