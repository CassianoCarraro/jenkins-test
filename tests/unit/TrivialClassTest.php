<?php

use \Test\TrivialClass;

class TrivialClassTest extends PHPUnit_Framework_TestCase {

	public function testDoAnything() {
		$tc = new TrivialClass();
		$this->assertTrue($tc->doAnything(), 'Do anything not return true');
	}
}