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

namespace MeTools\Model\Validation;

use Cake\Validation\Validator;

/**
 * Validator object encapsulates all methods related to data validations for a model.
 *
 * This class provides additional methods compared to the base one.
 *
 * Remember to set this validator as the default validator in the table class or make your table extend `AppTable`.
 * @see https://book.cakephp.org/4/en/orm/validation.html#default-validator-class
 * @see \MeTools\Test\TestCase\Model\Table\AppTable
 */
class AppValidator extends Validator
{
    /**
     * Add a rule that ensure a string does not contain a reserved word
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param callable|string|null $when Either 'create' or 'update' or a callable that returns
     *   true when the validation rule should be applied.
     * @return $this
     */
    public function notReservedWords(string $field, ?string $message = null, $when = null)
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'notReservedWords', $extra + [
            'message' => __d('me_tools', 'This value contains a reserved word'),
            'rule' => ['custom', '/^((?!admin|manager|root|supervisor|moderator|pwd|password).)+$/i'],
        ]);
    }

    /**
     * Add a rule that ensures a string length is within a range
     * @param string $field The field you want to apply the rule to.
     * @param array $range The inclusive minimum and maximum length you want permitted.
     * @param string|null $message The error message when the rule fails.
     * @param callable|string|null $when Either 'create' or 'update' or a callable that returns
     *   true when the validation rule should be applied.
     * @return $this
     * @see Validator::lengthBetween()
     * @throws \InvalidArgumentException
     */
    public function lengthBetween(string $field, array $range, ?string $message = null, $when = null)
    {
        $message = $message ?: __d('me_tools', 'Must be between {0} and {1} chars', array_value_first($range), array_value_last($range));

        return parent::lengthBetween($field, $range, $message, $when);
    }

    /**
     * Adds a rule that ensures a string is a valid "person name"
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param callable|string|null $when Either 'create' or 'update' or a callable that returns
     *   true when the validation rule should be applied.
     * @return $this
     */
    public function personName(string $field, ?string $message = null, $when = null)
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'personName', $extra + [
            'message' => sprintf(
                '%s: %s. %s',
                I18N_ALLOWED_CHARS,
                __d('me_tools', 'letters, apostrophe, space'),
                __d('me_tools', 'Has to begin with a capital letter')
            ),
            'rule' => ['custom', '/^[A-Z][A-zàèéìòù\'\ ]+$/'],
        ]);
    }

    /**
     * Adds a rule that ensure is valid "slug" (lowercase letters, numbers, dash .Has to begin with a lowercase letter)
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param callable|string|null $when Either 'create' or 'update' or a callable that returns
     *   true when the validation rule should be applied.
     * @return $this
     */
    public function slug(string $field, ?string $message = null, $when = null)
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        return $this->add($field, 'slug', $extra + [
            'message' => sprintf(
                '%s: %s. %s',
                I18N_ALLOWED_CHARS,
                __d('me_tools', 'lowercase letters, numbers, dash'),
                __d('me_tools', 'Has to begin with a lowercase letter')
            ),
            'rule' => ['custom', '/^[a-z][a-z\d\-]+$/'],
        ]);
    }

    /**
     * Adds a rule that ensure a password is a valid password
     * @param string $field The field you want to apply the rule to.
     * @param string|null $message The error message when the rule fails.
     * @param callable|string|null $when Either 'create' or 'update' or a callable that returns
     *   true when the validation rule should be applied.
     * @return $this
     */
    public function validPassword(string $field, ?string $message = null, $when = null)
    {
        $extra = array_filter(['on' => $when, 'message' => $message]);

        $this->add($field, 'minLength', $extra + [
            'last' => true,
            'message' => __d('me_tools', 'Must be at least {0} chars', 8),
            'rule' => ['minLength', 8],
        ]);

        $this->add($field, 'hasDigit', $extra + [
            'message' => __d('me_tools', 'Must contain at least one digit'),
            'rule' => ['custom', '/\d/'],
        ]);

        $this->add($field, 'hasLowercaseLetter', $extra + [
            'message' => __d('me_tools', 'Must contain at least one lowercase letter'),
            'rule' => ['custom', '/[a-z]/'],
        ]);

        $this->add($field, 'hasCapitalLetter', $extra + [
            'message' => __d('me_tools', 'Must contain at least one capital letter'),
            'rule' => ['custom', '/[A-Z]/'],
        ]);

        $this->add($field, 'notAlphaNumeric', $extra + ['message' => __d('me_tools', 'Must contain at least one symbol')]);

        return $this;
    }
}
