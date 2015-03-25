<%
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    });
%>
<div class="actions col-md-3 col-lg-2">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
<% if (strpos($action, 'add') === false): %>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $<%= $singularVar %>-><%= $primaryKey[0] %>],
                ['confirm' => __('Are you sure you want to delete # {0}?', $<%= $singularVar %>-><%= $primaryKey[0] %>)]
            )
        ?></li>
<% endif; %>
        <li><?= $this->Html->link(__('List <%= $pluralHumanName %>'), ['action' => 'index']) ?></li>
<%
        $done = [];
        foreach ($associations as $type => $data) {
            foreach ($data as $alias => $details) {
                if ($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
%>
        <li><?= $this->Html->link(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index']) %> </li>
        <li><?= $this->Html->link(__('New <%= $this->_singularHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add']) %> </li>
<%
                    $done[] = $details['controller'];
                }
            }
        }
%>
    </ul>
</div>
<div class="<%= $pluralVar %> form col-md-9 col-lg-10">
    <?= $this->Form->create($<%= $singularVar %>); ?>
    <fieldset>
        <legend><?= __('<%= Inflector::humanize($action) %> <%= $singularHumanName %>') ?></legend>
        <?php
<%
        foreach ($fields as $field) {
            if (in_array($field, $primaryKey)) {
                continue;
            }
            if (isset($keyFields[$field])) {
                $fieldData = $schema->column($field);
                if (!empty($fieldData['null'])) {
%>
            echo $this->Form->input('<%= $field %>', ['options' => $<%= $keyFields[$field] %>, 'empty' => true]);
<%
                } else {
%>
            echo $this->Form->input('<%= $field %>', ['options' => $<%= $keyFields[$field] %>]);
<%
                }
                continue;
            }
            if (!in_array($field, ['created', 'modified', 'updated'])) {
                $fieldData = $schema->column($field);
                if (($fieldData['type'] === 'date') && (!empty($fieldData['null']))) {
%>
            echo $this->Form->input('<%= $field %>', array('empty' => true, 'default' => ''));
<%
                } else {
%>
            echo $this->Form->input('<%= $field %>');
<%
                }
            }
        }
        if (!empty($associations['BelongsToMany'])) {
            foreach ($associations['BelongsToMany'] as $assocName => $assocData) {
%>
            echo $this->Form->input('<%= $assocData['property'] %>._ids', ['options' => $<%= $assocData['variable'] %>]);
<%
            }
        }
%>
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
