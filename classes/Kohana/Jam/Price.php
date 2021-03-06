<?php defined('SYSPATH') OR die('No direct script access.');

use OpenBuildings\Monetary\Monetary;

/**
 * @package    Openbuildings\Jam
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Price implements Serializable {

	const EQUAL_TO = '=';
	const GREATER_THAN = '>';
	const LESS_THAN = '<';
	const GREATER_THAN_OR_EQUAL_TO = '>=';
	const LESS_THAN_OR_EQUAL_TO = '<=';

	const EPSILON = 0.000001;

	public static function min(array $prices)
	{
		$min = reset($prices);

		foreach (array_slice($prices, 1) as $price) 
		{
			if ($price->is(Jam_Price::LESS_THAN, $min)) 
			{
				$min = $price;
			}
		}

		return $min;
	}

	public static function max(array $prices)
	{
		$max = reset($prices);

		foreach (array_slice($prices, 1) as $price) 
		{
			if ($price->is(Jam_Price::GREATER_THAN, $max)) 
			{
				$max = $price;
			}
		}

		return $max;
	}

	public static function ceil($amount, $precision)
	{
		$fraction = pow(10, $precision);

		return ceil($fraction * $amount) / $fraction;
	}

	public static function sum(array $prices, $currency, $monetary = NULL, $display_currency = NULL, $ceil_on_convert = FALSE)
	{
		$amount = 0;

		foreach ($prices as $price) 
		{
			if ( ! ($price instanceof Jam_Price))
			{
				$amount += (float) $price;
			}
			elseif ($price->currency() == $currency) 
			{
				$amount += $price->amount();
			}
			else
			{
				$amount += $price->in($currency, $monetary);
			}
		}

		return new Jam_Price($amount, $currency, $monetary, $display_currency, $ceil_on_convert);
	}

	protected $_amount = 0;
	protected $_currency;
	protected $_monetary;
	protected $_ceil_on_convert = FALSE;
	protected $_display_currency;
	
	public function ceil_on_convert($ceil_on_convert = NULL)
	{
		if ($ceil_on_convert !== NULL)
		{
			$this->_ceil_on_convert = $ceil_on_convert;
			return $this;
		}
		return $this->_ceil_on_convert;
	}
	
	public function display_currency($display_currency = NULL)
	{
		if ($display_currency !== NULL)
		{
			$this->_display_currency = $display_currency;
			return $this;
		}
		return $this->_display_currency;
	}
	
	/**
	 * Getter / Setter of amount, casts to float
	 * @param  float $amount 
	 * @return float|$this         
	 */
	public function amount($amount = NULL)
	{
		if ($amount !== NULL)
		{
			$this->_amount = (float) $amount;
			return $this;
		}
		return $this->_amount;
	}
	
	/**
	 * Getter / Setter
	 * @param  string $currency 
	 * @return string|$this           
	 */
	public function currency($currency = NULL)
	{
		if ($currency !== NULL)
		{
			$this->_currency = $currency;
			return $this;
		}
		return $this->_currency;
	}
	
	/**
	 * Getter / Setter, defaults to Monetary::instance()
	 * @param  OpenBuildings\Monetary\Monetary $monetary 
	 * @return OpenBuildings\Monetary\Monetary|$this
	 */
	public function monetary($monetary = NULL)
	{
		if ($monetary !== NULL)
		{
			$this->_monetary = $monetary;
			return $this;
		}

		if ( ! $this->_monetary) 
		{
			$this->_monetary = Monetary::instance();
		}

		return $this->_monetary;
	}

	public function __construct($amount, $currency, $monetary = NULL, $display_currency = NULL, $ceil_on_convert = FALSE)
	{
		$this->amount($amount);
		$this->currency($currency);
		$this->display_currency($display_currency);
		$this->monetary($monetary);
		$this->ceil_on_convert($ceil_on_convert);
	}

	/**
	 * Use as_string method
	 * @return string 
	 */
	public function __toString()
	{
		return $this->as_string();
	}

	/**
	 * Use number_format with 2 digits after the decimal dot
	 * @return string 
	 */
	public function as_string($currency = NULL)
	{
		return number_format($this->in($currency), 2, '.', '');	
	}

	/**
	 * Use Monetary's "format" method
	 * @param  string $currency optionally convert to another currency
	 * @return string
	 */
	public function humanize($currency = NULL)
	{
		$currency = $currency ?: ($this->display_currency() ?: $this->currency());

		return $this->monetary()->format($this->in($currency), $currency);
	}

	/**
	 * Replace HTML entities in humanize
	 * @param  string $currency optionally convert to another currency
	 * @return string
	 */
	public function as_html($currency = NULL)
	{
		return HTML::entities($this->humanize($currency));
	}

	/**
	 * Perform price arithmetic - add / remove prices with correct currency convertions
	 * @return $this
	 */
	public function add($price)
	{
		$prices = func_get_args();
		array_unshift($prices, $this);

		return Jam_Price::sum($prices, $this->currency(), $this->monetary(), $this->display_currency(), $this->ceil_on_convert());
	}

	/**
	 * Myltiply by a value
	 * @param  mixed $value
	 * @return Jam_Price
	 */
	public function multiply_by($value)
	{
		return new Jam_Price($this->amount() * $value, $this->currency(), $this->monetary(), $this->display_currency(), $this->ceil_on_convert());
	}

	/**
	 * Convert the price to a different currency
	 * @param  string $currency 
	 * @return Jam_Price
	 */
	public function convert_to($currency)
	{
		return new Jam_Price($this->in($currency), $currency, $this->monetary(), $this->display_currency(), $this->ceil_on_convert());
	}

	/**
	 * Perform price comparation, e.g. =, >, <, => or =<. Performs currency conversion if nesessary
	 * @return boolean 
	 * @param string $operator 
	 * @param mixed value 
	 */
	public function is($operator, $value)
	{
		if ($value instanceof Jam_Price) 
		{
			$value = $value->in($this->currency());
		}

		switch ($operator) 
		{
			case Jam_Price::EQUAL_TO:
				$result = (abs($this->amount() - $value) < Jam_Price::EPSILON);
			break;
			case Jam_Price::GREATER_THAN:
				$result = (($this->amount() - $value) >= Jam_Price::EPSILON);
			break;
			case Jam_Price::LESS_THAN:
				$result = (($value - $this->amount()) >= Jam_Price::EPSILON);
			break;
			case Jam_Price::GREATER_THAN_OR_EQUAL_TO:
				$result = (($value - $this->amount()) < Jam_Price::EPSILON);
			break;
			case Jam_Price::LESS_THAN_OR_EQUAL_TO:
				$result = (($this->amount() - $value) < Jam_Price::EPSILON);
			break;
			default;
				throw new Kohana_Exception('Operator not supported :operator', array(':operator' => $operator));
		}
		return $result;
	}

	/**
	 * Display the amount in a currency
	 * @param  string|null $currency
	 * @param  OpenBuildings\Monetary\Monetary|null $monetary
	 * @return float
	 */
	public function in($currency = NULL, $monetary = NULL)
	{
		if ( ! $currency OR $currency == $this->currency())
		{
			return $this->amount();
		}
		else
		{
			$monetary = $monetary ?: $this->monetary();

			$amount = $monetary->convert($this->amount(), $this->currency(), $currency);

			if ($this->ceil_on_convert() !== FALSE)
			{
				$amount = static::ceil($amount, $this->ceil_on_convert() === TRUE ? 0 : $this->ceil_on_convert());
			}

			return $amount;
		}
	}

	/**
	 * implement Serializable
	 * @return string
	 */
	public function serialize()
	{
		return serialize(array($this->amount(), $this->currency(), $this->display_currency(), $this->ceil_on_convert()));
	}

	/**
	 * implement Serializable
	 * @param string $data 
	 */
	public function unserialize($data)
	{
		$data = unserialize($data);

		$this->amount($data[0]);
		$this->currency($data[1]);
		$this->display_currency($data[2]);
		$this->ceil_on_convert($data[3]);
		$this->monetary(Monetary::instance());
	}
}
