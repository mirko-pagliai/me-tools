<?php

namespace MeTools\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use MeTools\Model\Validation\AppValidator;

/**
 * Represents a single database table
 */
abstract class AppTable extends Table
{
    /**
     * Initialize method
     * @param array $config The configuration for the Table
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->_validatorClass = AppValidator::class;
    }

    /**
     * `active` find method
     * @param \Cake\ORM\Query $Query The query builder
     * @return \Cake\ORM\Query The query builder
     * @see \Cake\ORM\Table::find() for options to use for the find
     */
    public function findActive(Query $Query): Query
    {
        return $Query->where(['active' => true]);
    }
}
