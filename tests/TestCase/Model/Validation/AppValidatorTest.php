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

        $this->Validator = $this->createPartialMockForAbstractClass(AppValidator::class);
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::notReservedWords()
     */
    public function testNotReservedWords(): void
    {
        $this->Validator->notReservedWords('username');

        $expected = ['username' => ['notReservedWords' => 'This value contains a reserved word']];
        foreach (['admin' , 'manager', 'root', 'supervisor', 'moderator'] as $username) {
            $this->assertSame($expected, $this->Validator->validate(compact('username')));
            $this->assertSame($expected, $this->Validator->validate(['username' => 'a' . $username . 'a']));
        }

        $this->assertEmpty($this->Validator->validate(['username' => 'validUsername']));
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::lengthBetween()
     */
    public function testLengthBetween(): void
    {
        $this->Validator->lengthBetween('username', [3, 20]);

        $expected = ['username' => ['lengthBetween' => 'Must be between 3 and 40 chars']];
        foreach (['', 'aa', str_repeat('a', 21)] as $username) {
            $this->assertSame($expected, $this->Validator->validate(compact('username')));
        }

        $this->assertEmpty($this->Validator->validate(['username' => 'validUsername']));
    }

    /**
     * @test
     * @uses \MeTools\Model\Validation\AppValidator::validPassword()
     */
    public function testValidPassword(): void
    {
        $this->Validator->validPassword('password');

        $expected = ['password' => ['minLength' => 'Must be at least 8 chars']];
        $this->assertSame($expected, $this->Validator->validate(['password' => 'aa']));

        $expected = ['password' => [
            'hasDigit' => 'Should contain at least one digit',
            'hasCapitalLetter' => 'Should contain at least one capital letter',
            'hasSymbol' => 'Should contain at least one symbol',
        ]];
        $this->assertSame($expected, $this->Validator->validate(['password' => str_repeat('a', 8)]));

        unset($expected['password']['hasCapitalLetter']);
        $expected['password'] += ['hasLowercaseLetter' => 'Should contain at least one lowercase letter'];
        $this->assertEquals($expected, $this->Validator->validate(['password' => str_repeat('A', 8)]));

        $this->assertEmpty($this->Validator->validate(['password' => 'aaaAAA1$']));
    }
}
