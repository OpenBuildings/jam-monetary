<?php defined('SYSPATH') OR die('No direct script access.');

use OpenBuildings\Monetary\Monetary;

/**
 * @package    Openbuildings\Jam
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Price implements Serializable {

	public static function sum($currency, $prices)
	{
		$prices = is_array($prices) ? $prices : array_slice(func_get_args(), 1);
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
				$amount += $price->in($currency);
			}
		}

		return $amount;
	}

	protected $_amount = 0;
	protected $_currency;
	protected $_monetary;
	
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

	public function __construct($amount, $currency, $monetary = NULL)
	{
		$this->amount($amount);
		$this->currency($currency);
		$this->monetary($monetary);
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
		return $this->monetary()->format($this->in($currency), $currency ?: $this->currency());
	}

	/**
	 * Perform price arithmetic - add / remove prices with correct currency convertions
	 * @return $this
	 */
	public function add($price)
	{
		$prices = func_get_args();
		$this->amount($this->amount() + Jam_Price::sum($this->currency(), $prices));

		return $this;
	}

	/**
	 * Display the amount in a currency
	 * @param  stirn $currency 
	 * @return float
	 */
	public function in($currency = NULL)
	{
		if ( ! $currency OR $currency == $this->currency())
		{
			return $this->amount();
		}
		else
		{
			return $this->monetary()->convert($this->amount(), $this->currency(), $currency);
		}
	}

	/**
	 * implement Serializable
	 * @return string
	 */
	public function serialize()
	{
		return serialize(array($this->amount(), $this->currency()));
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
		$this->monetary(Monetary::instance());
	}
}