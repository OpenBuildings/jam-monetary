<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @group price
 */
class Jam_Validator_Rule_PriceTest extends Testcase_Monetary {

	/**
	 * @covers Jam_Validator_Rule_Price::validate
	 */
	public function test_validate()
	{
		$product = Jam::build('product', array('price' => 10));

		$this->assertFalse($product->check());
		$product->price = 30;
		$this->assertTrue($product->check());

		$product = Jam::build('product', array('price' => 30));
		$this->assertTrue($product->check());
	}
}
