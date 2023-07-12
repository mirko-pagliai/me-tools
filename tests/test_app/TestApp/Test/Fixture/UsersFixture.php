<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Init method
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'username' => 'myusername',
                'email' => 'mymail@example.com',
                'password' => 'MyPassword!',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'active' => 1,
                'created' => '2023-06-20 11:28:21',
                'modified' => '2023-06-20 11:28:21',
            ],
        ];

        parent::init();
    }
}
