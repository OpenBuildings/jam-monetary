<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @group jam.field.price
 */
class Jam_Field_PriceTest extends Testcase_Monetary {

	public function test_construct()
	{
		$product = Jam::build('product', array('price' => 10.233, 'name' => 'Basket'));

		$this->assertInstanceOf('Jam_Price', $product->price);
		$this->assertSame(10.233, $product->price->amount());
		$this->assertSame('10.23', $product->price->as_string());
		$this->assertSame('10.23', (string) $product->price);
		$this->assertSame('GBP', $product->price->currency());
		$this->assertSame('GBP', $product->price->display_currency());


		$product->currency('EUR');
		$product->display_currency('BGN');

		$product->price = 8;

		$this->assertInstanceOf('Jam_Price', $product->price);
		$this->assertSame(8.0, $product->price->amount());
		$this->assertSame('EUR', $product->price->currency());
		$this->assertSame('BGN', $product->price->display_currency());

		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);
		$product->monetary($monetary);

		$product->price = 6;

		$this->assertInstanceOf('Jam_Price', $product->price);
		$this->assertSame(5.0379, $product->price->in('GBP'));

		$product->price = 0;
		$this->assertInstanceOf('Jam_Price', $product->price);
		$this->assertSame(0.0, $product->price->amount());

		$product->price = 0.0;
		$this->assertInstanceOf('Jam_Price', $product->price);
		$this->assertSame(0.0, $product->price->amount());

		$product->price = NULL;
		$this->assertNull($product->price);
	}

	public function test_default_price()
	{
		$variation = Jam::build('variation', array('price' => 10));

		$price = $variation->price;

		$this->assertEquals('GBP', $price->currency());

		$this->assertSame(OpenBuildings\Monetary\Monetary::instance(), $price->monetary());
	}
}
