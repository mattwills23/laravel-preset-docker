<?php

namespace mattwills23\LaravelPresetDocker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\PresetCommand;

class PresetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PresetCommand::macro('docker', function ($command) {
            $command->info('');
            $command->info('By default this preset configures containers running the following software:');
            $command->table(['Software','Version'],[['PHP','7.2'],['Nginx','1.5.8'],['MySQL','5.7'],['Node','11.9']]);

            $options['redis'] = $command->confirm('Would you like to add a Redis container?', false);
            $options['mailhog'] = $command->confirm('Would you like to add Mailhog container?', false);
            $options['test-db'] =$command->confirm('Would you like to use a separate MySQL database for testing?', false);

            Preset::install($command, $options);

            $command->info('Docker preset installed successfully.');
            $command->info('Please run "docker-compose up -d" to build and start the environment.');
        });
    }
}