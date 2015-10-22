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

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use MeTools\Shell\Base\BaseShell;
use MeTools\Utility\Unix;

/**
 * Combines and compresses css and js files
 */
class CompressShell extends BaseShell {
	/**
	 * Parses arguments, checks values and returns input and output files
	 * @param array $args Arguments
	 * @return mixed Input and output files or FALSE
	 */
	protected function _parseArgs($args) {
		//Checks if there are at least 2 arguments
		if(!is_array($args) || count($args) < 2)
			return $this->error(__d('me_tools', 'you have to indicate at least two arguments, an input file and an output file'));
		
		//Gets the output file and the input files. The last argument is the output file, the other arguments are the input files
		$output = array_pop($args);
		$input = is_array($args) ? $args : [$args];

		//Checks that each input files exists and is readable
		foreach($input as $file)
			if(!is_readable($file))
				$this->error(__d('me_tools', '`{0}` doesn\'t exists or is not readable', $file));
		
		//Checks if the output directory is writable
		if(!is_writable(dirname($output)))
			return $this->error(__d('me_tools', '`{0}` doesn\'t exists or is not writeable', dirname($output)));
		
		//If the output file already exists and the "force" option is empty, asks if the output file should be overwritten
		if(file_exists($output) && empty($this->params['force']))
			if($this->in(__d('me_tools', 'The file `{0}` already exists. Do you want to overwrite it?', $output), ['y', 'n'], 'y') === 'n')
				return [FALSE, FALSE];
		
		return [$input, $output];
	}

	/**
	 * Gets the option parser instance and configures it.
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		
		$parser->addSubcommands([
				'auto'		=> ['help' => __d('me_tools', 'it searches all the configuration files and automatically combines and compresses')],
				'config'	=> ['help' => __d('me_tools', 'it combines and compresses files using a configuration file')],
				'css'		=> ['help' => __d('me_tools', 'it combines and compresses css files')],
				'js'		=> ['help' => __d('me_tools', 'it combines and compresses js files')]
			])
			->addOption('force', [
				'boolean'	=> TRUE,
				'default'	=> FALSE,
				'help'		=> __d('me_tools', 'Executes tasks without prompting'),
				'short'		=> 'f'
			])
			->description(__d('me_tools', 'Combines and compresses css and js files'));
				
		return $parser;
	}
	
	/**
	 * Searches all the configuration files and automatically compresses
	 * @return bool
	 * @uses MeTools\Core\Plugin::path()
	 * @uses config()
	 */
	public function auto() {
		//Searches into the application configuration
		foreach((new Folder(ROOT.DS.'config'))->findRecursive('assets.php') as $file)
			if(is_readable($file))
				$files[] = $file;
		
		//Searches into plugins
		foreach(af(\MeTools\Core\Plugin::path()) as $plugin) {			
			if(!is_readable($dir = $plugin.'config'))
				continue;
			
			foreach((new Folder($dir))->findRecursive('assets.php') as $file)
				if(is_readable($file))
					$files[] = $file;
		}
		
		if(empty($files))
			return $this->error(__d('me_tools', 'no configuration files found'));
		
		return $this->config($files);
	}
	
