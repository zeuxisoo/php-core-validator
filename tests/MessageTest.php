<?php
use \Zeuxisoo\Core\Validator;

class MessageTest extends PHPUnit_Framework_TestCase {
	public function testFirstErrorMethod() {
		$validator = new Validator(array(
			'username' => ''
		));
		$validator->add("username", "Please enter username")->rule('required');
		$validator->inValid();

		$this->assertEquals("Please enter username", $validator->firstError());
	}

	public function testErrorsMethod() {
		$validator = new Validator(array(
			'username' => '',
			'password' => ''
		));
		$validator->add("username", "Please enter username")->rule('required');
		$validator->add("password", "Please enter password")->rule('required');
		$validator->inValid();

		$this->assertEquals(array(
			'Please enter username',
			'Please enter password'
		), $validator->errors());
	}
}
