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
namespace MeTools\Utility;

use Cake\Utility\Xml as BaseXml;

/**
 * An utility to handle XML files and strings.
 * 
 * You can use this utility by adding:
 * <code>
 * use MeTools\Utility\Xml;
 * </code>
 */
class Xml {
	/**
	 * Transform an array into a SimpleXMLElement
	 * 
	 * If the input array doesn't have a root element, this will be added automatically.
	 * @param array|\Cake\Collection\Collection $input Array with data or a collection instance
	 * @param array $options The options to use or a string to use as format
	 * @return \SimpleXMLElement|\DOMDocument SimpleXMLElement or DOMDocument
	 * @throws \Cake\Utility\Exception\XmlException
	 * @uses Cake\Utility\Xml::fromArray()
	 */
	public static function fromArray($input, array $options = []) {		
		//Adds the root element, if it doesn't exist
		if(count($input) > 1 || empty($input['root']))
			$input = ['root' => $input];
			
		$xmlObject = BaseXml::fromArray($input, am(['pretty' => TRUE], $options));
		return $xmlObject->asXML();
	}
	
	/**
	 * Gets an XML file (passed as a url or path) and returns it as an array.
	 * If the path is relative, it will be relative to `APP`.
     * @param string $file XML url/path
	 * @return array Array representation of the XML
	 * @uses toArray()
	 */
	public static function fromFile($file) {
		//If the path is an url, sets the context
		if(is_remote($file)) {
			$context = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 5]]);
			
			if(empty($content))
				return FALSE;
		}
		//Else, if the path is a relative path, then the path will be relative to the APP
		elseif(!realpath($file))
			$path = APP.$file;
		
		$content = file_get_contents($file, FALSE, empty($context) ? NULL : $context);
		
		if(is_json($content))
			return json_decode($content, TRUE);
		
		return empty($content) ? FALSE : self::toArray($content);
	}
	
	/**
	 * Returns an XML structure as an array
	 * @param SimpleXMLElement|DOMDocument|DOMNode $xml SimpleXMLElement, DOMDocument or DOMNode instance 
	 * @return array Array representation of the XML structure
	 * @uses Cake\Utility\Xml::build()
	 * @uses Cake\Utility\Xml::toArray()
	 */
	public static function toArray($xml) {
		$xml = BaseXml::toArray(BaseXml::build($xml));
		
		if(!is_array($xml))
			return;

		//If the array has a root element, it returns the array without the root element
		return count($xml) > 1 ? $xml : fv($xml);
	}
}