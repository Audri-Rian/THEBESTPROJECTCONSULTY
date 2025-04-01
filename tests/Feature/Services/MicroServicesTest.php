<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\Product;
use App\Services\MicroServices;

class MicroServicesTest extends TestCase 
{
    public function test_check_low_stock_when_stock_is_low()
    {
        $product = new Product([
            'name' => 'Produto Teste',
            'quantity' => 3,
            'price' => 100.00
        ]);

        $service = new MicroServices();
        $result = $service->checkLowStock($product);

        $this->assertNotNull($result);
        $this->assertEquals($product->id, $result['product']->id);
        $this->assertEquals("Estoque baixo! Tem apenas 3 restantes.", $result['message']);
    }

    public function test_check_low_stock_when_stock_is_sufficient()
    {
        $product = new Product([
            'name' => 'Produto Teste',
            'quantity' => 10,
            'price' => 100.00
        ]);

        $service = new MicroServices();
        $result = $service->checkLowStock($product);

        $this->assertNull($result);
    }
}