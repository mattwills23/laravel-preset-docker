<?php

namespace mattwills23\LaravelPresetDocker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\PresetCommand;

class PresetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PresetCommand::macro('docker', function ($command) {
            Preset::install($command);
        });
    }
}