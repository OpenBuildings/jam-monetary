<?php defined('SYSPATH') OR die('No direct access allowed.'); 

class Model_Variation extends Jam_Model {

	static public function initialize(Jam_Meta $meta)
	{
		$meta
			->fields(array(
				'id' => Jam::field('primary'),
				'name' => Jam::field('string'),
				'price' => Jam::field('price', array('default_currency' => 'GBP')),
			))
			->validator('price', array('price' => TRUE));

	}
}