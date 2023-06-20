<?php
declare(strict_types=1);

namespace App\Model\Entity;

use MeTools\Model\Entity\AbstractPerson;

/**
 * User entity
 * @property string $username
 * @property string $email
 * @property string $password
 * @property bool $active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class User extends AbstractPerson
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity()
     * @var array<string, bool>
     */
    protected $_accessible = [
        'username' => true,
    ];

    /**
     * Virtual fields
     * @var string[]
     */
    protected $_virtual = ['short_username'];
}
