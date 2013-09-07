<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Jam Validatior Rule
 *
 * @package    Openbuildings\Jam
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Kohana_Jam_Validator_Rule_Price extends Jam_Validator_Rule_Numeric {

	public function validate(Jam_Validated $model, $attribute, $value)
	{
		parent::validate($model, $attribute, $value->amount());
	}
}