<?php namespace Slider23\LaravelModulator;

use Illuminate\Support\ServiceProvider;

class LaravelModulatorServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->commands('Slider23\LaravelModulator\RunCommand');
	}

	public function boot()
	{
		$this->package('slider23/laravel-modulator');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
