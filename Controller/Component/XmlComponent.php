<?php

/**
 * XmlComponent
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
 * @package		MeTools\Controller\Component
 * @see         http://repository.novatlantis.it/metools-sandbox/xml/xmlasarray Examples
 */
App::uses('Xml', 'Utility');

/**
 * A component to handle XML.
 */
class XmlComponent extends Component {
	/**
	 * Transforms an array into a SimpleXMLElement.
	 * 
	 * If the input array doesn't have a root element, this will be added automatically
	 * @param array $array Input array with data
	 * @param array $options Options
	 * @return object SimpleXMLElement
	 * @see http://api.cakephp.org/2.5/source-class-Xml.html#154-224 CakePHP documentation
	 */
	public function fromArray($array, $options = array()) {
		if(empty($array))
			return FALSE;
		
		//Adds the root element, if it doesn't exist
		if(count($array) > 1)
			$array = array('root' => $array);
			
		$xml = Xml::fromArray($array, am(array('pretty' => TRUE), $options));
		return $xml->asXML();
	}

    /**
     * Alias for `toArray()` method
     * @see fromArray()
     */
    public function fromXml() {
        return call_user_func_array(array(get_class(), 'toArray'), func_get_args());
    }
	
	/**
	 * Gets an XML file (passed as a url or path) and returns it as an array.
	 * 
	 * If the path is relative, it will be relative to APP.
     * @param string $path XML url/path
	 * @return array Array representation of the XML
	 * @uses toArray() to returns the XML structure as an array
	 */
	public function getAsArray($path) {		
		//If the path is an url, sets the context
		if(filter_var($path, FILTER_VALIDATE_URL))
			$context = stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 5)));
		//Else, if the path is a relative path, then the path will be relative to the APP
		elseif(!realpath($path))
			$path = APP.$path;
		
		$content = @file_get_contents($path, FALSE, empty($context) ? NULL : $context);
		
		return empty($content) ? FALSE : self::toArray($content);
	}
	
	/**
	 * Returns an XML structure as an array
	 * @param object $xml SimpleXMLElement
	 * @return array Array representation of the XML
	 */
	public function toArray($xml) {
		$xml = Xml::toArray(Xml::build($xml));
		
		if(!is_array($xml))
			return FALSE;

		//If the array has a root element, it returns the array without the root element
		return count($xml) > 1 ? $xml : array_values($xml)[0];
	}

    /**
     * Alias for `fromArray()` method
     * @see fromArray()
     */
    public function toXml() {
        return call_user_func_array(array(get_class(), 'fromArray'), func_get_args());
    }
}