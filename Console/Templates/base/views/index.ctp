<?php
	echo "<?php\n";
	echo "\t\$this->start('sidebar');\n";
	echo "\t\techo \$this->Html->li(\$this->Html->link(__d('me_cms_backend', 'Add ".strtolower($singularHumanName)."'), array('action' => 'add')));\n";

	$done = array();
	foreach($associations as $type => $data) {
		foreach($data as $alias => $details) {
			if($details['controller']!=$this->name && !in_array($details['controller'], $done)) {
				echo "\t\techo \$this->Html->li(\$this->Html->link(__d('me_cms_backend', 'List ".strtolower(Inflector::humanize($details['controller']))."'), array('controller' => '{$details['controller']}', 'action' => 'index')));\n";
				echo "\t\techo \$this->Html->li(\$this->Html->link(__d('me_cms_backend', 'Add ".strtolower(Inflector::humanize(Inflector::underscore($alias)))."'), array('controller' => '{$details['controller']}', 'action' => 'add')));\n";
				$done[] = $details['controller'];
			}
		}
	}
	
	echo "\t\$this->end();\n";
	echo "?>\n";
?>
	
<div class="<?php echo $pluralVar; ?> index">
	<?php echo "<?php echo \$this->Html->h2(__d('me_cms_backend', '".ucfirst(strtolower($pluralHumanName))."')); ?>\n"; ?>
	<table class="table table-striped">
		<tr>
			<th></th>
	<?php foreach($fields as $field): ?>
		<th><?php echo "<?php echo \$this->Paginator->sort('{$field}'); ?>"; ?></th>
	<?php endforeach; ?>
	</tr>
	<?php
		echo "\t<?php foreach(\${$pluralVar} as \${$singularVar}): ?>\n";
			echo "\t\t\t<tr>\n";
				echo "\t\t\t\t<td class=\"actions\">\n";
					echo "\t\t\t\t\t<?php echo \$this->Html->linkButton(NULL, array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('icon' => 'eye', 'tooltip' => __d('me_cms_backend', 'View'))); ?>\n";
					echo "\t\t\t\t\t<?php echo \$this->Html->linkButton(NULL, array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('icon' => 'pencil', 'tooltip' => __d('me_cms_backend', 'Edit'))); ?>\n";
					echo "\t\t\t\t\t<?php echo \$this->Form->postButton(NULL, array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('class' => 'btn-danger', 'icon' => 'trash-o', 'tooltip' => __d('me_cms_backend', 'Delete')), __d('me_cms_backend', 'Are you sure you want to delete this?')); ?>\n";
				echo "\t\t\t\t</td>\n";

				foreach($fields as $field) {
					$isKey = FALSE;
					if(!empty($associations['belongsTo']))
						foreach($associations['belongsTo'] as $alias => $details)
							if($field===$details['foreignKey']) {
								$isKey = TRUE;
								echo "\t\t\t\t<td><?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?></td>\n";
								break;
							}
					if($isKey!==TRUE)
						echo "\t\t\t\t<td><?php echo \${$singularVar}['{$modelClass}']['{$field}']; ?></td>\n";
				}
			echo "\t\t\t</tr>\n";

		echo "\t\t<?php endforeach; ?>\n";
	?>
	</table>
	<?php echo "<?php echo \$this->element('MeTools.paginator'); ?>\n"; ?>
</div>