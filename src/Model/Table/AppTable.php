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

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use MeTools\Model\Validation\AppValidator;

/**
 * Represents a single database table
 *
 * @method \Cake\ORM\Query\SelectQuery findById($id)
 */
abstract class AppTable extends Table
{
    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->_validatorClass = AppValidator::class;
    }

    /**
     * `active` find method
     * @param \Cake\ORM\Query\SelectQuery $Query Query
     * @return \Cake\ORM\Query\SelectQuery
     * @see \Cake\ORM\Table::find() for options to use for the find
     */
    public function findActive(SelectQuery $Query): SelectQuery
    {
        if ($this->getSchema()->hasColumn('active')) {
            $Query->where([$this->getAlias() . '.active' => true]);
        }

        return $Query;
    }
}
