<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @group price
 */
class Jam_PriceTest extends Testcase_Monetary {

	/**
	 * @covers Jam_Price::min
	 */
	public function test_min()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);

		$result = Jam_Price::min(array($price1, $price2, $price3));

		$this->assertSame($price2, $result);
	}

	public function data_ceil()
	{
		return array(
			array(12.33, 0, 13),
			array(12.33, 2, 12.33),
			array(12.3343, 2, 12.34),
			array(12.762, 2, 12.77),
		);
	}

	/**
	 * @covers Jam_Price::ceil
	 * @dataProvider data_ceil
	 */
	public function test_ceil($amount, $precision, $expected)
	{
		$this->assertEquals($expected, Jam_Price::ceil($amount, $precision));
	}

	/**
	 * @covers Jam_Price::max
	 */
	public function test_max()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);

		$result = Jam_Price::max(array($price1, $price2, $price3));
		$this->assertSame($price1, $result);

		$price4 = new Jam_Price(17.2, 'EUR', $monetary);

		$this->assertSame($price4, Jam_Price::max(array($result, $price4)));
	}
	/**
	 * @covers Jam_Price::sum
	 */
	public function test_sum()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);

		$this->assertEquals('USD', Jam_Price::sum(array($price1, $price2, $price3), 'USD')->currency());
		$this->assertSame(40.238901914488, Jam_Price::sum(array($price1, $price2, $price3), 'USD')->amount());
		$this->assertSame(45.371025, Jam_Price::sum(array($price1, $price2, $price3, 20), 'GBP')->amount());

		$price1 = new Jam_Price(13.234, 'GBP');
		$price2 = new Jam_Price(5, 'GBP');
		$price3 = new Jam_Price(8.5, 'EUR');

		$expected = new Jam_Price(40.238901914488, 'USD', $monetary, 'EUR');

		$this->assertEquals($expected, Jam_Price::sum(array($price1, $price2, $price3), 'USD', $monetary, 'EUR'));
	}

	/**
	 * @covers Jam_Price::convert_to
	 */
	public function test_convert_to()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price = new Jam_Price(13.234, 'GBP', $monetary, 'EUR');

		$converted = $price->convert_to('USD');

		$this->assertEquals('USD', $converted->currency());
		$this->assertEquals(new Jam_Price(20.98936199607, 'USD', $monetary, 'EUR'), $converted);
	}

	/**
	 * @covers Jam_Price::multiply_by
	 */
	public function test_multiply_by()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price = new Jam_Price(13.234, 'GBP', $monetary, 'EUR');

		$converted = $price->multiply_by(3);

		$this->assertEquals(new Jam_Price(13.234 * 3, 'GBP', $monetary, 'EUR'), $converted);
	}
	
	/**
	 * @covers Jam_Price::amount
	 * @covers Jam_Price::currency
	 * @covers Jam_Price::monetary
	 * @covers Jam_Price::__construct
	 */
	public function test_construct()
	{
		$monetary = new OpenBuildings\Monetary\Monetary;
		$monetary2 = new OpenBuildings\Monetary\Monetary;
		$price = new Jam_Price(10, 'GBP', $monetary, 'GBP');
		$price2 = new Jam_Price(10, 'GBP', $monetary, 'EUR');
		$price3 = new Jam_Price(10, 'GBP', $monetary, 'EUR', TRUE);

		$this->assertSame(10.0, $price->amount());
		$this->assertSame('GBP', $price->currency());
		$this->assertSame('GBP', $price->display_currency());
		$this->assertSame($monetary, $price->monetary());
		$this->assertSame('EUR', $price2->display_currency());

		$price->amount(20.10);
		$price->currency('EUR');
		$price->monetary($monetary2);

		$this->assertSame(20.10, $price->amount());
		$this->assertSame('EUR', $price->currency());
		$this->assertSame($monetary2, $price->monetary());
		$this->assertSame(TRUE, $price3->ceil_on_convert());
	}

	/**
	 * @covers Jam_Price::as_string
	 * @covers Jam_Price::__toString
	 */
	public function test_as_string()
	{
		$price = new Jam_Price(13.234, 'GBP');

		$this->assertSame('13.23', $price->as_string());
		$this->assertSame('13.23', (string) $price);

		$price->display_currency('EUR');

		$this->assertSame('13.23', (string) $price);
	}

	/**
	 * @covers Jam_Price::in
	 */
	public function test_in()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(8.5, 'EUR', $monetary);
		$price3 = new Jam_Price(8.534, 'EUR', $monetary, 'GBP', TRUE);

		$this->assertSame(13.234, $price1->in('GBP'));
		$this->assertSame(8.5, $price2->in('EUR'));
		$this->assertSame(7.137025, $price2->in('GBP'));
		$this->assertSame(11.31945, $price2->in('USD'));

		$price1 = new Jam_Price(13.234, 'GBP');
		$this->assertNotEquals($price1->monetary(), $monetary);
		$this->assertSame(13.234, $price1->in('GBP', $monetary));

		$price1->display_currency('EUR');
		$this->assertSame(13.234, $price1->in(NULL, $monetary));

		$this->assertEquals(8, $price3->in('GBP'), 'Should ceil on convert');
	}

	/**
	 * @covers Jam_Price::ceil_on_convert
	 */
	public function test_ceil_on_convert()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price = new Jam_Price(13.234, 'EUR', $monetary);

		$this->assertFalse($price->ceil_on_convert());

		$this->assertSame($price, $price->ceil_on_convert(TRUE));

		$this->assertTrue($price->ceil_on_convert());

		$price->ceil_on_convert(3);

		$this->assertEquals(3, $price->ceil_on_convert());
	}

	/**
	 * @covers Jam_Price::is
	 */
	public function test_is()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(10.2, 'GBP', $monetary);
		$price2 = new Jam_Price(11.5, 'EUR', $monetary);

		$this->assertTrue($price1->is(Jam_Price::EQUAL_TO, $price1));
		$this->assertFalse($price1->is(Jam_Price::EQUAL_TO, $price2));

		$this->assertTrue($price1->is(Jam_Price::GREATER_THAN, $price2));
		$this->assertFalse($price1->is(Jam_Price::LESS_THAN, $price2));
		$this->assertFalse($price2->is(Jam_Price::GREATER_THAN, $price1));
		$this->assertTrue($price2->is(Jam_Price::LESS_THAN, $price1));

		$this->assertTrue($price1->is(Jam_Price::GREATER_THAN_OR_EQUAL_TO, $price2));
		$this->assertTrue($price1->is(Jam_Price::GREATER_THAN_OR_EQUAL_TO, $price1));
		$this->assertFalse($price1->is(Jam_Price::LESS_THAN_OR_EQUAL_TO, $price2));
		$this->assertTrue($price2->is(Jam_Price::LESS_THAN_OR_EQUAL_TO, $price1));
		$this->assertFalse($price2->is(Jam_Price::GREATER_THAN_OR_EQUAL_TO, $price1));
		$this->assertTrue($price2->is(Jam_Price::LESS_THAN_OR_EQUAL_TO, $price1));
	}

	/**
	 * @covers Jam_Price::is
	 * @expectedException Kohana_Exception
	 */
	public function test_is_unknow_operator()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(10.2, 'GBP', $monetary);
		$price2 = new Jam_Price(11.5, 'EUR', $monetary);

		$this->assertTrue($price1->is('something', $price1));
	}

	/**
	 * @covers Jam_Price::add
	 */
	public function test_add()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);

		$added_price = $price1->add($price2);
		$this->assertSame(13.234 + 5, $added_price->amount());
		$this->assertSame($price1->display_currency(), $added_price->display_currency());

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);
		$this->assertSame(13.234 + 7.137025, $price1->add($price3)->amount());

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);
		$this->assertSame(13.234 + 5 + 7.137025, $price1->add($price2, $price3)->amount());

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);
		$this->assertSame(13.234 + 5 + 7.137025 + 10, $price1->add($price2, $price3, 10)->amount());

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);
		$this->assertSame(13.234 + 5 + 7.137025 + 10 + 12.32, $price1->add($price2, $price3, 10, '12.32')->amount());

		$price1 = new Jam_Price(13.234, 'GBP', $monetary, 'EUR');
		$price2 = new Jam_Price(5, 'GBP', $monetary, 'GBP');
		$added_price = $price1->add($price2);
		$this->assertSame($price1->display_currency(), $added_price->display_currency());
	}

	/**
	 * @covers Jam_Price::humanize
	 */
	public function test_humanize()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);

		$this->assertSame('£13.23', $price1->humanize());
		$this->assertSame('$7.93', $price2->humanize('USD'));
		$this->assertSame('€8.50', $price3->humanize());

		$price1->display_currency('EUR');
		$this->assertSame('€15.76', $price1->humanize());
	}

	/**
	 * @covers Jam_Price::as_html
	 */
	public function test_as_html()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);

		$this->assertSame('&pound;13.23', $price1->as_html());
		$this->assertSame('$7.93', $price2->as_html('USD'));
		$this->assertSame('&euro;8.50', $price3->as_html());

		$price1->display_currency('EUR');
		$this->assertSame('&euro;15.76', $price1->as_html());
	}

	/**
	 * @covers Jam_Price::serialize
	 * @covers Jam_Price::unserialize
	 */
	public function test_serialize()
	{
		$price1 = new Jam_Price(13.234, 'GBP');
		$price2 = unserialize(serialize($price1));

		$this->assertEquals($price1, $price2);
	}
}
