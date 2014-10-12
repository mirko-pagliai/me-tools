<?php
	echo "<?php\n";
	echo "\t\$this->start('sidebar');\n";
	
	if(strpos($action, 'add')===FALSE):
		echo "\t\techo \$this->Html->li(\$this->Form->postLink(__('Delete'), array('action' => 'delete', \$this->Form->value('{$modelClass}.{$primaryKey}')), NULL, __('Are you sure you want to delete this record?')));\n";
	endif;
		echo "\t\techo \$this->Html->li(\$this->Html->link(__('List ".strtolower($pluralHumanName)."'), array('action' => 'index')));\n";

	$done = array();
	foreach($associations as $type => $data)
		foreach($data as $alias => $details)
			if($details['controller']!=$this->name && !in_array($details['controller'], $done)) {
				echo "\t\techo \$this->Html->li(\$this->Html->link(__('List ".strtolower(Inflector::humanize($details['controller']))."'), array('controller' => '{$details['controller']}', 'action' => 'index')));\n";
				echo "\t\techo \$this->Html->li(\$this->Html->link(__('Add ".strtolower(Inflector::humanize(Inflector::underscore($alias)))."'), array('controller' => '{$details['controller']}', 'action' => 'add')));\n";
				$done[] = $details['controller'];
			}
	
	echo "\t\$this->end();\n";
	echo "?>\n";
?>

<div class="<?php echo $pluralVar; ?> form">
	<?php printf("<?php echo \$this->Html->h2(__('%s %s')); ?>\n", Inflector::humanize(preg_replace('/(admin_|manager_|_)/', '', $action)), strtolower($singularHumanName)); ?>
	<?php echo "<?php echo \$this->Form->create('{$modelClass}'); ?>\n"; ?>
		<fieldset>
			<?php
				echo "<?php\n";
				foreach ($fields as $field) {
					if(strpos($action, 'add') !== FALSE && $field == $primaryKey)
						continue;
					elseif(preg_match('/^.+_count$/', $field))
						continue;
					elseif(!in_array($field, array('created', 'modified', 'updated')))
						echo "\t\t\t\techo \$this->Form->input('{$field}');\n";
				}
				if(!empty($associations['hasAndBelongsToMany']))
					foreach($associations['hasAndBelongsToMany'] as $assocName => $assocData)
						echo "\t\t\t\techo \$this->Form->input('{$assocName}');\n";
				echo "\t\t\t?>\n";
			?>
		</fieldset>
	<?php echo "<?php echo \$this->Form->end(__('".sprintf('%s %s', Inflector::humanize(preg_replace('/(admin_|manager_|_)/', '', $action)), strtolower($singularHumanName))."')); ?>\n"; ?>
</div>