<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @group price
 */
class Jam_PriceTest extends Testcase_Monetary {

	/**
	 * @covers Jam_Price::amount
	 */
	public function test_sum()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);

		$this->assertSame(40.238901914488, Jam_Price::sum('USD', array($price1, $price2, $price3)));
		$this->assertSame(25.371025, Jam_Price::sum('GBP', $price1, $price2, $price3));
	}

	/**
	 * @covers Jam_Price::amount
	 * @covers Jam_Price::currency
	 * @covers Jam_Price::monetary
	 * @covers Jam_Price::__construct
	 */
	public function test_construct()
	{
		$monetary = new OpenBuildings\Monetary\Monetary();
		$monetary2 = new OpenBuildings\Monetary\Monetary();
		$price = new Jam_Price(10, 'GBP', $monetary);

		$this->assertSame(10.0, $price->amount());
		$this->assertSame('GBP', $price->currency());
		$this->assertSame($monetary, $price->monetary());

		$price->amount(20.10);
		$price->currency('EUR');
		$price->monetary($monetary2);

		$this->assertSame(20.10, $price->amount());
		$this->assertSame('EUR', $price->currency());
		$this->assertSame($monetary2, $price->monetary());
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
	}

	/**
	 * @covers Jam_Price::in
	 */
	public function test_in()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(8.5, 'EUR', $monetary);

		$this->assertSame(13.234, $price1->in('GBP'));
		$this->assertSame(8.5, $price2->in('EUR'));
		$this->assertSame(7.137025, $price2->in('GBP'));
		$this->assertSame(11.31945, $price2->in('USD'));
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
		$this->assertSame(13.234 + 5, $price1->add($price2)->amount());

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
	}

	public function test_humanize()
	{
		$monetary = new OpenBuildings\Monetary\Monetary('GBP', new OpenBuildings\Monetary\Source_Static);

		$price1 = new Jam_Price(13.234, 'GBP', $monetary);
		$price2 = new Jam_Price(5, 'GBP', $monetary);
		$price3 = new Jam_Price(8.5, 'EUR', $monetary);

		$this->assertSame('£13.23', $price1->humanize());
		$this->assertSame('$7.93', $price2->humanize('USD'));
		$this->assertSame('€8.50', $price3->humanize());
	}

	public function test_serialize()
	{
		$price1 = new Jam_Price(13.234, 'GBP');
		$price2 = unserialize(serialize($price1));

		$this->assertEquals($price1, $price2);
	}
}