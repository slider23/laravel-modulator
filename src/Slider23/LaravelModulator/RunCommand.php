<?php namespace Slider23\LaravelModulator;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Config;
use Exception;

class RunCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'modulator';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create module in your namespace.';
	/**
	 * @var Filesystem
	 */
	private $filesystem;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Filesystem $filesystem)
	{
		parent::__construct();
		$this->filesystem = $filesystem;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$module_name = $this->argument("modulename");
		$module_folder_path = $this->option("path");
		$template_name = $this->option("template");
		if( ! $template_name) $template_name = "default";
		$is_debug = $this->option("debug");

		if(!$module_name OR !$module_folder_path){
			$this->info("Creating new module for your laravel application (app/NamespaceRoot/[Somefolder/]Somemodule).");
			if(!$module_folder_path) $module_folder_path = $this->ask('Path to your module folder (for example app/NamespaceRoot/Somefolder , all folders must be exist): ');
			if(!$module_name) $module_name = $this->ask("Module name (for example Somemodule): ");
			if(!$template_name) $template_name = $this->ask("Module template [default]: ");
		}

		$module_name_lower = strtolower($module_name);
		$module_name_capitalized = ucwords($module_name);
		$module_name_pluralized = str_plural($module_name_lower);

		$module_folder_path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $module_folder_path);
		if($is_debug) $this->info("module_folder_path = $module_folder_path");
		$module_base_namespace = str_replace(DIRECTORY_SEPARATOR, '\\',
								str_replace("app".DIRECTORY_SEPARATOR, "", $module_folder_path.DIRECTORY_SEPARATOR.$module_name)
							);
		if($is_debug) $this->info("module_base_namespace = $module_base_namespace");


		$config_template_path = Config::get("laravel-modulator::config.templates_path");
		if(is_array($config_template_path)){
			if( ! isset($config_template_path[$template_name])){
				throw new Exception("Path to template $template_name not found. Check laravel-modulator`s config.php.");
			}
			$templates_path = $config_template_path[$template_name];
		}else{
			$templates_path = $config_template_path;
		}
		$templates_path = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $this->laravel['path.base'].DIRECTORY_SEPARATOR.$templates_path);
		if($is_debug) $this->info("templates_path = $templates_path");

		$destination_path = $this->laravel['path.base'].DIRECTORY_SEPARATOR.$module_folder_path.DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR;
		if($is_debug) $this->info("destination_path = $destination_path");

		$this->filesystem->copyDirectory($templates_path, $destination_path);
		if($is_debug) $this->info("directory copied.");

		$directories = array();
		foreach (Finder::create()->in($destination_path)->directories()->depth(" < 100") as $dir)
		{
			$directories[] = $dir->getPathname();
		}
		foreach($directories as $directory_name){
			$new_directory_name = str_replace("modulename", $module_name_lower, $directory_name);
			if($new_directory_name != $directory_name){
				$this->filesystem->copyDirectory($directory_name, $new_directory_name);
				$this->filesystem->deleteDirectory($directory_name);
				if($is_debug) $this->info("directory rename $directory_name -> $new_directory_name");
			}
		}

		foreach($this->filesystem->allFiles($destination_path) as $filename){

			$content = $this->filesystem->get($filename);
			if($is_debug) $this->info("get $filename");
			$content = str_replace("{{Modulename}}", $module_name_capitalized, $content);
			$content = str_replace("{{modulename}}", $module_name_lower, $content);
			$content = str_replace("{{modulenames}}", $module_name_pluralized, $content);
			$content = str_replace("{{namespace}}", $module_base_namespace, $content);
			$this->filesystem->put($filename, $content);
			if($is_debug) $this->info("put new content to $filename");

			$new_filename = str_replace(".txt", ".php", $filename);
			$new_filename = str_replace("modulename", $module_name_lower, $new_filename);
			$new_filename = str_replace("modulenames", $module_name_pluralized, $new_filename);
			$new_filename = str_replace("Modulename", $module_name_capitalized, $new_filename);
			rename($filename, $new_filename);
			if($is_debug) { $this->info("rename to $new_filename"); $this->info("--"); }
		}

		$this->info("Module created.");
	}


	private function getPartPath($path = '', $depth = 0) {
		$pathArray = array();
		$pathArray = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
		if($depth < 0)
			$depth = count($pathArray)+$depth;

		if(!isset($pathArray[$depth]))
			return false;
		return $pathArray[$depth];
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('modulename', InputArgument::OPTIONAL, 'Your module name.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('path', null, InputOption::VALUE_OPTIONAL, 'Path to module folder (\'app/Acme\' for example).', null),
			array('template', null, InputOption::VALUE_OPTIONAL, "Module template folder (see config.php).", null),
			array('debug', 0, InputOption::VALUE_OPTIONAL, 'Debug output.', null),
		);
	}

}
