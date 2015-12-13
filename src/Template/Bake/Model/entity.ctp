<%
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

$propertyHintMap = null;
if (!empty($propertySchema)) {
    $propertyHintMap = $this->DocBlock->buildEntityPropertyHintTypeMap($propertySchema);
}

$accessible = [];
if (!isset($fields) || $fields !== false) {
    if (!empty($fields)) {
        foreach ($fields as $field) {
            $accessible[$field] = 'true';
        }
    } elseif (!empty($primaryKey)) {
        $accessible['*'] = 'true';
        foreach ($primaryKey as $field) {
            $accessible[$field] = 'false';
        }
    }
}
%>
<?php
namespace <%= $namespace %>\Model\Entity;

use Cake\ORM\Entity;

/**
 * <%= $name %> entity
<% if ($propertyHintMap): %>
<% foreach ($propertyHintMap as $property => $type): %>
<% if ($type): %>
 * @property <%= $type %> $<%= $property %>
<% else: %>
 * @property $<%= $property %>
<% endif; %>
<% endforeach; %>
<% endif; %>
 */
class <%= $name %> extends Entity {
<% if (!empty($accessible)): %>
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity()
     * @var array
     */
    protected $_accessible = [
<% foreach ($accessible as $field => $value): %>
        '<%= $field %>' => <%= strtoupper($value) %>,
<% endforeach; %>
    ];
<% endif %>
<% if (!empty($hidden)): %>

    /**
     * Fields that are excluded from JSON an array versions of the entity
     * @var array
     */
    protected $_hidden = [<%= $this->Bake->stringifyList($hidden) %>];
<% endif %>
<% if (empty($accessible) && empty($hidden)): %>

<% endif %>
}