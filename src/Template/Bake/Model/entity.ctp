<?php
namespace <%= $namespace %>\Model\Entity;

use Cake\ORM\Entity;

/**
 * <%= $name %> entity
 */
class <%= $name %> extends Entity {
<% if (!empty($fields)): %>
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity()
     * @var array
     */
    protected $_accessible = [
<% foreach ($fields as $field): %>
        '<%= $field %>' => TRUE,
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
<% if (empty($fields) && empty($hidden)): %>

<% endif %>
}