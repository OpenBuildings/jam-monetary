<?php

use PHPUnit\Framework\TestCase;

/**
 * @package    Openbuildings\Jam
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2013 OpenBuildings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class Testcase_Monetary extends TestCase {
	
	public function setUp()
	{
		parent::setUp();
		Database::instance()->begin();
	}

	public function tearDown()
	{
		Database::instance()->rollback();	
		parent::tearDown();
	}
}
