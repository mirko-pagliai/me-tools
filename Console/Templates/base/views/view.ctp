<?php
	echo "<?php\n";
	echo "\t\$this->start('sidebar');\n";
	echo "\t\techo \$this->Html->li(\$this->Html->link(__d('me_cms_backend', 'Edit ".strtolower($singularHumanName)."'), array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])));\n";
	echo "\t\techo \$this->Html->li(\$this->Form->postLink(__d('me_cms_backend', 'Delete ".strtolower($singularHumanName)."'), array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), NULL, __d('me_cms_backend', 'Are you sure you want to delete this record?')));\n";
	echo "\t\techo \$this->Html->li(\$this->Html->link(__d('me_cms_backend', 'List ".strtolower($pluralHumanName)."'), array('action' => 'index')));\n";
	echo "\t\techo \$this->Html->li(\$this->Html->link(__d('me_cms_backend', 'Add ".strtolower($singularHumanName)."'), array('action' => 'add')));\n";

	$done = array();
	foreach($associations as $type => $data)
		foreach($data as $alias => $details)
			if($details['controller']!=$this->name && !in_array($details['controller'], $done)) {
				echo "\t\techo \$this->Html->li(\$this->Html->link(__d('me_cms_backend', 'List ".strtolower(Inflector::humanize($details['controller']))."'), array('controller' => '{$details['controller']}', 'action' => 'index')));\n";
				echo "\t\techo \$this->Html->li(\$this->Html->link(__d('me_cms_backend', 'Add ".strtolower(Inflector::humanize(Inflector::underscore($alias)))."'), array('controller' => '{$details['controller']}', 'action' => 'add')));\n";
				$done[] = $details['controller'];
			}
			
	echo "\t\$this->end();\n";
	echo "?>\n";
?>

<div class="<?php echo $pluralVar; ?> view">
	<?php echo "<?php echo \$this->Html->h2(__d('me_cms_backend', '".ucfirst(strtolower($singularHumanName))."')); ?>\n"; ?>
	
	<div class="view-buttons">
		<?php echo "<?php echo \$this->Html->linkButton(__d('me_cms_backend', 'Edit'), array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('icon' => 'pencil', 'tooltip' => __d('me_cms_backend', 'Edit'))); ?>\n"; ?>
		<?php echo "<?php echo \$this->Form->postButton(__d('me_cms_backend', 'Delete'), array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('class' => 'btn-danger', 'icon' => 'trash-o', 'tooltip' => __d('me_cms_backend', 'Delete')), __d('me_cms_backend', 'Are you sure you want to delete this ".strtolower($singularHumanName)."?')); ?>\n"; ?>
	</div>
	
	<dl class="dl-horizontal">
<?php
echo "\t\t<?php\n";
foreach($fields as $field) {
	$isKey = FALSE;
	if(!empty($associations['belongsTo']))
		foreach($associations['belongsTo'] as $alias => $details)
			if($field===$details['foreignKey']) {
				$isKey = TRUE;
				echo "\t\t\techo \$this->Html->dt(__d('me_cms_backend', '".Inflector::humanize(Inflector::underscore($alias))."'));\n";
				echo "\t\t\techo \$this->Html->dd(\$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])));\n";
				break;
			}
	if($isKey!==TRUE) {
		echo "\t\t\techo \$this->Html->dt(__d('me_cms_backend', '".Inflector::humanize($field)."'));\n";
		echo "\t\t\techo \$this->Html->dd(\${$singularVar}['{$modelClass}']['{$field}']);\n";
	}
}
echo "\t\t?>\n";
?>
	</dl>
