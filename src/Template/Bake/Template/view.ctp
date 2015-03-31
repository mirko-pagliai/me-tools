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
use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'] + $associations['HasOne'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
            }
        }
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = $schema->columnType($field);
        if (isset($associationFields[$field])) {
            return 'string';
        }
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger'])) {
            return 'number';
        }
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        }
        return in_array($type, ['text', 'boolean']) ? $type : 'string';
    })
    ->toArray();

$groupedFields += ['number' => [], 'string' => [], 'boolean' => [], 'date' => [], 'text' => []];
$pk = "\$$singularVar->{$primaryKey[0]}";
%>
<?php $this->start('sidebar'); ?>
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit <%= $singularHumanName %>'), ['action' => 'edit', <%= $pk %>]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete <%= $singularHumanName %>'), ['action' => 'delete', <%= $pk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>)]) ?> </li>
        <li><?= $this->Html->link(__('List <%= $pluralHumanName %>'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New <%= $singularHumanName %>'), ['action' => 'add']) ?> </li>
<%
    $done = [];
    foreach ($associations as $type => $data) {
        foreach ($data as $alias => $details) {
            if ($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
%>
        <li><?= $this->Html->link(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New <%= Inflector::humanize(Inflector::singularize(Inflector::underscore($alias))) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add']) ?> </li>
<%
                $done[] = $details['controller'];
            }
        }
    }
%>
    </ul>
<?php $this->end('sidebar'); ?>

<div class="<%= $pluralVar %> view">
    <h2><?= h($<%= $singularVar %>-><%= $displayField %>) ?></h2>
    <div class="row">
<% if ($groupedFields['string']) : %>
        <div class="large-5 columns strings">
<% foreach ($groupedFields['string'] as $field) : %>
<% if (isset($associationFields[$field])) :
            $details = $associationFields[$field];
%>
            <h6 class="subheader"><?= __('<%= Inflector::humanize($details['property']) %>') ?></h6>
            <p><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></p>
<% else : %>
            <h6 class="subheader"><?= __('<%= Inflector::humanize($field) %>') ?></h6>
            <p><?= h($<%= $singularVar %>-><%= $field %>) ?></p>
<% endif; %>
<% endforeach; %>
        </div>
<% endif; %>
<% if ($groupedFields['number']) : %>
        <div class="large-2 columns numbers end">
<% foreach ($groupedFields['number'] as $field) : %>
            <h6 class="subheader"><?= __('<%= Inflector::humanize($field) %>') ?></h6>
            <p><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></p>
<% endforeach; %>
        </div>
<% endif; %>
<% if ($groupedFields['date']) : %>
        <div class="large-2 columns dates end">
<% foreach ($groupedFields['date'] as $field) : %>
            <h6 class="subheader"><%= "<%= __('" . Inflector::humanize($field) . "') %>" %></h6>
            <p><?= h($<%= $singularVar %>-><%= $field %>) ?></p>
<% endforeach; %>
        </div>
<% endif; %>
<% if ($groupedFields['boolean']) : %>
        <div class="large-2 columns booleans end">
<% foreach ($groupedFields['boolean'] as $field) : %>
            <h6 class="subheader"><?= __('<%= Inflector::humanize($field) %>') ?></h6>
            <p><?= $<%= $singularVar %>-><%= $field %> ? __('Yes') : __('No'); ?></p>
<% endforeach; %>
        </div>
<% endif; %>
    </div>
<% if ($groupedFields['text']) : %>
<% foreach ($groupedFields['text'] as $field) : %>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('<%= Inflector::humanize($field) %>') ?></h6>
            <?= $this->Text->autoParagraph(h($<%= $singularVar %>-><%= $field %>)); ?>

        </div>
    </div>
<% endforeach; %>
<% endif; %>
</div>
<%
$relations = $associations['HasMany'] + $associations['BelongsToMany'];
foreach ($relations as $alias => $details):
    $otherSingularVar = Inflector::variable($alias);
    $otherPluralHumanName = Inflector::humanize($details['controller']);
    %>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Related <%= $otherPluralHumanName %>') ?></h4>
    <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
<% foreach ($details['fields'] as $field): %>
            <th><?= __('<%= Inflector::humanize($field) %>') ?></th>
<% endforeach; %>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>): ?>
        <tr>
            <%- foreach ($details['fields'] as $field): %>
            <td><?= h($<%= $otherSingularVar %>-><%= $field %>) ?></td>
            <%- endforeach; %>

            <%- $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}"; %>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['controller' => '<%= $details['controller'] %>', 'action' => 'view', <%= $otherPk %>]) %>
                <?= $this->Html->link(__('Edit'), ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', <%= $otherPk %>]) %>
                <?= $this->Form->postLink(__('Delete'), ['controller' => '<%= $details['controller'] %>', 'action' => 'delete', <%= $otherPk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $otherPk %>)]) %>
            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
<% endforeach; %>
