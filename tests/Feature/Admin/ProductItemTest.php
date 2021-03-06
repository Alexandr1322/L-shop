<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Product;
use App\Services\AdminItems;
use App\Services\AdminProducts;
use Illuminate\Container\Container;

/**
 * Class ProductItemTest
 *
 * @author D3lph1 <d3lph1.contact@gmail.com>
 *
 * @package Tests\Feature\Admin
 */
class ProductItemTest extends TestCase
{
    /**
     * @var AdminItems
     */
    private $adminItems;

    /**
     * @var AdminProducts
     */
    private $adminProduct;

    /**
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->adminItems = Container::getInstance()->make('App\Services\AdminItems');
        $this->adminProduct = Container::getInstance()->make('App\Services\AdminProducts');

        parent::__construct($name, $data, $dataName);
    }

    /**
     * Create/delete product/item test
     *
     * @return void
     */
    public function testCase()
    {
        $item = $this->adminItems->create('Test item', '', 'item', null, 1337, null);
        $this->assertInstanceOf(Item::class, $item);
        $itemId = (int)$item->id;

        $product = $this->adminProduct->create(0.01, 64, $itemId, 1, 1, 0);
        $this->assertInstanceOf(Product::class, $product);

        $this->assertTrue($this->adminItems->delete($itemId));
    }
}
