<?php
App::uses('WysiwygHelper', 'MeTools.View/Helper');

/**
 * Creates form inputs to be used with CKEditor.
 * 
 * The `load()` method allows you to load all CKEditor scripts and the `textarea()` 
 * method (and its `input()` alias) creates a textarea for CKEditor.
 * 
 * This helper looking for CKEditor in `app/webroot/ckeditor` and `app/webroot/js/ckeditor`.
 * 
 * So it looks for the file `ckeditor.js` (this file is used to instantiate CKEditor) in `app/webroot/js`, 
 * `app/webroot/ckeditor`, `app/webroot/js/ckeditor` and `app/Plugin/MeTools/webroot/ckeditor`.
 * 
 * Usually `ckeditor.js` is located in `app/Plugin/MeTools/webroot/js`. 
 * If you want to edit the file, you should copy it to `app/webroot/js`.
 * 
 * You should use this helper as an alias, for example:
 * <pre>public $helpers = array('Wysiwyg' => array('className' => 'MeTools.CKEditor'));</pre>
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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools.View.Helper
 */
class CKEditorHelper extends WysiwygHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array(
		'Form' => array('className' => 'MeTools.MeForm'),
		'Html' => array('className' => 'MeTools.MeHtml')
	);
	
	/**
	 * Loads all CKEditor scripts
	 * @param boolean $jquery FALSE if you don't want to use the jquery adapter
	 * @return mixed String of <script /> tags
	 */
	public function load($jquery=true) {
		$url = '/ckeditor';
		
		//Checks if CKEditor script (ckeditor.js) exists
		//It seeks in app/webroot/ckeditor and app/webroot/js/ckeditor
		if(fileExistsInPath(WWW_ROOT.'ckeditor'.DS.'ckeditor.js'))
			$path = WWW_ROOT.'ckeditor';
		elseif(fileExistsInPath(WWW_ROOT.'js'.DS.'ckeditor'.DS.'ckeditor.js')) {
			$path = WWW_ROOT.'js'.DS.'ckeditor';
			$url = '/js'.$url;
		}
		
		//If CKEditor script exists
		if(!empty($path) && !empty($url)) {
			$script = array($url.'/ckeditor');
			
			//Checks if the jQuery adapter exists
			if(!empty($jquery) && fileExistsInPath($path.DS.'adapters'.DS.'jquery.js'))
				$script[] = $url.'/adapters/jquery';
			
			//Checks if the init script exists
			//It seeks in app/webroot/js, app/webroot/ckeditor, app/webroot/js/ckeditor and app/Plugin/MeTools/webroot/ckeditor
			if(fileExistsInPath(WWW_ROOT.'js'.DS.'ckeditor_init.js'))
				$script[] = 'ckeditor_init';
			elseif(fileExistsInPath($path.DS.'ckeditor_init.js'))
				$script[] = $url.'/ckeditor_init';
			elseif(fileExistsInPath(App::pluginPath('MeTools').'webroot'.DS.'ckeditor'.DS.'ckeditor_init.js'))
				$script[] = '/MeTools/ckeditor/ckeditor_init';
			else
				return false;
			
			return $this->Html->js($script);
		}
		
		return false;
	}
}