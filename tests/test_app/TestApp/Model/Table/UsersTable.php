<?php
declare(strict_types=1);

namespace App\Model\Table;

use MeTools\Model\Table\AppTable;

/**
 * Users Model
 */
class UsersTable extends AppTable
{
    public static function defaultConnectionName(): string {
        return 'test';
    }

    /**
     * Initialize method
     * @param array $config The configuration for the Table
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }
}
