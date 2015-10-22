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
use MeTools\Shell\Base\BaseShell;
use MeTools\Utility\Thumbs;
use MeTools\Utility\Unix;

/**
 * Executes some tasks to make the system ready to work
 */
class InstallShell extends BaseShell {
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
	 * @uses MeTools\Utility\Unix::is_root()
	 * @uses $links
	 * @uses $packages
	 * @uses $paths
	 */
	public function __construct() {
		parent::__construct();

		//Checks if the current user is the root user		
		if(!Unix::is_root())
			return $this->error(__d('me_tools', 'You have to run this command as root user or using sudo'));
		
		//Assets for which create symbolic links
		$this->links = [
			'ckeditor/ckeditor'							=> 'ckeditor',
			'components/bootstrap-datetimepicker/build'	=> 'bootstrap-datetimepicker',
			'components/jquery'							=> 'jquery',
			'components/moment/min'						=> 'moment',
			'fortawesome/font-awesome'					=> 'font-awesome',
			'newerton/fancy-box/source'					=> 'fancybox',
			'twbs/bootstrap/dist'						=> 'bootstrap'
		];
		
		//Suggested packages to install by Composer
		$this->packages = [
			'ckeditor/ckeditor',
			'components/bootstrap-datetimepicker',
			'newerton/fancy-box'
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
			WWW_ROOT.'files',
			WWW_ROOT.'vendor',
			dirname(Thumbs::photo()),
			Thumbs::photo(),
			Thumbs::remote(),
			Thumbs::video()
		];
	}

	/**
	 * Rewrites the header for the shell
	 */
	protected function _welcome() { }
	
