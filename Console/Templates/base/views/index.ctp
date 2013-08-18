<?php echo "<?php echo \$this->start('sidebar'); ?>\n"; ?>
	<li><?php echo "<?php echo \$this->Html->link(__('New ".$singularHumanName."'), array('action' => 'add')); ?>"; ?></li>
<?php
	$done = array();
	foreach($associations as $type => $data) {
		foreach ($data as $alias => $details) {
			if($details['controller'] != $this->name && !in_array($details['controller'], $done)) {
				echo "\t<li><?php echo \$this->Html->link(__('List ".Inflector::humanize($details['controller'])."'), array('controller' => '{$details['controller']}', 'action' => 'index')); ?></li>\n";
				echo "\t<li><?php echo \$this->Html->link(__('New ".Inflector::humanize(Inflector::underscore($alias))."'), array('controller' => '{$details['controller']}', 'action' => 'add')); ?></li>\n";
				$done[] = $details['controller'];
			}
		}
	}
?>
<?php echo "<?php echo \$this->end(); ?>\n"; ?>
	
<div class="<?php echo $pluralVar; ?> index">
	<h2><?php echo "<?php echo __('{$pluralHumanName}'); ?>"; ?></h2>
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
		echo "\t\t\t\t\t<?php echo \$this->Html->linkButton(null, array('action' => 'view', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('icon' => 'icon-eye-open', 'tooltip' => __('View'))); ?>\n";
		echo "\t\t\t\t\t<?php echo \$this->Html->linkButton(null, array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('icon' => 'icon-pencil', 'tooltip' => __('Edit'))); ?>\n";
		echo "\t\t\t\t\t<?php echo \$this->Form->postButton(null, array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), array('class' => 'btn-danger', 'icon' => 'icon-trash', 'tooltip' => __('Delete')), __('Are you sure you want to delete this record?')); ?>\n";
		echo "\t\t\t\t</td>\n";
		
		foreach($fields as $field) {
			$isKey = false;
			if(!empty($associations['belongsTo'])) {
				foreach($associations['belongsTo'] as $alias => $details) {
					if($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t\t\t<td><?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?></td>\n";
						break;
					}
				}
			}
			if($isKey !== true)
				echo "\t\t\t\t<td><?php echo \${$singularVar}['{$modelClass}']['{$field}']; ?></td>\n";
		}
	echo "\t\t\t</tr>\n";

	echo "\t\t<?php endforeach; ?>\n";
	?>
	</table>
	<?php echo "<?php echo \$this->element('MeTools.paginator'); ?>\n"; ?>
</div>