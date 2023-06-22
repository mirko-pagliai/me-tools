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
 * @since       2.25.0
 */

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
