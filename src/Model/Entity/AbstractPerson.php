<?php
declare(strict_types=1);

/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @since       2.25.1
 */

namespace MeTools\Model\Entity;

use Cake\ORM\Entity;

/**
 * Abstract `Person` entity.
 *
 * Provides properties and methods common to all entities that represent a person (user, client, patient, and so on...).
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 */
abstract class AbstractPerson extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity()
     * @var array<string, bool>
     * @see \MeTools\Model\Entity\AbstractPerson::__construct()
     */
    protected $_accessible;

    /**
     * Virtual fields
     * @var string[]
     * @see \MeTools\Model\Entity\AbstractPerson::__construct()
     */
    protected $_virtual;

    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);

        $this->_accessible = array_merge([
            'id' => false,
            'first_name' => true,
            'last_name' => true,
        ], $this->_accessible);

        $this->_virtual = array_merge(['full_name'], $this->_virtual ?? []);
    }

    /**
     * `full_name` virtual field
     * @return string
     */
    protected function _getFullName(): string
    {
        return $this->hasValue('first_name') && $this->hasValue('last_name') ? $this->get('first_name') . ' ' . $this->get('last_name') : '';
    }
}
