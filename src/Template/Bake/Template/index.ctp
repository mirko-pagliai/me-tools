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

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return !in_array($schema->columnType($field), ['binary', 'text']);
    })
    ->take(7);
%>
<div class="actions col-md-3 col-lg-2">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New <%= $singularHumanName %>'), ['action' => 'add']) ?></li>
<%
    $done = [];
    foreach ($associations as $type => $data):
        foreach ($data as $alias => $details):
            if ($details['controller'] != $this->name && !in_array($details['controller'], $done)):
%>
        <li><?= $this->Html->link(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New <%= $this->_singularHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add']) ?> </li>
<%
                $done[] = $details['controller'];
            endif;
        endforeach;
    endforeach;
%>
    </ul>
</div>
<div class="<%= $pluralVar %> index col-md-9 col-lg-10">
    <table class="table">
		<thead>
			<tr>
	<% foreach ($fields as $field): %>
			<th><?= $this->Paginator->sort('<%= $field %>') ?></th>
	<% endforeach; %>
			<th class="actions"><?= __('Actions') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($<%= $pluralVar %> as $<%= $singularVar %>): ?>
				<tr>
		<%        foreach ($fields as $field) {
					$isKey = false;
					if (!empty($associations['BelongsTo'])) {
						foreach ($associations['BelongsTo'] as $alias => $details) {
							if ($field === $details['foreignKey']) {
								$isKey = true;
		%>
					<td>
						<?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?>
					</td>
		<%
								break;
							}
						}
					}
					if ($isKey !== true) {
						if (!in_array($schema->columnType($field), ['integer', 'biginteger', 'decimal', 'float'])) {
		%>
			<td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
		<%
						} else {
		%>
			<td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
		<%
						}
					}
				}

				$pk = '$' . $singularVar . '->' . $primaryKey[0];
		%>
			<td class="actions">
						<?= $this->Html->button(__('View'), ['action' => 'view', <%= $pk %>], ['icon' => 'eye']) ?>
						<?= $this->Html->button(__('Edit'), ['action' => 'edit', <%= $pk %>], ['icon' => 'pencil']) ?>
						<?= $this->Form->postButton(__('Delete'), ['action' => 'delete', <%= $pk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>), 'icon' => 'trash']) ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
    </table>
	<?= $this->element('MeTools.paginator'); ?>
</div>