# Laravel Preset: Docker

Set up a Docker-based local development environment for your Laravel project with an artisan preset command.
 

## What's Included

Default:
* PHP 7.2
* Nginx
* MySQL
* Node

Optional:
* Redis
* Mailhog

## Prerequisites

You should have Docker installed on your machine and be familiar with `docker-compose` commands.
[Read an overview of the docker-compose CLI](https://docs.docker.com/compose/reference/overview/)

## Installation

1. Add the repository to your `composer.json` file:

	```json
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/mattwills23/laravel-preset-docker"
		}
	]
	```

2. Install the package via composer:

	```bash
	composer require --dev mattwills23/laravel-preset-docker
	```
	
3. Run the `preset` command with the `docker` option:

	```bash
	php artisan preset docker
	```

## Usage

* You will interact with the environment using `docker-compose` commands
* Once your environment is running your application will be available at <http://localhost>

### Basics

* Start the environment:

	```bash
	docker-compose up -d
	```

* Stop the environment:

	```bash
	docker-compose down
	```
	
* Use artisan:

	```bash
	docker-compose exec app php artisan
	```
	
* Use composer:

	```bash
	docker-compose exec app composer
	```

* Use npm:

	```bash
	docker-compose exec node npm
	```
	
* Run tests:

	```bash
	docker-compose exec app phpunit
	```
	
* See a list of available commands:
	```bash
	docker-compose --help
	```
## Notes

I originally planned on releasing this as part of a Laravel project "starter" repo, containing a Laravel installation,
 this docker setup, my go-to packages, etc. Instead I'm going to break that repo down into multiple presets. This should be more useful
 to the community as you can then pick and choose which presets to use, and you'll be able to install this docker setup on projects already underway.
 
 Be on the lookout for the the following presets that I plan on releasing:
 
 * mattwills23/laravel-preset-docker
 * mattwills23/laravel-preset-utilities
 * mattwills23/laravel-preset-backend
 * mattwills23/laravel-preset-frontend
 
 And a "starter" preset which will install all of my presets in a single command.
 
 * mattwills23/laravel-preset-starter
 
## Acknowledgments

* I got the idea of using a preset instead of a personal "starter" repo from [TJ Miller](https://github.com/sixlive)
	* His take on the concept: <https://github.com/sixlive/laravel-preset>

## License

This project is licensed under the MIT License - see the LICENSE.md file for details   