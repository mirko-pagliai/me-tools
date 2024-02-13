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
namespace MeTools\Test\TestCase\View\Helper;

use Cake\Datasource\Paging\PaginatedResultSet;
use Cake\Http\ServerRequest;
use Cake\ORM\ResultSet;
use Cake\Routing\Router;
use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\PaginatorHelper;

/**
 * PaginatorHelperTest class
 */
class PaginatorHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\PaginatorHelper
     */
    protected PaginatorHelper $Helper;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $Request = new ServerRequest([
            'url' => '/',
            'params' => [
                'plugin' => null,
                'controller' => 'Articles',
                'action' => 'index',
            ],
        ]);

        Router::reload();
        $builder = Router::createRouteBuilder('/');
        $builder->connect('/', ['controller' => 'Articles', 'action' => 'index']);
        $builder->connect('/{controller}/{action}/*');
        $builder->connect('/{plugin}/{controller}/{action}/*');
        Router::setRequest($Request);

        $PaginatedResult = new PaginatedResultSet(new ResultSet([]), [
            'alias' => 'Articles',
            'currentPage' => 1,
            'count' => 9,
            'totalCount' => 62,
            'hasPrevPage' => false,
            'hasNextPage' => true,
            'pageCount' => 7,
        ]);

        $this->Helper ??= new PaginatorHelper(new View());
        $this->Helper->setPaginated($PaginatedResult);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\PaginatorHelper::hasPaginated()
     */
    public function testHasPaginated(): void
    {
        $this->assertTrue($this->Helper->hasPaginated());

        $this->Helper = new PaginatorHelper(new View());
        $this->assertFalse($this->Helper->hasPaginated());
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\PaginatorHelper::next()
     */
    public function testNext(): void
    {
        $expected = '<li class="page-item"><a class="page-link" rel="next" href="/?page=2">Next <i class="fa-solid fa-caret-right"></i></a></li>';
        $this->assertSame($expected, $this->Helper->next('Next'));
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\PaginatorHelper::prev()
     */
    public function testPrev(): void
    {
        $expected = '<li class="page-item disabled"><a class="page-link" href="#"><i class="fa-solid fa-caret-left"></i> Previous</a></li>';
        $this->assertSame($expected, $this->Helper->prev('Previous'));
    }
}
