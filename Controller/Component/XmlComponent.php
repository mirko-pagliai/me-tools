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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
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
     * Gets an XML file (remote or local) and returns it as an array
     * @param string $url XML url or path
     * @return mixed Array or NULL
     * @see http://repository.novatlantis.it/metools-sandbox/xml/xmlasarray Examples
     */
    public function get($url) {
        //If it's not a url (but it's a path) and if it's a relative path, the path is relative to APP
        if(!filter_var($url, FILTER_VALIDATE_URL) && !realpath($url))
            $url = APP.$url;

        if(@file_get_contents($url)) {
            $xml = Xml::toArray(Xml::build($url));

            //If the array has only one item, returns the first one, otherwise the whole array
            return count($xml) > 1 ? $xml : array_shift($xml);
        }
    }
}