</div><?php if(!empty($associations['hasOne'])) :
	foreach($associations['hasOne'] as $alias => $details): ?>
	<div class="related">
		<?php echo "<?php echo \$this->Html->h3(__d('me_cms_backend', 'Related ".Inflector::humanize($details['controller'])."')); ?>\n"; ?>
	<?php echo "<?php if(!empty(\${$singularVar}['{$alias}'])): ?>\n"; ?>
		<dl>
	<?php
		echo "\t\t<?php\n";
			foreach($details['fields'] as $field) {
				echo "\t\t\techo \$this->Html->dt(__d('me_cms_backend', '".Inflector::humanize($field)."'));\n";
				echo "\t\t\techo \$this->Html->dd(\${$singularVar}['{$alias}']['{$field}']);\n";
			}
		echo "\t\t?>\n"
	?>
		</dl>
	<?php echo "<?php endif; ?>\n"; ?>
		<div class="actions">
			<ul>
				<li><?php echo "<?php echo \$this->Html->link(__d('me_cms_backend', 'Edit ".Inflector::humanize(Inflector::underscore($alias))."'), array('controller' => '{$details['controller']}', 'action' => 'edit', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?></li>\n"; ?>
			</ul>
		</div>
	</div>
	<?php
	endforeach;
endif;
if(empty($associations['hasMany']))
	$associations['hasMany'] = array();
if(empty($associations['hasAndBelongsToMany']))
	$associations['hasAndBelongsToMany'] = array();
	
$relations = array_merge($associations['hasMany'], $associations['hasAndBelongsToMany']);
$i = 0;
foreach ($relations as $alias => $details):
	$otherSingularVar = Inflector::variable($alias);
	$otherPluralHumanName = Inflector::humanize($details['controller']);
	?>
<?php echo "\n\n<?php if(!empty(\${$singularVar}['{$alias}'])): ?>\n"; ?>
	<div class="related">
		<?php echo "<?php echo \$this->Html->h3(__d('me_cms_backend', 'Related ".strtolower($otherPluralHumanName)."')); ?>\n"; ?>
		<div class="btn-group pull-right margin-10">
			<?php echo "<?php echo \$this->Html->linkButton(__d('me_cms_backend', 'New ".strtolower(Inflector::humanize(Inflector::underscore($alias)))."'), array('controller' => '{$details['controller']}', 'action' => 'add'), array('icon' => 'plus')); ?>\n"; ?>
		</div>
		
		<table class="table table-striped table-bordered">
			<tr>
				<th></th>
<?php
			foreach($details['fields'] as $field)
				echo "\t\t\t\t<th><?php echo __d('me_cms_backend', '".Inflector::humanize($field)."'); ?></th>\n";
?>
			</tr>
	<?php
	echo "\t\t<?php \$i = 0; foreach(\${$singularVar}['{$alias}'] as \${$otherSingularVar}): ?>\n";
			echo "\t\t\t\t<tr>\n";
				echo "\t\t\t\t\t<td class=\"actions\">\n";
				echo "\t\t\t\t\t\t<?php echo \$this->Html->linkButton(NULL, array('controller' => '{$details['controller']}', 'action' => 'view', \${$otherSingularVar}['{$details['primaryKey']}']), array('icon' => 'eye', 'tooltip' => __d('me_cms_backend', 'View'))); ?>\n";
				echo "\t\t\t\t\t\t<?php echo \$this->Html->linkButton(NULL, array('controller' => '{$details['controller']}', 'action' => 'edit', \${$otherSingularVar}['{$details['primaryKey']}']), array('icon' => 'pencil', 'tooltip' => __d('me_cms_backend', 'Edit'))); ?>\n";
				echo "\t\t\t\t\t\t<?php echo \$this->Form->postButton(NULL, array('controller' => '{$details['controller']}', 'action' => 'delete', \${$otherSingularVar}['{$details['primaryKey']}']), array('class' => 'btn-danger', 'icon' => 'trash-o', 'tooltip' => __d('me_cms_backend', 'Delete')), __d('me_cms_backend', 'Are you sure you want to delete this record?')); ?>\n";
				echo "\t\t\t\t\t</td>\n";
				
				foreach($details['fields'] as $field)
					echo "\t\t\t\t\t<td><?php echo \${$otherSingularVar}['{$field}']; ?></td>\n";
			echo "\t\t\t\t</tr>\n";

	echo "\t\t\t<?php endforeach; ?>\n";
	?>
		</table>
	</div>
<?php 
	echo "<?php endif; ?>";
	endforeach;
?>