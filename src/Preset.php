<?php

namespace mattwills23\LaravelPresetDocker;

use Illuminate\Filesystem\Filesystem;
use sixlive\DotenvEditor\DotenvEditor;
use Illuminate\Foundation\Console\Presets\Preset as BasePreset;

/**
 * Class Preset
 *
 * @package mattwills23\LaravelPresetDocker
 */
class Preset extends BasePreset
{
    /**
     * @param $command
     */
    public static function install($command)
    {
        static::displayConfigurationMessage($command);

        $options = static::getConfigurationOptions($command);

        $command->task('Publish docker folder', function () {
            static::publishDockerFiles();
        });

        $command->task('Configure docker-compose.yml', function () use ($options) {
            static::configureDockerCompose($options);
        });

        $command->task('Install required composer packages', function () use ($options) {
            static::installComposerPackages($options);
        });

        $command->task('Update environment files', function () use ($options) {
            static::updateEnvironmentFiles($options);
        });

        static::displaySuccessMessage($command);
    }

    /**
     * @param $command
     */
    public static function displayConfigurationMessage($command)
    {
        $command->info('By default this preset configures containers running the following software:');
        $command->table(['Software','Version'], [['PHP','7.2'],['Nginx','1.5.8'],['MySQL','5.7'],['Node','11.9']]);
    }

    /**
     * @param $command
     * @return mixed
     */
    public static function getConfigurationOptions($command)
    {
        $options['redis'] = $command->confirm('Would you like to add a Redis container?', false);
        $options['mailhog'] = $command->confirm('Would you like to add Mailhog container?', false);
        $options['test-db'] = $command->confirm('Would you like to use a separate MySQL database for testing?', false);

        return $options;
    }

    /**
     *
     */
    public static function publishDockerFiles()
    {
        copy(__DIR__.'/stubs/docker-compose.yml', base_path('docker-compose.yml'));

        tap(new Filesystem, function ($files) {
            $files->copyDirectory(__DIR__.'/stubs/docker', base_path('docker'));
        });
    }

    /**
     * @param $options
     */
    public static function configureDockerCompose($options)
    {
        $compose = fopen(base_path('docker-compose.yml'), 'a');

        foreach ($options as $key => $option) {
            if ($option) {
                fwrite($compose, PHP_EOL.file_get_contents(__DIR__.'/stubs/optional/'.$key.'.yml'));
            }
        }

        fclose($compose);
    }

    /**
     * @param $options
     */
    public static function installComposerPackages($options)
    {
        if ($options['redis']) {
            $packages[] = 'predis/predis';
        }

        if (! empty($packages)) {
            exec('composer require '. implode(' ', $packages));
        }
    }

    /**
     * @param $options
     */
    public static function updateEnvironmentFiles($options)
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

    /**
     * @param $command
     */
    public static function displaySuccessMessage($command)
    {
        $command->info('Docker preset installed successfully.');
        $command->info('Please run "docker-compose up -d" to build and start the environment.');
    }
}