	/**
	 * Executes all available tasks
	 * @uses createDirectories()
	 * @uses createRobots()
	 * @uses createSymbolicLinks()
	 * @uses fixComposerJson()
	 * @uses installPackages()
	 * @uses setPermissions()
	 */
	public function all() {
		if(!empty($this->params['force'])) {
			$this->createDirectories();
			$this->setPermissions();
			$this->createSymbolicLinks();
			$this->createRobots();
			$this->fixComposerJson();
			$this->installPackages();
			
			return;
		}
		
		$ask = $this->in(__d('me_tools', 'Create default directories?'), ['y', 'N'], 'N');
		if(in_array($ask, ['Y', 'y']))
			$this->createDirectories();
		
		$ask = $this->in(__d('me_tools', 'Set folder permissions?'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->setPermissions();
		
		$ask = $this->in(__d('me_tools', 'Create symbolic links for vendor assets?'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->createSymbolicLinks();
		
		$ask = $this->in(__d('me_tools', 'Create `{0}`?', 'robots.txt'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->createRobots();
		
		$ask = $this->in(__d('me_tools', 'Fix `{0}`?', 'composer.json'), ['Y', 'n'], 'Y');
		if(in_array($ask, ['Y', 'y']))
			$this->fixComposerJson();	
		
		$ask = $this->in(__d('me_tools', 'Install the suggested packages?'), ['y', 'N'], 'N');
		if(in_array($ask, ['Y', 'y']))
			$this->installPackages();
    }

	/**
	 * Creates directories
	 * @uses $paths
	 */
	public function createDirectories() {
		foreach($this->paths as $path)
			if(!file_exists($path)) {
				if(mkdir($path, 0777, TRUE))
					$this->success(__d('me_tools', 'Created `{0}` directory', rtr($path)));
				else
					$this->error(__d('me_tools', 'Failed to create directory `{0}`', rtr($path)));	
			}
	}
	
	/**
	 * Creates the `robots.txt` file
	 */
	public function createRobots() {
		if(file_exists($file = WWW_ROOT.'robots.txt'))
			return;
		
		if($this->createFile($file, 'User-agent: *
				Disallow: /admin/
				Disallow: /ckeditor/
				Disallow: /css/
				Disallow: /js/
				Disallow: /vendor/'))
			$this->success(__d('me_tools', 'The file `{0}` has been created', $file));
		else
			$this->error(__d('me_tools', 'The file `{0}` has not been created', $file));
	}
	
	/**
	 * Creates symbolic links for vendor assets
	 * @uses $links
	 */
	public function createSymbolicLinks() {
		//Checks if the target directory (`webroot/vendor/`) is writeable
		if(!is_writable($destinationDir = WWW_ROOT.'vendor'))
			return $this->error(__d('me_tools', '`{0}` doesn\'t exists or is not readable', $destinationDir));
				
		foreach($this->links as $origin => $destination) {
			$origin = ROOT.DS.'vendor'.DS.$origin;
			
			//Continues, if the vendor doesn't exist
			if(!file_exists($origin))
				continue;
			
			$destination = $destinationDir.DS.$destination;
			
			//Continues, if the link already exists
			if(file_exists($destination))
				continue;
			
			//Creates the symbolic link
			if(@symlink($origin, $destination))
				$this->success(__d('me_tools', 'Created symbolic link to `{0}`', rtr($destination)));
			else
				$this->error(__d('me_tools', 'Failed to create a symbolic link to `{0}`', rtr($destination)));
		}
	}
	
	/**
	 * Fixes `composer.json`
	 */
	public function fixComposerJson() {
		if(!is_writeable($file = ROOT.DS.'composer.json'))
			return $this->error(__d('me_tools', '`{0}` doesn\'t exists or is not writeable', rtr($file)));
		
		$contents = json_decode(file_get_contents($file), TRUE);
		
		$contents['config']['component-dir'] = 'vendor/components';
		
		$contents = (new File($file))->prepare(json_encode($contents, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
		
		if((new File($file))->write($contents))
			$this->success(__d('me_tools', 'The file `{0}` has been fixed', rtr($file)));
		else
			$this->error(__d('me_tools', 'The file `{0}` has not been fixed', rtr($file)));
	}
	
	/**
	 * Gets the option parser instance and configures it.
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		
		$parser->addSubcommands([
				'all'					=> ['help' => __d('me_tools', 'it executes all available tasks')],
				'createDirectories'		=> ['help' => __d('me_tools', 'it creates directories')],
				'createRobots'			=> ['help' => __d('me_tools', 'it creates the `{0}` file', 'robots.txt')],
				'createSymbolicLinks'	=> ['help' => __d('me_tools', 'it creates symbolic links for vendor assets')],
				'fixComposerJson'		=> ['help' => __d('me_tools', 'it fixes `{0}`', 'composer.json')],
				'installPackages'		=> ['help' => __d('me_tools', 'it install the suggested packages')],
				'setPermissions'		=> ['help' => __d('me_tools', 'it sets permissions on directories')]
			])
			->addOption('force', [
				'boolean'	=> TRUE,
				'default'	=> FALSE,
				'help'		=> __d('me_tools', 'Executes tasks without prompting'),
				'short'		=> 'f'
			])
			->description(__d('me_tools', 'Executes some tasks to make the system ready to work'));
				
		return $parser;
	}
	
	/**
	 * Install the suggested packages
	 * @uses MeTools\Utility\Unix::which()
	 * @uses $packages
	 */
	public function installPackages() {
		//Checks for Composer
		if(!($bin = Unix::which('composer')))
			return $this->error(__d('me_tools', 'I can\'t find {0}', 'Composer'));
		
		if(empty($this->params['force'])) {
			foreach($this->packages as $package) {
				$ask = $this->in(__d('me_tools', 'Do you want to install `{0}`?', $package), ['Y', 'n'], 'Y');
				if(in_array($ask, ['Y', 'y']))
					$packagesToInstall[] = $package;
			}

			if(empty($packagesToInstall))
				return;
		}
		else
			$packagesToInstall = $this->packages;
			
		//Executes the command
		exec(sprintf('%s require %s', $bin, implode(' ', $packagesToInstall)));
	}
	
	/**
	 * Sets permissions on directories
	 * @uses $paths
	 */
	public function setPermissions() {
		foreach($this->paths as $path)
			if(!(new \Cake\Filesystem\Folder())->chmod($path, 0777))
                $this->error(__d('me_tools', 'Failed to set permissions on `{0}`', rtr($path)));	
	}
}