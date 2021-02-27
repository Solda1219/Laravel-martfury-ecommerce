<?php

namespace Botble\AuditLog\Providers;

use Botble\AuditLog\Commands\ActivityLogClearCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActivityLogClearCommand::class,
            ]);
        }
    }
}
