<?php

namespace MeTools\Model\Table;

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
}
