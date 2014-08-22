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

	php artisan modulator --path=app/Acme User --template=default
	
where
`Acme` - namespace of your application (must be in `autoload` section of `composer.json`)
`User` - name of module for create.
`default` - folder with files of template, defined in config.php . 'default' is devault value, also available template 'formvalidation' with validator an model presenter of Jeffrey Way (https://github.com/laracasts). `--template` is optional.

In folder `app/Acme` will be created:

![Module structure](https://monosnap.com/image/ZYKQ4udjW08d1T76iyDu7RDMfl0WHt.png)

Add `Acme\User\UserServiceProvider` to the providers array in `app/config/app.php`. Module is ready to work ! 

## Customization

To change module structure clone config to your app:

	php artisan config:publish slider23/laravel-modulator
	
and add path to your folder of template to `app/config/packages/slider23/laravel-modulator/config.php`:
```
return array(
	'templates_path' =>
		array(
			'default' => "vendor/slider23/laravel-modulator/src/Slider23/LaravelModulator/templates/default/",
			'formvalidation' => "vendor/slider23/laravel-modulator/src/Slider23/LaravelModulator/templates/formvalidation/",
			'myowntemplate' => "app/storage/my_module_template/"
		)
);
```