	/**
	 * Combines and compresses files from a configuration file.
	 * 
	 * Example:
	 * <code>bin/cake MeTools.compress config plugins/MyPlugin/config/assets.php</code>
	 * 
	 * Arguments can be passed from the shell. Otherwise, you can call it as a method, passing 
	 * the configuration files as the first argument
	 * @param array $args Configuration files
	 * @uses css()
	 * @uses js()
	 */
	public function config($args = NULL) {
		$args = empty($args) ? $this->args : (is_array($args) ? $args : [$args]);
		
		if(!count($args))
			return $this->error(__d('me_tools', 'you have to indicate at least one config file'));
		
		foreach($args as $file) {
			if(!is_readable($file))
				$this->error(__d('me_tools', '`{0}` doesn\'t exists or is not readable', $file));
			
			Configure::config('default', new \Cake\Core\Configure\Engine\PhpConfig(dirname($file).DS));
			Configure::load(pathinfo($file, PATHINFO_FILENAME), 'default');
			
			foreach(Configure::consume('Assets') as $asset) {
				if(empty($asset['input']) || (!is_string($asset['input']) && !is_array($asset['input'])))
					$this->error(__d('me_tools', 'the "{0}" option is not present or is invalid', 'input'));
				
				if(empty($asset['output']) || !is_string($asset['output']))
					$this->error(__d('me_tools', 'the "{0}" option is not present or is invalid', 'output'));
				
				if(empty($asset['type']) || !in_array($asset['type'], ['css', 'js']))
					$this->error(__d('me_tools', 'the "{0}" option is not present or is invalid', 'type'));
				
				//Adds the extension to the input files
				array_walk($asset['input'], function(&$v, $k, $type) {
					$v = preg_match(sprintf('/\.%s$/', $type), $v) ? $v : sprintf('%s.%s', $v, $type);
				}, $asset['type']);
				
				//Adds the extension to the output file
				if(!preg_match(sprintf('/\.%s$/', $asset['type']), $asset['output']))
					$asset['output'] = sprintf('%s.%s', $asset['output'], $asset['type']);
				
				switch($asset['type']) {
					case 'css':
						$this->css($asset['input'], $asset['output']);
						break;
					case 'js':
						$this->js($asset['input'], $asset['output']);
						break;
				}
			}
		}
	}
	
	/**
	 * Combines and compresses css files.
	 * 
	 * Example:
	 * <code>bin/cake MeTools.compress css webroot/css/layout.css webroot/css/layout.min.css</code>
	 * 
	 * Arguments can be passed from the shell. Otherwise, you can call it as a method, passing 
	 * the input files as the first argument and the output file as the second argument
	 * @uses MeTools\Utility\Unix::which()
	 * @uses _parseArgs()
	 */
	public function css() {		
		//Checks for Clean-css
		if(!($bin = Unix::which('cleancss')))
			return $this->error(__d('me_tools', 'I can\'t find {0}', 'Clean-css'));
		
		if(func_num_args())
			$args = am(is_array(func_get_arg(0)) ? func_get_arg(0) : [func_get_arg(0)], is_array(func_get_arg(1)) ? func_get_arg(1) : [func_get_arg(1)]);
		
		//Gets the input file and the output files
		list($input, $output) = $this->_parseArgs(empty($args) ? $this->args : $args);
		
		if(!$input || !$output)
			return;
		
		$created = (bool) !file_exists($output);
		
		//Executes the command
		exec(sprintf('%s -o %s %s', $bin, $output, implode(' ', $input)));
		
		if($created)
			$this->success(__d('me_tools', 'The file `{0}` has been created', $output));
		else
			$this->success(__d('me_tools', 'The file `{0}` has been updated', $output));
	}

	/**
	 * Combines and compresses js files.
	 * 
	 * Example:
	 * <code>bin/cake MeTools.compress js webroot/js/scripts.js webroot/js/scripts.min.js</code>
	 * 
	 * Arguments can be passed from the shell. Otherwise, you can call it as a method, passing 
	 * the input files as the first argument and the output file as the second argument
	 * @return bool
	 * @uses MeTools\Utility\Unix::which()
	 * @uses _parseArgs()
	 */
	public function js() {
		if(!($bin = Unix::which('uglifyjs')))
			return $this->error(__d('me_tools', 'I can\'t find {0}', 'UglifyJS'));
		
		if(func_num_args())
			$args = am(is_array(func_get_arg(0)) ? func_get_arg(0) : [func_get_arg(0)], is_array(func_get_arg(1)) ? func_get_arg(1) : [func_get_arg(1)]);
		
		//Gets the input file and the output files
		list($input, $output) = $this->_parseArgs(empty($args) ? $this->args : $args);
		
		if(!$input || !$output)
			return;
		
		$created = (bool) !file_exists($output);
		
		//Sets the comments option for UglifyJS
		$comments = '/!|@[Ll]icen[sc]e|@[Pp]reserve/';
		
		//Executes the command
		exec(sprintf('%s --mangle --comments "%s" -o %s %s', $bin, $comments, $output, implode(' ', $input)));
		
		if($created)
			$this->success(__d('me_tools', 'The file `{0}` has been created', $output));
		else
			$this->success(__d('me_tools', 'The file `{0}` has been updated', $output));
	}
}