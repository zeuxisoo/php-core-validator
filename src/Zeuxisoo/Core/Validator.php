<?php
namespace Zeuxisoo\Core;

class Validator {
	private static $instance = null;

	private $form_datas = array();
	private $rule_datas = array();
	private $errors     = array();

	private $current_rule_data = array();

	public static function factory($form_datas) {
		if (self::$instance == null) {
			self::$instance = new Validator($form_datas);
		}
		return self::$instance;
	}

	public function __construct($form_datas = "") {
		$this->form_datas = $form_datas;
	}

	public function add($field_name, $fail_message) {
		$this->current_rule_data = array(
			'field_name' => $field_name,
			'fail_message' => $fail_message,
			'rule' => array(),
		);

		return $this;
	}

	public function rule($name) {
		$arguments = func_get_args();

		$this->current_rule_data['rule']['name'] = $name;
		$this->current_rule_data['rule']['args'] = array_splice($arguments, 1);

		$this->rule_datas[] = $this->current_rule_data;
		return $this;
	}

	public function valid() {
		foreach($this->rule_datas as $rule_data) {
			$field_value = isset($this->form_datas[$rule_data['field_name']]) === true ? $this->form_datas[$rule_data['field_name']] : "";

			$output = call_user_func_array(
				array($this, $rule_data['rule']['name']),
				array_merge(array($field_value), $rule_data['rule']['args'])
			);

			if ($output === false && $output !== false) {
				$this->errors[] = "Exception on $field_value";
			}elseif ($output !== true) {
				$this->errors[] = $rule_data['fail_message'];
			}
		}

		return empty($this->errors);
	}

	public function inValid() {
		return $this->valid() === false;
	}

	public function errors() {
		return $this->errors;
	}

	public function firstError() {
		return isset($this->errors[0]) ? $this->errors[0] : "";
	}

	// Validatiors
	public function required($val) {
		return $this->blank($val) === false;
	}

	public function blank($val) {
		return ($val === false || $val === null || $val === '' || $val === array());
	}

	public function match_value($val, $compare, $strict = false) {
		if ($this->blank($val) || $val === $compare || ($strict === true && $val === $compare)) {
			return true;
		}

		if (is_array($compare) === true) {
			foreach($compare as $c) {
				if ($val === $c || ($strict === true && $val === $c)) {
					return true;
				}
			}
		}

		return false;
	}

	public function match_pattern($val, $pattern) {
		return $this->blank($val) || preg_match($pattern, $val) > 0;
	}

	public function match_field($val, $field) {
		return $this->blank($val) || $this->form_datas[$field] === $val;
	}

	public function min_length($val, $length) {
		return $this->blank($val) || (function_exists("mb_strlen") === true ? mb_strlen($val) : strlen($val)) >= $length;
	}

	public function max_length($val, $length) {
		return $this->blank($val) || (function_exists("mb_strlen") === true ? mb_strlen($val) : strlen($val)) <= $length;
	}

	public function exact_length($val, $length) {
		return $this->blank($val) || (function_exists("mb_strlen") === true ? mb_strlen($val) : strlen($val)) == $length;
	}

	public function valid_email($val) {
		return $this->blank($val) || preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!\.)){0,61}[a-zA-Z0-9]?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!$)){0,61}[a-zA-Z0-9]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $val) > 0;
	}

	public function valid_emails($val) {
		if ($this->blank($val) === true) {
			return true;
		}

		$emails = explode(',', $val);

		foreach ($emails as $email) {
			if ($this->valid_email(trim($email)) === false) {
				return false;
			}
		}

		return true;
	}

	public function valid_url($val) {
		return $this->blank($val) || preg_match('/^(?:(?:ht|f)tp(?:s?)\:\/\/|~\/|\/)?(?:\w+:\w+@)?((?:(?:[-\w\d{1-3}]+\.)+(?:com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|edu|co\.uk|ac\.uk|it|fr|tv|museum|asia|local|travel|[a-z]{2}))|((\b25[0-5]\b|\b[2][0-4][0-9]\b|\b[0-1]?[0-9]?[0-9]\b)(\.(\b25[0-5]\b|\b[2][0-4][0-9]\b|\b[0-1]?[0-9]?[0-9]\b)){3}))(?::[\d]{1,5})?(?:(?:(?:\/(?:[-\w~!$+|.,=]|%[a-f\d]{2})+)+|\/)+|\?|#)?(?:(?:\?(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)(?:&(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)*)*(?:#(?:[-\w~!$ |\/.,*:;=]|%[a-f\d]{2})*)?$/i', $val) > 0;
	}

	public function valid_ip($val) {
		return $this->blank($val) || preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $val) > 0;
	}

	public function valid_string($val, $flags = array('alpha', 'utf8')) {
		if ($this->blank($val) === true) {
			return true;
		}

		if (is_array($flags) === false) {
			switch($flags) {
				case 'alpha':
					$flags = array('alpha', 'utf8');
					break;
				case 'alpha_numeric':
					$flags = array('alpha', 'utf8', 'numeric');
					break;
				case 'url_safe':
					$flags = array('alpha', 'numeric', 'dashes');
					break;
				case 'integer':
				case 'numeric':
					$flags = array('numeric');
					break;
				case 'float':
					$flags = array('numeric', 'dots');
					break;
				case 'all':
					$flags = array('alpha', 'utf8', 'numeric', 'spaces', 'newlines', 'tabs', 'punctuation', 'dashes');
					break;
				default:
					return false;
					break;
			}
		}

		$pattern  = in_array('uppercase', $flags) === false && in_array('alpha', $flags) === true ? 'a-z' : '';
		$pattern .= in_array('lowercase', $flags) === false && in_array('alpha', $flags) === true ? 'A-Z' : '';
		$pattern .= in_array('numeric', $flags) === true ? '0-9' : '';
		$pattern .= in_array('spaces', $flags) === true ? ' ' : '';
		$pattern .= in_array('newlines', $flags) === true ? "\n" : '';
		$pattern .= in_array('tabs', $flags) === true ? "\t" : '';
		$pattern .= in_array('dots', $flags) === true && in_array('punctuation', $flags) === false ? '\.' : '';
		$pattern .= in_array('punctuation', $flags) === true ? "\.,\!\?:;\&" : '';
		$pattern .= in_array('dashes', $flags) === true ? '_\-' : '';

		$pattern  = $this->blank($pattern) ? '/^(.*)$/' : ('/^(['.$pattern.'])+$/');
		$pattern .= in_array('utf8', $flags) === true ? 'u' : '';

		return preg_match($pattern, $val) > 0;
	}

	public function numeric_min($val, $min_val) {
		return $this->blank($val) || floatval($val) >= floatval($min_val);
	}

	public function numeric_max($val, $max_val) {
		return $this->blank($val) || floatval($val) <= floatval($max_val);
	}

	public function custom($val, $callback) {
		return call_user_func_array($callback, array($val));
	}

	public function is_true($val, $callback) {
		return $this->custom($val, $callback) == false;
	}

	public function is_false($val, $callback) {
		return $this->custom($val, $callback) == true;
	}

	public function between($val, $between) {
		return in_array($val, $between);
	}

	public function key_exists($val, $keys_val) {
		return array_key_exists($val, $keys_val);
	}
}

