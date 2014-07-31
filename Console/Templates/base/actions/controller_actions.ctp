<?php
/**
 * Bake Template for Controller action generation.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Console.Templates.default.actions
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>

	/**
	 * List <?php echo strtolower(Inflector::pluralize($singularHumanName))."\n"; ?>
	 * @return void
	 */
	public function <?php echo $admin ?>index() {
		$this-><?php echo $currentModelName ?>->recursive = 0;
		
		$this->set(array(
			'<?php echo $pluralName ?>'		=> $this->paginate(),
			'subtitle'	=> <?php echo "__('".Inflector::pluralize($singularHumanName)."')\n"; ?>
		));
	}

	/**
	 * View <?php echo strtolower($singularHumanName)."\n"; ?>
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function <?php echo $admin ?>view($id = NULL) {
		if(!$this-><?php echo $currentModelName; ?>->exists($id))
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
			
		$this->set('<?php echo $singularName; ?>', $this-><?php echo $currentModelName; ?>->find('first', array(
			'conditions' => array('<?php echo $currentModelName; ?>.'.$this-><?php echo $currentModelName; ?>->primaryKey => $id)
		)));
		<?php echo "\$this->set('subtitle', __('View ".strtolower($singularHumanName)."'));\n"; ?>
	}

<?php $compact = array(); ?>
	/**
	 * Add <?php echo strtolower($singularHumanName)."\n"; ?>
	 * @return void
	 */
	public function <?php echo $admin ?>add() {
		if($this->request->is('post')) {
			$this-><?php echo $currentModelName; ?>->create();
			if($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if($wannaUseSession): ?>
				$this->Session->flash(__('The <?php echo strtolower($singularHumanName); ?> has been created'));
				$this->redirect(array('action' => 'index'));
<?php else: ?>
				$this->flash(__('<?php echo ucfirst(strtolower($currentModelName)); ?> saved.'), array('action' => 'index'));
<?php endif; ?>
			}
<?php if($wannaUseSession): ?>
			else
				$this->Session->flash(__('The <?php echo strtolower($singularHumanName); ?> could not be created. Please, try again'), 'error');
<?php endif; ?>
		}
<?php
	foreach(array('belongsTo', 'hasAndBelongsToMany') as $assoc):
		foreach($modelObj->{$assoc} as $associationName => $relation):
			if(!empty($associationName)):
				$otherModelName = $this->_modelName($associationName);
				$otherPluralName = $this->_pluralName($associationName);
				echo "\n\t\t\$this->set('{$otherPluralName}', \$this->{$currentModelName}->{$otherModelName}->find('list'));";
			endif;
		endforeach;
	endforeach;
	
	echo "\n\t\t\$this->set('subtitle', __('Add ".strtolower($singularHumanName)."'));\n";
?>
	}

<?php $compact = array(); ?>
	/**
	 * Edit <?php echo strtolower($singularHumanName)."\n"; ?>
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function <?php echo $admin; ?>edit($id = NULL) {
		if(!$this-><?php echo $currentModelName; ?>->exists($id))
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
			
		if($this->request->is('post') || $this->request->is('put')) {
			if($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if($wannaUseSession): ?>
				$this->Session->flash(__('The <?php echo strtolower($singularHumanName); ?> has been edited'));
				$this->redirect(array('action' => 'index'));
<?php else: ?>
				$this->flash(__('The <?php echo strtolower($singularHumanName); ?> has been saved.'), array('action' => 'index'));
<?php endif; ?>
			}
<?php if($wannaUseSession): ?>
			else
				$this->Session->flash(__('The <?php echo strtolower($singularHumanName); ?> could not be edited. Please, try again'), 'error');
<?php endif; ?>
		} 
		else
			$this->request->data = $this-><?php echo $currentModelName; ?>->find('first', array(
				'conditions' => array('<?php echo $currentModelName; ?>.'.$this-><?php echo $currentModelName; ?>->primaryKey => $id)
			));
<?php
		foreach(array('belongsTo', 'hasAndBelongsToMany') as $assoc):
			foreach($modelObj->{$assoc} as $associationName => $relation):
				if(!empty($associationName)):
					$otherModelName = $this->_modelName($associationName);
					$otherPluralName = $this->_pluralName($associationName);
					echo "\n\t\t\$this->set('{$otherPluralName}', \$this->{$currentModelName}->{$otherModelName}->find('list'));";
				endif;
			endforeach;
		endforeach;
	
		echo "\n\t\t\$this->set('subtitle', __('Edit ".strtolower($singularHumanName)."'));\n";
	?>
	}

	/**
	 * Delete <?php echo strtolower($singularHumanName)."\n"; ?>
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function <?php echo $admin; ?>delete($id = NULL) {
		$this-><?php echo $currentModelName; ?>->id = $id;
		if(!$this-><?php echo $currentModelName; ?>->exists())
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
			
		$this->request->onlyAllow('post', 'delete');
		
		if($this-><?php echo $currentModelName; ?>->delete())
<?php if($wannaUseSession): ?>
			$this->Session->flash(__('The <?php echo strtolower($singularHumanName); ?> has been deleted'));
<?php else: ?>
			$this->flash(__('<?php echo ucfirst(strtolower($singularHumanName)); ?> deleted'), array('action' => 'index'));
<?php endif; ?>
		else
<?php if($wannaUseSession): ?>
			$this->Session->flash(__('The <?php echo strtolower($singularHumanName); ?> was not deleted'), 'error');
<?php else: ?>
			$this->flash(__('<?php echo ucfirst(strtolower($singularHumanName)); ?> was not deleted'), array('action' => 'index'));
<?php endif; ?>
			
		$this->redirect(array('action' => 'index'));
	}
	