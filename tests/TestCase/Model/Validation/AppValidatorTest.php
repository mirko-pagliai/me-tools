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
 */

namespace MeTools\Test\TestCase\Model\Validation;

use MeTools\Model\Validation\AppValidator;
use MeTools\TestSuite\TestCase;

/**
 * AppValidatorTest class
 */
class AppValidatorTest extends TestCase
{
    /**
     * @var AppValidator
     */
    protected AppValidator $Validator;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Validator ??= new AppValidator();
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::allowEmptyStringOnEmptyField()
     */
    public function testAllowEmptyStringOnEmptyField(): void
    {
        $this->Validator->allowEmptyStringOnEmptyField('tested_field', 'check_field');

        $this->assertEmpty($this->Validator->validate(['tested_field' => 'My field']));
        $this->assertEmpty($this->Validator->validate(['tested_field' => 'My field', 'check_field' => 'My check field']));
        $this->assertEmpty($this->Validator->validate(['tested_field' => '', 'check_field' => '']));

        $expected = ['tested_field' => ['_empty' => 'This field cannot be left empty']];
        $this->assertSame($expected, $this->Validator->validate(['tested_field' => '', 'check_field' => 'My check field']));
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::firstLetterCapitalized()
     */
    public function testFirstLetterCapitalized(): void
    {
        $this->Validator->firstLetterCapitalized('name');

        $expected = ['name' => ['firstLetterCapitalized' => 'Has to begin with a capital letter']];
        foreach (['', 'aa', 'aA', '1A', '?a', '?A', 123, ['A'], false, true] as $name) {
            $this->assertSame($expected, $this->Validator->validate(compact('name')));
        }

        foreach (['A', 'AA', 'Aa', 'A1', 'A?'] as $name) {
            $this->assertEmpty($this->Validator->validate(compact('name')));
        }
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::lengthBetween()
     */
    public function testLengthBetween(): void
    {
        $this->Validator->lengthBetween('username', [3, 20]);

        $expected = ['username' => ['lengthBetween' => 'Must be between 3 and 20 chars']];
        foreach (['', 'aa', str_repeat('a', 21)] as $username) {
            $this->assertSame($expected, $this->Validator->validate(compact('username')));
        }

        $this->assertEmpty($this->Validator->validate(['username' => 'validUsername']));

        //With wrong numbers
        $this->expectExceptionMessage('The $range argument requires 2 numbers');
        $this->Validator->lengthBetween('username', [1]);
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::notReservedWords()
     */
    public function testNotReservedWords(): void
    {
        $this->Validator->notReservedWords('username');

        $expected = ['username' => ['notReservedWords' => 'This value contains a reserved word']];
        foreach (['adMin' , '1manager2', 'root', 'supervisor', 'moderator', '!pwd!', 'password'] as $username) {
            $this->assertSame($expected, $this->Validator->validate(compact('username')));
            $this->assertSame($expected, $this->Validator->validate(['username' => 'a' . $username . 'a']));
        }

        $this->assertEmpty($this->Validator->validate(['username' => 'validUsername']));
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::personName()
     */
    public function testPersonName(): void
    {
        $this->Validator->personName('name');

        $expected = ['name' => ['personName' => 'Allowed chars: letters, apostrophe, space. Has to begin with a capital letter']];
        foreach (['mirko', 'Mirk-o', 'Mirko_0', ' Mirko', '\'Mirko', 'mirkO', 'Mirko1'] as $name) {
            $this->assertSame($expected, $this->Validator->validate(compact('name')));
        }

        foreach (['Mirko', 'Di Alessandro', 'D\'Alessandro', 'Casinò'] as $name) {
            $this->assertEmpty($this->Validator->validate(compact('name')));
        }
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::slug()
     */
    public function testSlug(): void
    {
        $this->Validator->slug('slug');

        $expected = ['slug' => ['slug' => 'Allowed chars: lowercase letters, numbers, dash. Has to begin with a lowercase letter']];
        foreach (['Mirko', 'mirko pagliai', 'mirko_pagliai', '-mirko', '3mirko'] as $slug) {
            $this->assertSame($expected, $this->Validator->validate(compact('slug')));
        }

        foreach (['mirko', 'mirko-pagliai', 'mirko3', 'mirko-3', 'mirko-pagliai-3'] as $slug) {
            $this->assertEmpty($this->Validator->validate(compact('slug')));
        }
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::title()
     */
    public function testTitle(): void
    {
        $this->Validator->title('title');

        $expected = ['title' => ['title' => 'Allowed chars: letters, numbers, apostrophe, space, slash, dash, parentheses, comma. Has to begin with a capital letter']];
        foreach (['my title', 'my_title', 'Ten $ dollar'] as $title) {
            $this->assertSame($expected, $this->Validator->validate(compact('title')), 'No error for `' . $title . '`');
        }

        foreach (['My title', 'My 4th title', 'Alfa/Beta', 'Alfa-Beta', 'Di Alessandro', 'D\'Alessandro', 'Casinò', 'First (second)', 'First, second'] as $title) {
            $this->assertEmpty($this->Validator->validate(compact('title')));
        }
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::validPassword()
     */
    public function testValidPassword(): void
    {
        $this->Validator->validPassword('password');

        $expected = ['password' => [
            'minLength' => 'Must be at least 8 chars',
            'hasDigit' => 'Must contain at least one digit',
            'hasCapitalLetter' => 'Must contain at least one capital letter',
            'notAlphaNumeric' => 'Must contain at least one symbol',
        ]];
        $this->assertSame($expected, $this->Validator->validate(['password' => str_repeat('a', 7)]));

        unset($expected['password']['minLength'], $expected['password']['hasCapitalLetter']);
        $expected['password'] += ['hasLowercaseLetter' => 'Must contain at least one lowercase letter'];
        $this->assertEquals($expected, $this->Validator->validate(['password' => str_repeat('A', 8)]));

        $this->assertEmpty($this->Validator->validate(['password' => 'aaaAAA1$']));

        //With a custom message
        $expected = ['password' => [
            'minLength' => 'Your password is wrong!',
            'hasDigit' => 'Your password is wrong!',
            'hasCapitalLetter' => 'Your password is wrong!',
            'notAlphaNumeric' => 'Your password is wrong!',
        ]];
        $this->Validator->validPassword('password', 'Your password is wrong!');
        $this->assertSame($expected, $this->Validator->validate(['password' => 'aa']));
    }
}
