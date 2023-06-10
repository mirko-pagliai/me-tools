<?php
declare(strict_types=1);

namespace App\Model\Entity;

use MeTools\Model\Entity\AbstractPerson;

/**
 * User entity.
 *
 * Useful for testing `AbstractPerson`
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
