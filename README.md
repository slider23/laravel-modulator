# laravel-modulator
Artisan generator for easy creating module in your Laravel namespace-based application.

<a href="https://packagist.org/packages/slider23/laravel-modulator"><img src="http://img.shields.io/packagist/v/slider23/laravel-modulator.svg?style=flat" /></a>

## Installation

Require this package in your composer.json and run composer update (or run `composer require slider23/laravel-modulator:1.*` directly):

    "slider23/laravel-modulator": "1.*"

After updating composer, add the ServiceProvider to the providers array in `app/config/app.php`

    'Slider23\LaravelModulator\LaravelModulatorServiceProvider',

## Usage

Run Artisan command:

	php artisan modulator --path=app/Acme User
	
where `Acme` - namespace of your application (must be in `autoload` section of `composer.json`) and `User` - name of module.

In folder `app/Acme` will be created:

![Module structure](https://monosnap.com/image/wTflxvS5IZZdxPTc7DQg7LAvjzY158.png)

Add `Acme\User\UserServiceProvider` to the providers array in `app/config/app.php`. Module is ready to work ! 

## Customization

To change module structure clone config to your app:

	php artisan config:publish slider23/laravel-modulator
	
and change path to folder of template.