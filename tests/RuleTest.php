<?php
use \Zeuxisoo\Core\Validator;

class RuleTest extends PHPUnit_Framework_TestCase {
	public function testItIsRequire() {
		$validator = new Validator(array(
			'username' => ''
		));
		$validator->add("username", "Please enter username")->rule('required');

		$this->assertTrue($validator->inValid());
	}

	public function testNotMeetMinLength() {
		$validator = new Validator(array(
			'username' => "Tom",
		));
		$validator->add("username", "The username length must more than 4 char")->rule('min_length', 4);

		$this->assertTrue($validator->inValid());
	}

	public function testNotMatchValue() {
		$validator = new Validator(array(
			'password' => "123456"
		));
		$validator->add("password", "The password must match confirm password")->rule('match_value', "12345");

		$this->assertTrue($validator->inValid());
	}

	public function testNotMatchPattern() {
		$validator = new Validator(array(
			'username' => "123456"
		));
		$validator->add("username", "The username not match given pattern")->rule('match_pattern', '/^[A-Za-z]/');

		$this->assertTrue($validator->inValid());
	}

	public function testNotMatchField() {
		$validator = new Validator(array(
			'password' => "123456",
			'confirm_password' => "12345",
		));
		$validator->add("password", "The password not match confirm password field")->rule('match_field', 'confirm_password');

		$this->assertTrue($validator->inValid());
	}

	public function testNotMeetMaxLength() {
		$validator = new Validator(array(
			'username' => "123456789",
		));
		$validator->add("username", "The username must less than 8 char")->rule('max_length', 8);

		$this->assertTrue($validator->inValid());
	}

	public function testNotMeetExactLength() {
		$validator = new Validator(array(
			'username' => "123456789",
		));
		$validator->add("username", "The username must equals 8 char")->rule('exact_length', 8);

		$this->assertTrue($validator->inValid());
	}

	public function testInvalidEmail() {
		$validator = new Validator(array(
			'email' => "ab@a",
		));
		$validator->add("email", "The email address invalid")->rule('valid_email');

		$this->assertTrue($validator->inValid());
	}

	public function testInvalidURL() {
		$validator = new Validator(array(
			'url' => "http://abc.",
		));
		$validator->add("url", "The url address invalid")->rule('valid_url');

		$this->assertTrue($validator->inValid());
	}

	public function testInvalidIP() {
		$validator = new Validator(array(
			'ip' => "122.122.0.a",
		));
		$validator->add("ip", "The ip address invalid")->rule('valid_ip');

		$this->assertTrue($validator->inValid());
	}

	public function testInvalidAlphaString() {
		$validator = new Validator(array(
			'title' => "Number3",
		));
		$validator->add("title", "The title is not alpha string")->rule('valid_string');

		$this->assertTrue($validator->inValid());
	}

	public function testInvalidNumberString() {
		$validator = new Validator(array(
			'title' => "12345A",
		));
		$validator->add("title", "The title is not number string")->rule('valid_string', 'integer');

		$this->assertTrue($validator->inValid());
	}

	public function testInvalidNumericMin() {
		$validator = new Validator(array(
			'age' => "17",
		));
		$validator->add("age", "The age is less than 18")->rule('numeric_min', 18);

		$this->assertTrue($validator->inValid());
	}

	public function testInvalidNumericMax() {
		$validator = new Validator(array(
			'age' => "20",
		));
		$validator->add("age", "The age is bigger than 18")->rule('numeric_max', 18);

		$this->assertTrue($validator->inValid());
	}

	public function testInvalidCustomRule() {
		$validator = new Validator(array(
			'custom_date' => "2013/04/02",
		));
		$validator->add("custom_date", "The custom date invalid")->rule('custom', function($val) {
			return preg_match('/\d{4}\-\d{2}\-\d{2}/', $val) > 0;
		});

		$this->assertTrue($validator->inValid());
	}

	public function testIsTrueRule() {
		$validator = new Validator(array(
			'name' => "Tomcat",
		));
		$validator->add("name", "The names are equal")->rule('is_true', function($val) {
			return $val === "Tomcat";
		});

		$this->assertTrue($validator->inValid());
	}

	public function testIsFalseRule() {
		$validator = new Validator(array(
			'name' => "Tomcat",
		));
		$validator->add("name", "The name are not equal")->rule('is_false', function($val) {
			return $val === "Cattom";
		});

		$this->assertTrue($validator->inValid());
	}

	public function testInValidBetweenRule() {
		$validator = new Validator(array(
			'gender' => "unknown",
		));
		$validator->add("gender", "The gender is supported")->rule('between', array('boy', 'girl'));

		$this->assertTrue($validator->inValid());
	}

	public function testInValidKeyExistsRule() {
		$validator = new Validator(array(
			'zip_code' => 1001,
		));
		$validator->add("zip_code", "The zip code is not exists")->rule('key_exists', array(
			1002 => "B",
			1003 => "C",
			1004 => "D",
		));

		$this->assertTrue($validator->inValid());
	}
}
