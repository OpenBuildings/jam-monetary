# Jam Monetary

A Jam Field to transparently use "monetary" as a jam field, and have currency exchange arithmetic out of the box

[![Build Status](https://travis-ci.org/OpenBuildings/jam-monetary.png?branch=master)](https://travis-ci.org/OpenBuildings/jam-monetary)
[![Coverage Status](https://coveralls.io/repos/OpenBuildings/jam-monetary/badge.png?branch=master)](https://coveralls.io/r/OpenBuildings/jam-monetary?branch=master)
[![Latest Stable Version](https://poser.pugx.org/openbuildings/jam-monetary/v/stable.png)](https://packagist.org/packages/openbuildings/jam-monetary)

## Usage

In your model, define the field as usual:

```php
class Model_Product extends Jam_Model {

	static public function initialize(Jam_Meta $meta)
	{
		$meta
			->fields(array(
				'id' => Jam::field('primary'),
				'name' => Jam::field('string'),
				'price' => Jam::field('price', array('default_currency' => 'GBP')),
				'discount_price' => Jam::field('price'),
			));
	}
}
```

And to use it you can:

```php
$product = Jam::build('product', array('price' => 10));

echo $product->price->amount();         // will output 10.00 float
echo $product->price->currency();       // will output GBP - the price currency
echo $product->price;                   // will output '10.00'
echo $product->price->as_string();      // will output '10.00'
echo $product->price->as_string('USD'); // will output '10.00'
echo $product->price->in('USD');        // will output 18.12 float
echo $product->price->humanize();       // will output '£10.00'
echo $product->price->humanize('USD');  // will output '$18.12'
echo $product->price->as_html();        // will output '&pound;10.00'
echo $product->price->as_html('USD');   // will output '$18.12'
echo $product->price->as_html('EUR');   // will output '&euro;18.12'

// Price arithmetic
$product->price->add(10);

// Add 2 prices together, doing the exchange conversion arithmetic on the fly
$product->price->add(new Jam_Price(20, 'EUR'));

// Adding more than one price
$product->price->add(new Jam_Price(20, 'EUR'), new Jam_Price(10, 'GBP'), 12.32);
```

## Methods

- ``in($currency)`` : display the amount in the specified currency, put through number_format with 2 digits after the dot
- ``as_string($currency = NULL)`` : return the number_format() on the price's amount, with 2 digits after the dot.
- ``humanize($currency = NULL)`` : display the amount with showing the proper currency sign in the correct position
- ``as_html($currency = NULL)`` : same as `humanize()`, but with HTML entities support
- ``add(... prices)`` : add one or more price values to this price (you can add negative prices in order to substract)

## Automatic currency and monetary values

If the model has a method ``currency()``, then each time a price object is requested, the result of this method is used for the price currency. That will allow you storing the currency alongside the amount iteself in the model

The same goes for a ``monetary()`` method - if its there in the model, then it'll be used for all the conversions. 

## Validators

There are 2 out of the box validator rules - one for currency and one for price. 

The price rule is basically a numeric rule, which performes the checks on the price's amount.

The currency validator is a choice validator, with the currencies of the world preselected in the "in" variable.

```php
```php
class Model_Product extends Jam_Model {

	static public function initialize(Jam_Meta $meta)
	{
		$meta
			->fields(array(
				'id' => Jam::field('primary'),
				'price' => Jam::field('price'),
				'currency' => Jam::field('string'),
			))
			->validator('price' => array('price' => array('greater_than' => 10)))
			->validator('currency' => array('currency' => TRUE));
	}
}
```

## License

Copyright (c) 2012-2013, OpenBuildings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.

