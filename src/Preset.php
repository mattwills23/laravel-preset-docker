<?php

namespace mattwills23\LaravelPresetDocker;

use Illuminate\Filesystem\Filesystem;
use sixlive\DotenvEditor\DotenvEditor;
use Illuminate\Foundation\Console\Presets\Preset as BasePreset;

class Preset extends BasePreset
{
    public static function install($command, $options)
    {

        $command->task('Publish docker files', function () {
            static::publishDockerFiles();
        });

        $command->task('Configure docker-compose.yml', function () use ($options) {
            static::configureDockerCompose($options);
        });

        if ($options['redis']) {
            $command->task('Install composer packages', function () use ($options)  {
                static::installComposerPackages();
            });
        }

        $command->task('Update environment files', function () use ($options)  {
            static::updateEnvFiles($options);
        });
    }

    public static function publishDockerFiles()
    {
        copy(__DIR__.'/stubs/docker-compose.yml', base_path('docker-compose.yml'));

        tap(new Filesystem, function ($files) {
            $files->copyDirectory(__DIR__.'/stubs/docker', base_path('docker'));
        });
    }

    public static function configureDockerCompose($options)
    {
        $compose = fopen(base_path('docker-compose.yml'), 'a');

        foreach ($options as $key => $option) {
            if ($option){
                fwrite($compose, PHP_EOL.file_get_contents(__DIR__.'/stubs/optional/'.$key.'.yml'));
            }
        }

        fclose($compose);
    }

    public static function installComposerPackages()
    {
        $packages = [
            'predis/predis',
        ];

        exec('composer require '. implode(' ', $packages));
    }

    public static function updateEnvFiles($options)
    {
        $editor = new DotenvEditor;
        $editor->load(base_path('.env'));
        $editor->set('DB_HOST', 'db');
        $editor->set('DB_PORT', '3306');

        if ($options['mailhog']) {
            $editor->set('MAIL_HOST', 'mailhog');
            $editor->set('MAIL_PORT', '1025');
        }

        if ($options['redis']) {
            $editor->set('REDIS_HOST', 'redis');
            $editor->set('REDIS_PORT', '6379');
        }

        $editor->save();

        copy(base_path('.env'), base_path('.env.docker'));

        if ($options['test-db']) {
            copy(base_path('.env'), base_path('.env.testing'));

            $editor = new DotenvEditor;
            $editor->load(base_path('.env.testing'));
            $editor->set('APP_ENV', 'testing');
            $editor->set('DB_HOST', 'test-db');
            $editor->set('CACHE_DRIVER', 'array');
            $editor->set('MAIL_DRIVER', 'array');
            $editor->set('SESSION_DRIVER', 'array');
            $editor->save();
        }
    }
}