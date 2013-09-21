<?php echo "<?php echo \$this->start('sidebar'); ?>\n"; ?>
<?php
	echo "\t<li><?php echo \$this->Html->link(__('Edit ".strtolower($singularHumanName)."'), array('action' => 'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])); ?></li>\n";
	echo "\t<li><?php echo \$this->Form->postLink(__('Delete ".strtolower($singularHumanName)."'), array('action' => 'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']), null, __('Are you sure you want to delete this record?')); ?></li>\n";
	echo "\t<li><?php echo \$this->Html->link(__('List ".strtolower($pluralHumanName)."'), array('action' => 'index')); ?></li>\n";
	echo "\t<li><?php echo \$this->Html->link(__('Add ".strtolower($singularHumanName)."'), array('action' => 'add')); ?></li>\n";

	$done = array();
	foreach($associations as $type => $data)
		foreach($data as $alias => $details)
			if($details['controller']!=$this->name && !in_array($details['controller'], $done)) {
				echo "\t<li><?php echo \$this->Html->link(__('List ".strtolower(Inflector::humanize($details['controller']))."'), array('controller' => '{$details['controller']}', 'action' => 'index')); ?></li>\n";
				echo "\t<li><?php echo \$this->Html->link(__('Add ".strtolower(Inflector::humanize(Inflector::underscore($alias)))."'), array('controller' => '{$details['controller']}', 'action' => 'add')); ?></li>\n";
				$done[] = $details['controller'];
			}
?>
<?php echo "<?php echo \$this->end(); ?>\n"; ?>

<div class="<?php echo $pluralVar; ?> view">
	<h2><?php echo "<?php echo __('{$singularHumanName}'); ?>"; ?></h2>
	<dl class="dl-horizontal">
<?php
foreach($fields as $field) {
	$isKey = false;
	if(!empty($associations['belongsTo']))
		foreach($associations['belongsTo'] as $alias => $details)
			if($field===$details['foreignKey']) {
				$isKey = true;
				echo "\t\t<dt><?php echo __('".Inflector::humanize(Inflector::underscore($alias))."'); ?></dt>\n";
				echo "\t\t<dd><?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?></dd>\n";
				break;
			}
	if($isKey!==true) {
		echo "\t\t<dt><?php echo __('".Inflector::humanize($field)."'); ?></dt>\n";
		echo "\t\t<dd><?php echo \${$singularVar}['{$modelClass}']['{$field}']; ?></dd>\n";
	}
}
?>
	</dl>
</div><?php if(!empty($associations['hasOne'])) :
	foreach($associations['hasOne'] as $alias => $details): ?>
	<div class="related">
		<h3><?php echo "<?php echo __('Related ".Inflector::humanize($details['controller'])."'); ?>"; ?></h3>
	<?php echo "<?php if(!empty(\${$singularVar}['{$alias}'])): ?>\n"; ?>
		<dl>
	<?php
			foreach($details['fields'] as $field) {
				echo "\t\t<dt><?php echo __('".Inflector::humanize($field)."'); ?></dt>\n";
				echo "\t\t<dd>\n\t<?php echo \${$singularVar}['{$alias}']['{$field}']; ?>\n&nbsp;</dd>\n";
			}
	?>
		</dl>
	<?php echo "<?php endif; ?>\n"; ?>
		<div class="actions">
			<ul>
				<li><?php echo "<?php echo \$this->Html->link(__('Edit ".Inflector::humanize(Inflector::underscore($alias))."'), array('controller' => '{$details['controller']}', 'action' => 'edit', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?></li>\n"; ?>
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
		<h3><?php echo "<?php echo __('Related " . $otherPluralHumanName . "'); ?>"; ?></h3>
		<div class="btn-group pull-right">
			<?php echo "<?php echo \$this->Html->linkButton(__('New ".Inflector::humanize(Inflector::underscore($alias))."'), array('controller' => '{$details['controller']}', 'action' => 'add'), array('icon' => 'icon-plus')); ?>\n"; ?>
		</div>
		<table class="table table-striped table-bordered">
			<tr>
				<th></th>
<?php
			foreach($details['fields'] as $field)
				echo "\t\t\t\t<th><?php echo __('" . Inflector::humanize($field) . "'); ?></th>\n";
?>
			</tr>
	<?php
	echo "\t\t<?php \$i = 0; foreach(\${$singularVar}['{$alias}'] as \${$otherSingularVar}): ?>\n";
			echo "\t\t\t\t<tr>\n";
				echo "\t\t\t\t\t<td class=\"actions\">\n";
				echo "\t\t\t\t\t\t<div class=\"btn-group\">\n";
				echo "\t\t\t\t\t\t\t<?php echo \$this->Html->linkButton(null, array('controller' => '{$details['controller']}', 'action' => 'view', \${$otherSingularVar}['{$details['primaryKey']}']), array('icon' => 'icon-eye-open', 'tooltip' => __('View'))); ?>\n";
				echo "\t\t\t\t\t\t\t<?php echo \$this->Html->linkButton(null, array('controller' => '{$details['controller']}', 'action' => 'edit', \${$otherSingularVar}['{$details['primaryKey']}']), array('icon' => 'icon-pencil', 'tooltip' => __('Edit'))); ?>\n";
				echo "\t\t\t\t\t\t\t<?php echo \$this->Form->postButton(null, array('controller' => '{$details['controller']}', 'action' => 'delete', \${$otherSingularVar}['{$details['primaryKey']}']), array('class' => 'btn-danger', 'icon' => 'icon-trash', 'tooltip' => __('Delete')), __('Are you sure you want to delete this record?')); ?>\n";
				echo "\t\t\t\t\t\t</div>\n";
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