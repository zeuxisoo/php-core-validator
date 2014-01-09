### Status

[![Build Status](https://travis-ci.org/zeuxisoo/php-core-validator.png?branch=master)](https://travis-ci.org/zeuxisoo/php-core-validator)

### Installation

Install the composer

	curl -sS https://getcomposer.org/installer | php

Edit composer.json

	{
		"require": {
			"zeuxisoo/core-validator": "dev-master"
		}
	}

Install/update your dependencies

	php composer.phar install

### Usage

Load the validation library

	use \Zeuxisoo\Core\Validator;

Initial the Validator object by `$_POST`

	$validator = Validator::factory($_POST);

The example of $_POST like:

	$_POST = array(
		'username' => "",
		'password' => "123456",
		'confirm_password' => "125",
		'first_name' => "123",
		'telephone' => '123456789',
		'email' => "abc@abc",
		'emails' => "abc@abc.com,abc@abc.com,abc",
		'url' => "http://127.0.0.1/()",
		'ip' => '127.0.0.1a',
		'title' => '123a',
		'age' => 17,
		'custom' => "2001-12",
		'gender' => 'boy',
		'zip_code' => 1002,
	);

Then, add the **target keys/fields** and **checking rules**

	$validator = Validator::factory($data);
	$validator->add('username', 'Pleas enter username')->rule('required');
	$validator->add('username', 'The username must less than 4 char')->rule('min_length', 4);	// empty so not show
	$validator->add('password', 'Password not match input value')->rule('match_value', $data['confirm_password']);
	$validator->add('first_name', 'The first name not match given pattern')->rule('match_pattern', '/^[A-Za-z]/');
	$validator->add('password', 'Password not match given field')->rule('match_field', 'confirm_password');
	$validator->add('confirm_password', 'Confirm password must more than 4 char')->rule('min_length', 4);
	$validator->add('telephone', 'The telephone number must less than 8 char')->rule('max_length', 8);
	$validator->add('telephone', 'The telephone number length must equals 8 char')->rule('exact_length', 8);
	$validator->add('email', 'Invalid email address')->rule('valid_email');
	$validator->add('url', 'Invalid email address')->rule('valid_url');
	$validator->add('ip', 'Invalid ip address')->rule('valid_ip');
	$validator->add('title', 'The string is not alpha string')->rule('valid_string');
	$validator->add('age', 'The age must less than 18 age')->rule('numeric_min', 18);
	$validator->add('age', 'The age must bigger than 16 age')->rule('numeric_max', 16);
	$validator->add('custom', 'The value is not much the custom format')->rule('custom', function($val) {
		return preg_match('/2001\-10/', $val) > 0;
	});
	$validator->add('name', 'The name is equals to Tomcat')->rule('is_true', function($val) {
		return $val === "Tomcat";
	});
	$validator->add('name', 'The name is not equals to Cattom')->rule('is_false', function($val) {
		return $val === "Cattom";
	});
	$validator->add('gender', "The gender is supported")->rule('between', array('boy', 'girl'));
	$validator->add('zip_code', 'The zip code is not exists')->rule('key_exists', array(
		1002 => "B",
		1003 => "C",
		1004 => "D",
	));

But, you can make it chainable like

	$validator->add('username', 'Pleas enter username')->rule('required')
			  ->add('username', 'The username must less than 4 char')->rule('min_length', 4)
			  ->add('password', 'Password not match input value')->rule('match_value', $data['confirm_password'])
			  ->add('first_name', 'The first name not match given pattern')->rule('match_pattern', '/^[A-Za-z]/')
			  ->add('password', 'Password not match given field')->rule('match_field', 'confirm_password')
			  ->add('confirm_password', 'Confirm password must more than 4 char')->rule('min_length', 4)
			  ->add('telephone', 'The telephone number must less than 8 char')->rule('max_length', 8)
			  ->add('telephone', 'The telephone number length must equals 8 char')->rule('exact_length', 8)
			  ->add('email', 'Invalid email address')->rule('valid_email')
			  ->add('url', 'Invalid email address')->rule('valid_url')
			  ->add('ip', 'Invalid ip address')->rule('valid_ip')
			  ->add('title', 'The string is not alpha string')->rule('valid_string')
			  ->add('age', 'The age must less than 18 age')->rule('numeric_min', 18)
			  ->add('age', 'The age must bigger than 16 age')->rule('numeric_max', 16)
			  ->add('custom', 'The value is not much the custom format')->rule('custom', function($val) {
					return preg_match('/2001\-10/', $val) > 0;
				})->rule('valid_string', array('utf8', 'alpha', 'lowercase'))
			  ->add('name', 'The name is equals to Tomcat')->rule('is_true', function($val) {
			  		return $val === "Tomcat";
				})
			  ->add('name', 'The name is not equals to Cattom')->rule('is_false', function($val) {
					return $val === "Cattom";
				})
			  ->add('gender', "The gender is supported")->rule('between', array('boy', 'girl'));
			  ->add('zip_code', 'The zip code is not exists')->rule('key_exists', array(
					1002 => "B",
					1003 => "C",
					1004 => "D",
			  ));


Finally, when you added rules completely, you can run the checking.

	if ($validator->inValid() === true) {
		foreach($validator->errors() as $error) {
			echo "<p>",$error,"</p>";
		}
	}

The method `$validator->inValid()` or alias method `$validator->valid()` will return the status of validation (true is contain error), the `$validator->error()` will contain all error messages. if you just want to show first error message, you can use method: `$validator->firstError()`

	if ($validator->inValid() === true) {
		echo $validator->firstError(); // pop first error
	}

All avaliable method:

	required
	min_length, 100
	match_value, "string"
	match_pattern, /regex/
	match_field, "post_field"
	max_length, 100
	exact_length, 8               (equals length)
	valid_email
	valid_url
	valid_ip
	valid_string
	numeric_min, 18
	numeric_max, 16
	custom, callback
	is_true, callback             (callback result is true)
	is_false, callback            (callback result is false)
	between, array('HK', 'TW')
	key_exists, array(1 => 'A',)
