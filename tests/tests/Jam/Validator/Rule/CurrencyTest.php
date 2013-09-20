<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @group price
 */
class Jam_Validator_Rule_CurrencyTest extends Testcase_Monetary {

	/**
	 * @covers Jam_Validator_Rule_Currency
	 */
	public function test_validate()
	{
		$product = Jam::build('product', array('currency' => 'NNN'));

		$this->assertFalse($product->check());
		$product->currency = 'USD';
		$this->assertTrue($product->check());
	}
}
