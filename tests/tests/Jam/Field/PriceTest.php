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
		$this->assertSame(2, $product->price->ceil_on_convert());


		$product->currency('EUR');
		$product->display_currency('BGN');
		$product->ceil_on_convert(4);

		$product->price = 8;

		$this->assertInstanceOf('Jam_Price', $product->price);
		$this->assertSame(8.0, $product->price->amount());
		$this->assertSame('EUR', $product->price->currency());
		$this->assertSame('BGN', $product->price->display_currency());
		$this->assertSame(4, $product->price->ceil_on_convert());

		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);
		$product->monetary($monetary);

		$product->price = 6;

		$this->assertInstanceOf('Jam_Price', $product->price);
		$this->assertSame(5.038, $product->price->in('GBP'));

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
		$this->assertEquals(2, $price->ceil_on_convert());

		$this->assertSame(OpenBuildings\Monetary\Monetary::instance(), $price->monetary());
	}


	public function data_set()
	{
		return array(
			array(0, 0.0),
			array(1, 1.0),
			array(1.11, 1.11),
			array('0', 0.0),
			array('0.0', 0.0),
			array('1', 1.0),
			array('1.11', 1.11),
			array('-1.11', -1.11),
			array(true, true),
			array('', null),
			array(null, null),
			array(false, null),
			array(array(), null),
			array('', '', false),
			array(null, null, false),
			array(false, false, false),
			array(array(), array(), false),
		);
	}

	/**
	 * @dataProvider data_set
	 */

	public function test_set($price, $expected, $convert_empty = true)
	{
		$product = Jam::build('product');
		$jamPriceField = new Kohana_Jam_Field_Price();
		$jamPriceField->convert_empty = $convert_empty;
		$this->assertSame($expected, $jamPriceField->set($product, $price, false));

	}
}
