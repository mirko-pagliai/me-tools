<?php

/**
 * AssetCompressShell
 *
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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Console\Command
 * @see			https://github.com/jakubpawlowicz/clean-css
 * @see			https://github.com/mishoo/UglifyJS2
 */

App::uses('MeToolsAppShell', 'MeTools.Console/Command');
App::uses('System', 'MeTools.Utility');

/**
 * This shell allows you to combine and compress css and js files.
 * 
 * To use this shell, you have to install on your system `Clean-css` and `UglifyJS`. As root user:
 * <code>
 * npm install clean-css -g
 * npm install uglify-js -g
 * </code>
 */
class AssetCompressShell extends MeToolsAppShell {
	/**
	 * Parses arguments or a config file, checks values and returns input and output files
	 * @param string $type `css` or `js`
	 * @return array Input and output files
	 */
	private function _parse($type) {
		//Checks if there are at least 2 arguments or a config file
		if(count($this->args) < 2 && empty($this->params['config']))
			$this->error('you have to indicate at least two arguments, an input file and an output file, or a configuration file by using the option');
		
		//If it needs to use a configuration file
		if(!empty($this->params['config'])) {
			//Checks if the config file is readable
			if(!is_readable($config = $this->params['config']))
				$this->error(sprintf('%s doesn\'t exists or is not readable', $config));
			
			//Sets each line of the configuration file as an argument
			$this->args = explode(PHP_EOL, file_get_contents($config));
			
			//Adds the file extensions
			array_walk($this->args, function(&$filename, $k, $type) {
				$filename = sprintf('%s.%s', $filename, $type);
			}, $type);
						
			//Checks if there are at least 2 files
			if(count($this->args) < 2)
				$this->error('you have to indicate at least two arguments, an input file and an output file');
		}
		
		//Gets the output file and the input files. The last argument is the output file, the other arguments are the input files
		$output = array_pop($this->args);
		$input = is_array($this->args) ? $this->args : array($this->args);
				
		//Checks that all input files exist and are readable 
		foreach($input as $file) {
			if(!is_readable($file))
				$this->error(sprintf('%s doesn\'t exists or is not readable', $file));
		}
		
		//Checks if the output directory is writable
		if(!is_writable(dirname($output)))
			$this->error(sprintf('%s doesn\'t exists or is not writeable', $output));
		
		//If the output file already exists and has not been used the "force" option, it asks if it should be overwritten
		if(file_exists($output) && empty($this->params['force']) && $this->in(sprintf('The file %s already exists. Do you want to overwrite it?', $output), array('y', 'n'), 'y') === 'n') {
			$this->out('Ok, i\'m exiting...');
			exit;
		}
		
		//Returns input and output files
		return array($input, $output);
	}
	
	/**
	 * Combines and compresses css files
	 * @uses _parse()
	 * @uses System::which()
	 */
	public function css() {
		//Checks for Clean-css
		if(!($cleancss = System::which('cleancss')))
			$this->error(sprintf('I can\'t find %s', 'Clean-css'));
		
		//Gets the output file and the input files
		list($input, $output) = $this->_parse('css');
				
		//Executes the command
		exec(sprintf('%s -o %s %s', $cleancss, $output, implode(' ', $input)));
	}
	
	/**
	 * Combines and compresses js files
	 * @uses _parse()
	 * @uses System::which()
	 */
	public function js() {
		if(!($uglifyjs = System::which('uglifyjs')))
			$this->error(sprintf('I can\'t find %s', 'UglifyJS'));
		
		//Gets the output file and the input files
		list($input, $output) = $this->_parse('js');
		
		//Sets the comments option for UglifyJS
		$comments = '/!|@[Ll]icen[sc]e|@[Pp]reserve/';
		
		//Executes the command
		exec(sprintf('%s --mangle --comments "%s" -o %s %s', $uglifyjs, $comments, $output, implode(' ', $input)));
	}
	
	/**
	 * Gets the option parser instance and configures it.
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->addSubcommand('css', array(
			'help' => 'Combines and compresses css files'
		))->addSubcommand('js', array(
			'help' => 'Combines and compresses js files'
		))->addOption('config', array(
			'help'	=> 'Configuration file',
			'short' => 'c'
		))->addOption('force', array(
			'boolean'	=> TRUE,
			'help'		=> 'Force overwriting existing files without prompting',
			'short'		=> 'f'
		));
		
		return $parser;
	}
}