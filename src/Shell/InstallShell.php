<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Shell;

use Cake\Filesystem\File;
use MeTools\Console\Shell;
use MeTools\Utility\Thumbs;
use MeTools\Utility\Unix;

/**
 * Executes some tasks to make the system ready to work
 */
class InstallShell extends Shell {
	/**
	 * Configuration files to be copied
	 * @see __construct()
	 * @var array
	 */
	protected $configs = [];
	
	/**
	 * Assets for which create symbolic links.
	 * Full path for each font
	 * @see __construct()
	 * @var array
	 */
	protected $fonts = [];
	
	/**
	 * Assets for which create symbolic links.
	 * The key must be relative to `vendor/` and the value must be relative to `webroot/vendor/`
	 * @see __construct()
	 * @var array
	 */
	protected $links = [];
	
	/**
	 * Suggested packages to install by Composer
	 * @see __construct()
	 * @var array 
	 */
	protected $packages = [];

	/**
	 * Paths to be created and made writable
	 * @see __construct()
	 * @var array 
	 */
	protected $paths = [];
	
	/**
	 * Construct
	 * @uses MeTools\Utility\Thumbs::photo()
	 * @uses MeTools\Utility\Thumbs::remote()
	 * @uses MeTools\Utility\Thumbs::video()
	 * @uses $config
	 * @uses $fonts
	 * @uses $links
	 * @uses $packages
	 * @uses $paths
	 */
	public function __construct() {
		parent::__construct();
		
		//Configuration files to be copied
		$this->config = [
			'MeTools.recaptcha'
		];
		
		//Assets for which create symbolic links (full paths)
		$this->fonts = [
			ROOT.DS.'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.eot',
			ROOT.DS.'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.ttf',
			ROOT.DS.'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.woff',
			ROOT.DS.'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.woff2'
		];
		
		//Assets for which create symbolic links
		$this->links = [
			'components/bootstrap-datetimepicker/build'	=> 'bootstrap-datetimepicker',
			'components/jquery'							=> 'jquery',
			'components/moment/min'						=> 'moment',
			'fortawesome/font-awesome'					=> 'font-awesome',
			'newerton/fancy-box/source'					=> 'fancybox'
		];
		
		//Suggested packages to install by Composer
		$this->packages = [
			'eonasdan/bootstrap-datetimepicker',
			'newerton/fancy-box:dev-master'
		];
		
		//Paths to be created and made writable
		$this->paths = [
			LOGS,
			TMP.'cache',
			TMP.'cache'.DS.'models',
			TMP.'cache'.DS.'persistent',
			TMP.'cache'.DS.'views',
			TMP.'sessions',
			TMP.'tests',
			WWW_ROOT.'assets',
			WWW_ROOT.'files',
			WWW_ROOT.'fonts',
			WWW_ROOT.'vendor',
			dirname(Thumbs::photo()),
			Thumbs::photo(),
			Thumbs::remote(),
			Thumbs::video()
		];
	}
	
