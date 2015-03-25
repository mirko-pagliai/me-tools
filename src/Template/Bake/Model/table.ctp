<?php
namespace <%= $namespace %>\Model\Table;

<%
$uses = [
    "use $namespace\\Model\\Entity\\$entity;",
    'use Cake\ORM\Query;',
    'use Cake\ORM\RulesChecker;',
    'use Cake\ORM\Table;',
    'use Cake\Validation\Validator;'
];
sort($uses);
echo implode("\n", $uses);
%>


/**
 * <%= $name %> model
 */
class <%= $name %>Table extends Table {
    /**
     * Initialize method
     * @param array $config The table configuration
     */
    public function initialize(array $config) {
<% if (!empty($table)): %>
        $this->table('<%= $table %>');
<% endif %>
<% if (!empty($displayField)): %>
        $this->displayField('<%= $displayField %>');
<% endif %>
<% if (!empty($primaryKey)): %>
<% if (count($primaryKey) > 1): %>
        $this->primaryKey([<%= $this->Bake->stringifyList((array)$primaryKey, ['indent' => false]) %>]);
<% else: %>
        $this->primaryKey('<%= current((array)$primaryKey) %>');
<% endif %>
<% endif %>
<% foreach ($behaviors as $behavior => $behaviorData): %>
        $this->addBehavior('<%= $behavior %>'<%= $behaviorData ? ", [" . implode(', ', $behaviorData) . ']' : '' %>);
<% endforeach %>
<% foreach ($associations as $type => $assocs): %>
<% foreach ($assocs as $assoc):
	$alias = $assoc['alias'];
	unset($assoc['alias']);
%>
        $this-><%= $type %>('<%= $alias %>', [<%= $this->Bake->stringifyList($assoc, ['indent' => 3]) %>]);
<% endforeach %>
<% endforeach %>
    }
<% if (!empty($validation)): %>

    /**
     * Default validation rules
     * @param \Cake\Validation\Validator $validator Validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator) {
        $validator
<% $validationMethods = []; %>
<%
foreach ($validation as $field => $rules):
    foreach ($rules as $ruleName => $rule):
        if ($rule['rule'] && !isset($rule['provider'])):
            $validationMethods[] = sprintf(
                "->add('%s', '%s', ['rule' => '%s'])",
                $field,
                $ruleName,
                $rule['rule']
            );
        elseif ($rule['rule'] && isset($rule['provider'])):
            $validationMethods[] = sprintf(
                "->add('%s', '%s', ['rule' => '%s', 'provider' => '%s'])",
                $field,
                $ruleName,
                $rule['rule'],
                $rule['provider']
            );
        endif;

        if (isset($rule['allowEmpty'])):
            if (is_string($rule['allowEmpty'])):
                $validationMethods[] = sprintf(
                    "->allowEmpty('%s', '%s')",
                    $field,
                    $rule['allowEmpty']
                );
            elseif ($rule['allowEmpty']):
                $validationMethods[] = sprintf(
                    "->allowEmpty('%s')",
                    $field
                );
            else:
                $validationMethods[] = sprintf(
                    "->requirePresence('%s', 'create')",
                    $field
                );
                $validationMethods[] = sprintf(
                    "->notEmpty('%s')",
                    $field
                );
            endif;
        endif;
    endforeach;
endforeach;
%>
<%= "            " . implode("\n            ", $validationMethods) . ";" %>


        return $validator;
    }
<% endif %>
<% if (!empty($rulesChecker)): %>

    /**
     * Returns a rules checker object that will be used for validating application integrity
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules) {
    <%- foreach ($rulesChecker as $field => $rule): %>
        $rules->add($rules-><%= $rule['name'] %>(['<%= $field %>']<%= !empty($rule['extra']) ? ", '$rule[extra]'" : '' %>));
    <%- endforeach; %>
        return $rules;
    }
<% endif; %>
}