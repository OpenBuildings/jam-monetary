<?php

use OpenBuildings\Monetary\Monetary;

/**
 * @package    Openbuildings\Jam
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Field_Price extends Jam_Field_String {

	public $default = 0;

	public $allow_null = TRUE;

	public $convert_empty = TRUE;

	public $default_currency = 'GBP';

	public $default_display_currency = 'GBP';

	public $default_ceil_on_convert = FALSE;

	public static $autoload_from_model = array('currency', 'display_currency', 'monetary', 'ceil_on_convert');

	/**
	 * Casts to a string, preserving NULLs along the way.
	 *
	 * @param   mixed   $value
	 * @return  string
	 */
	public function set(Jam_Validated $model, $value, $is_changed)
	{
		list($value, $return) = $this->_default($model, $value);

		return $value;
	}

	/**
	 * Preserve nulls and 0 / 0.0 values
	 * @param  Jam_Validated $model 
	 * @param  mixed        $value 
	 * @return array               
	 */
	protected function _default(Jam_Validated $model, $value)
	{
		$return = FALSE;

		$value = $this->run_filters($model, $value);

		// Convert empty values to NULL, if needed
		if ($this->convert_empty AND empty($value) AND $value !== 0 AND $value !== 0.0 AND $value !== '0')
		{
			$value  = $this->empty_value;
			$return = TRUE;
		}

		// Allow NULL values to pass through untouched by the field
		if ($this->allow_null AND $value === NULL)
		{
			$value  = NULL;
			$return = TRUE;
		}

		return array($value, $return);
	}

	public function monetary()
	{
		return Monetary::instance();
	}

	/**
	 * convert to Jam_Price if not NULL 
	 * @param  Jam_Validated $model     
	 * @param  mixed        $value     
	 * @param  boolean        $is_loaded 
	 * @return Jam_Price                   
	 */
	public function get(Jam_Validated $model, $value, $is_loaded)
	{
		if ( ! ($value instanceof Jam_Price) AND $value !== NULL)
		{
			$value = new Jam_Price($value, $this->default_currency, $this->monetary(), $this->default_display_currency, $this->default_ceil_on_convert);

			foreach (static::$autoload_from_model as $autoload_method) 
			{
				if (method_exists($model, $autoload_method)) 
				{
					$value->$autoload_method($model->$autoload_method());
				}
			}
		}

		return $value;
	}
}