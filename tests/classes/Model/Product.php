<?php defined('SYSPATH') OR die('No direct access allowed.'); 

class Model_Product extends Jam_Model {

	static public function initialize(Jam_Meta $meta)
	{
		$meta
			->fields(array(
				'id' => Jam::field('primary'),
				'name' => Jam::field('string'),
				'price' => Jam::field('price', array('default_currency' => 'GBP')),
			))
			->validator('price', array('price' => array('greater_than' => 10)));

	}

	protected $_currency = 'GBP';
	
	protected $_monetary;
	
	public function monetary($monetary = NULL)
	{
		if ($monetary !== NULL)
		{
			$this->_monetary = $monetary;
			return $this;
		}
		return $this->_monetary;
	}
	
	public function currency($currency = NULL)
	{
		if ($currency !== NULL)
		{
			$this->_currency = $currency;
			return $this;
		}
		return $this->_currency;
	}
}