<?php
declare(strict_types=1);

namespace MeTools\Model\Rule;

use Cake\Datasource\EntityInterface;
use Cake\ORM\AssociationCollection;

/**
 * `NoAssociatedElements` rule.
 *
 * Gets an `AssociationCollection` as a constructor argument and checks that in the associated tables there are no
 *  elements related to the current one (which you are about to delete).
 * @see https://github.com/cakephp/cakephp/issues/6327
 * @since 2.25.8
 */
class NoAssociatedElements
{
    /**
     * @var \Cake\ORM\AssociationCollection
     */
    protected AssociationCollection $Associations;

    /**
     * @param \Cake\ORM\AssociationCollection $Associations AssociationCollection
     */
    public function __construct(AssociationCollection $Associations)
    {
        $this->Associations = $Associations;
    }

    /**
     * Invoke method
     * @param \Cake\Datasource\EntityInterface $Entity Entity
     * @return bool
     */
    public function __invoke(EntityInterface $Entity): bool
    {
        foreach ($this->Associations->getByType(['HasOne', 'HasMany']) as $Association) {
            /** @var string $foreignKey */
            $foreignKey = $Association->getForeignKey();

            $QueryOnAssociated = $Association->getTarget()->find()
                /** @phpstan-ignore-next-line */
                ->where([$foreignKey => $Entity->id])
                ->all();

            if ($QueryOnAssociated->count() > 0) {
                return false;
            }
        }

        return true;
    }
}