	/**
	 * Executes all available tasks
	 * @uses copyConfig()
	 * @uses copyFonts()
	 * @uses createDirectories()
	 * @uses createRobots()
	 * @uses createSymbolicLinks()
	 * @uses fixComposerJson()
	 * @uses installPackages()
	 * @uses setPermissions()
	 */
	public function all() {
		if($this->param('force')) {
			$this->createDirectories(TRUE);
			$this->setPermissions(TRUE);
			$this->copyConfig();
			$this->createRobots();
			$this->fixComposerJson();
			$this->installPackages(TRUE);
			$this->createSymbolicLinks();
			$this->copyFonts();
			
			return;
		}
		
		$ask = $this->in(__d('me_tools', 'Create default directories?'), ['y', 'N'], 'N');
		if(in_array($ask, ['Y', 'y']))
			$this->createDirectories();
		
		$ask = $this->in(__d('me_tools', 'Set directories permissions?'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->setPermissions();
		
		$ask = $this->in(__d('me_tools', 'Copy configuration files?'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->copyConfig();
		
		$ask = $this->in(__d('me_tools', 'Create `{0}`?', 'robots.txt'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->createRobots();
		
		$ask = $this->in(__d('me_tools', 'Fix `{0}`?', 'composer.json'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->fixComposerJson();	
		
		$ask = $this->in(__d('me_tools', 'Install the suggested packages?'), ['y', 'N', 'all'], 'N');
		if(in_array($ask, ['Y', 'y', 'all']))
			$this->installPackages($ask === 'all' ? TRUE : FALSE);
		
		$ask = $this->in(__d('me_tools', 'Create symbolic links for vendor assets?'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->createSymbolicLinks();
		
		$ask = $this->in(__d('me_tools', 'Create symbolic links for fonts?'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->copyFonts();
    }
	
	/**
	 * Copies the configuration files
	 * @uses $config
	 * @uses MeTools\Core\Plugin::path()
	 */
	public function copyConfig() {
		foreach($this->config as $file) {
			list($plugin, $file) = pluginSplit($file);
			
			$file = sprintf('%s.php', $file);
			
			//Checks if the file already exists
			if(is_readable($target = ROOT.DS.'config'.DS.$file)) {
				$this->verbose(__d('me_tools', 'File or directory `{0}` already exists', rtr($target)));
				continue;
			}
			
			if(@copy(\MeTools\Core\Plugin::path($plugin, 'config'.DS.$file), $target))
				$this->verbose(__d('me_tools', 'The file `{0}` has been copied', rtr($target)));
			else
				$this->err(__d('me_tools', 'The file `{0}` has not been copied', rtr($target)));
		}
	}
	
	/**
	 * Creates symbolic links for fonts
	 * @uses $fonts
	 */
	public function copyFonts() {
		//Checks if the target directory (`webroot/fonts/`) is writeable
		if(is_writable($destinationDir = WWW_ROOT.'fonts')) {
			foreach($this->fonts as $origin) {
				//Continues, if the origin file doesn't exist
				if(!file_exists($origin))
					continue;
				
				$destination = $destinationDir.DS.basename($origin);
				
				//Continues, if the link already exists
				if(file_exists($destination))
					continue;

				//Creates the symbolic link
				if(@symlink($origin, $destination))
					$this->verbose(__d('me_tools', 'Created symbolic link to `{0}`', rtr($destination)));
				else
					$this->err(__d('me_tools', 'Failed to create a symbolic link to `{0}`', rtr($destination)));
			}
		}
		else
			$this->err(__d('me_tools', 'File or directory `{0}` not writeable', rtr($destinationDir)));
	}

	/**
	 * Creates directories
	 * @param bool $force
	 * @uses MeTools\Utility\Unix::which()
	 * @uses $paths
	 */
	public function createDirectories($force = FALSE) {
		$error = FALSE;
		
		foreach($this->paths as $path) {
			if(!file_exists($path)) {
				if(mkdir($path, 0777, TRUE))
					$this->verbose(__d('me_tools', 'Created `{0}` directory', rtr($path)));
				else {
					$this->err(__d('me_tools', 'Failed to create file or directory `{0}`', rtr($path)));
					$error = TRUE;
				}
			}
			else
				$this->verbose(__d('me_tools', 'File or directory `{0}` already exists', rtr($path)));
		}
		
		//In case of error, asks for sudo
		if($error && Unix::which('sudo')) {
			if($this->param('force') || $force)
				return exec(sprintf('sudo mkdir -p %s', implode(' ', $this->paths)));
			
			$ask = $this->in(__d('me_tools', 'It was not possible to create some directories. Try again using `{0}`?', 'sudo'), ['Y', 'n'], 'Y');
			if(in_array($ask, ['Y', 'y']))
				exec(sprintf('sudo mkdir -p %s', implode(' ', $this->paths)));
		}
	}
	
	/**
	 * Creates the `robots.txt` file
	 * @uses MeTools\Console\Shell::createFile()
	 */
	public function createRobots() {
		$this->createFile(WWW_ROOT.'robots.txt', 'User-agent: *
			Disallow: /admin/
			Disallow: /ckeditor/
			Disallow: /css/
			Disallow: /js/
			Disallow: /vendor/'
		);
	}
	
	/**
	 * Creates symbolic links for vendor assets
	 * @uses $links
	 * @uses createLink()
	 */
	public function createSymbolicLinks() {
		foreach($this->links as $origin => $target) {
			//Sets full path to origin and target
			$origin = ROOT.DS.'vendor'.DS.$origin;
			$target = WWW_ROOT.'assets'.DS.$target;
			
			//Creates the link
			$this->createLink($origin, $target);
		}
	}
	
	/**
	 * Fixes `composer.json`
	 */
	public function fixComposerJson() {
		if(!is_writeable($file = ROOT.DS.'composer.json'))
			return $this->err(__d('me_tools', 'File or directory `{0}` not writeable', rtr($file)));
		
		//Gets and decodes the file
		$contents = json_decode(file_get_contents($file), TRUE);
		
		//Checks if the file has been fixed
		if(!empty($contents['config']['component-dir']) && $contents['config']['component-dir'] === 'vendor/components')
			return $this->verbose(__d('me_tools', 'The file `{0}` doesn\'t need to be fixed', rtr($file)));
				
		//Fixeds and encodes the content
		$contents = (new File($file))->prepare(json_encode(am($contents, ['config' => ['component-dir' => 'vendor/components']]), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		
		if((new File($file))->write($contents))
			$this->verbose(__d('me_tools', 'The file `{0}` has been fixed', rtr($file)));
		else
			$this->err(__d('me_tools', 'The file `{0}` has not been fixed', rtr($file)));
	}
	
	/**
	 * Gets the option parser instance and configures it.
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		
		return $parser->addSubcommands([
			'all'					=> ['help' => __d('me_tools', 'Executes all available tasks')],
			'copyConfig'			=> ['help' => __d('me_tools', 'Copies the configuration files')],
			'copyFonts'				=> ['help' => __d('me_tools', 'Creates symbolic links for fonts')],
			'createDirectories'		=> ['help' => __d('me_tools', 'Creates default directories')],
			'createRobots'			=> ['help' => __d('me_tools', 'Creates the `{0}` file', 'robots.txt')],
			'createSymbolicLinks'	=> ['help' => __d('me_tools', 'Creates symbolic links for vendor assets')],
			'fixComposerJson'		=> ['help' => __d('me_tools', 'Fixes `{0}`', 'composer.json')],
			'installPackages'		=> ['help' => __d('me_tools', 'Installs the suggested packages')],
			'setPermissions'		=> ['help' => __d('me_tools', 'Sets directories permissions')]
		])->addOption('force', [
			'boolean'	=> TRUE,
			'default'	=> FALSE,
			'help'		=> __d('me_tools', 'Executes tasks without prompting'),
			'short'		=> 'f'
		])->description(__d('me_tools', 'Executes some tasks to make the system ready to work'));
	}
	
	/**
	 * Install the suggested packages
	 * @param bool $force
	 * @uses MeTools\Utility\Unix::which()
	 * @uses $packages
	 */
	public function installPackages($force = FALSE) {
		//Checks for Composer
		if(!($bin = Unix::which('composer')))
			return $this->err(__d('me_tools', '{0} is not available', 'composer'));
		
		//Empty array. This will contain the packages to install
		$packagesToInstall = [];
		
		//Asks whick packages to install, if it was not asked to install all of them or if you are not using the "force" parameter
		if(!$force && !$this->param('force')) {
			foreach($this->packages as $package) {
				$ask = $this->in(__d('me_tools', 'Do you want to install `{0}`?', $package), ['Y', 'n'], 'Y');
				if(in_array($ask, ['Y', 'y']))
					$packagesToInstall[] = $package;
			}
		
			if(empty($packagesToInstall))
				return $this->verbose(__d('me_tools', 'No package has been selected for installation'));
		}
		else
			$packagesToInstall = $this->packages;
		
		//Gets the list of installed packages
		exec(sprintf('%s show --installed', $bin), $installed);
		$installed = array_map(function($line) { return array_values(preg_split('/[\s]+/', $line))[0]; }, $installed);
		
		$packagesToInstall = array_diff($packagesToInstall, $installed);
		
		if(empty($packagesToInstall))
			return $this->verbose(__d('me_tools', 'All packages are already installed'));
			
		//Executes the command
		exec(sprintf('%s require %s', $bin, implode(' ', $packagesToInstall)));
	}
	
	/**
	 * Sets permissions on directories
	 * @param bool $force
	 * @uses MeTools\Utility\Unix::which()
	 * @uses $paths
	 */
	public function setPermissions($force = FALSE) {
		$error = FALSE;
		
		foreach($this->paths as $path) {
			if((new \Cake\Filesystem\Folder())->chmod($path, 0777))
				$this->verbose(__d('me_tools', 'Setted permissions on `{0}`', rtr($path)));
			else {
                $this->err(__d('me_tools', 'Failed to set permissions on `{0}`', rtr($path)));
				$error = TRUE;
			}
		}
		
		//In case of error, asks for sudo
		if($error && Unix::which('sudo')) {
			if($this->param('force') || $force)
				return exec(sprintf('sudo chmod -R 777 %s', implode(' ', $this->paths)));
			
			$ask = $this->in(__d('me_tools', 'It was not possible to set permissions on some directories. Try again using `{0}`?', 'sudo'), ['Y', 'n'], 'Y');
			if(in_array($ask, ['Y', 'y']))
				exec(sprintf('sudo chmod -R 777 %s', implode(' ', $this->paths)));
		}
	}
}