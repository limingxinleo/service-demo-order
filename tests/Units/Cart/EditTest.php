<?php
// +----------------------------------------------------------------------
// | EditTest.php [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2017 limingxinleo All rights reserved.
// +----------------------------------------------------------------------
// | Author: limx <715557344@qq.com> <https://github.com/limingxinleo>
// +----------------------------------------------------------------------
namespace Tests\Units\Cart;

use App\Models\Cart;
use App\Models\Model;
use App\Thrift\Clients\OrderClient;
use Tests\Units\BaseTest;

class EditTest extends BaseTest
{
    public function testAddCartCase()
    {
        $client = OrderClient::getInstance();
        $status = $client->addGoodsToCart($this->userId, $this->goodsId, $this->shopId, 1, $this->fee);
        $this->assertTrue($status);
    }

    public function testListCartByUserIdCase()
    {
        $client = OrderClient::getInstance();
        $client->addGoodsToCart($this->userId, $this->goodsId, $this->shopId, 2, $this->fee);
        $result = $client->listCartsByUserId($this->userId, 10, null);
        $this->assertTrue($result instanceof \Xin\Thrift\OrderService\Order\CartList);
        $this->assertTrue(count($result->items) > 0);
        $this->assertTrue($result->items[0] instanceof \Xin\Thrift\OrderService\Order\Cart);
    }

    public function testDelCartCase()
    {
        $client = OrderClient::getInstance();
        $result = $client->listCartsByUserId($this->userId, 10, null);
        if (count($result->items) > 0) {
            $id = $result->items[0]->id;
            $status = $client->delGoodsFromCart($this->userId, $this->goodsId, $id);
            $this->assertTrue($status);

            $model = Cart::getInstance([
                'user_id' => $this->userId
            ]);
            $delCart = $model->findFirst($id);
            if ($delCart) {
                $this->assertEquals(Model::DELETED, $delCart->is_deleted);
            } else {
                $this->assertTrue(false);
            }
        }
    }
}
