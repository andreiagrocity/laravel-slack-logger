<?php

namespace Ioanandrei\SlackLogger;

use Illuminate\Support\ServiceProvider;

class SlackLoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/slack_logger.php' => config_path('slack_logger.php'),
        ]);
    }
}