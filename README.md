### Usage

Include the validation library

	require_once 'validator.php';
	
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
	);
	
Then, add the **target keys/fields** and **checking rules**

	$validator = Validator::factory($data);
	$validator->add('username', '請輸入暱稱')->rule('required');
	$validator->add('username', '暱稱最少需要4個字元')->rule('min_length', 4);	// empty so not show
	$validator->add('password', '密碼不相符 MatchValue')->rule('match_value', $data['confirm_password']);
	$validator->add('first_name', '名字開頭必須是英文字母')->rule('match_pattern', '/^[A-Za-z]/');
	$validator->add('password', '密碼不相符 MatchField')->rule('match_field', 'confirm_password');
	$validator->add('confirm_password', '確認密碼長度最少 4 個位')->rule('min_length', 4);
	$validator->add('telephone', '電話號碼長度最多 8 個位')->rule('max_length', 8);
	$validator->add('telephone', '電話號碼長度必須等於 8 個位')->rule('exact_length', 8);
	$validator->add('email', '不是電郵地址格式 單個')->rule('valid_email');
	$validator->add('url', '不是網址格式')->rule('valid_url');
	$validator->add('ip', '不是 IP 地址格式')->rule('valid_ip');
	$validator->add('title', '不是純字母')->rule('valid_string');
	$validator->add('age', '小於 18 歲')->rule('numeric_min', 18);
	$validator->add('age', '大於 16 歲')->rule('numeric_max', 16);
	$validator->add('custom', '自定格式不正確')->rule('custom', function($val) {
		return preg_match('/2001\-10/', $val) > 0;
	});
	
But, you can make it chainable like

	$validator->add('username', '請輸入暱稱')->rule('required')
			  ->add('username', '暱稱最少需要4個字元')->rule('min_length', 4)
			  ->add('password', '密碼不相符 MatchValue')->rule('match_value', $data['confirm_password'])
			  ->add('first_name', '名字開頭必須是英文字母')->rule('match_pattern', '/^[A-Za-z]/')
			  ->add('password', '密碼不相符 MatchField')->rule('match_field', 'confirm_password')
			  ->add('confirm_password', '確認密碼長度最少 4 個位')->rule('min_length', 4)
			  ->add('telephone', '電話號碼長度最多 8 個位')->rule('max_length', 8)
			  ->add('telephone', '電話號碼長度必須等於 8 個位')->rule('exact_length', 8)
			  ->add('email', '不是電郵地址格式 單個')->rule('valid_email')
			  ->add('url', '不是網址格式')->rule('valid_url')
			  ->add('ip', '不是 IP 地址格式')->rule('valid_ip')
			  ->add('title', '不是純字母')->rule('valid_string')
			  ->add('age', '小於 18 歲')->rule('numeric_min', 18)
			  ->add('age', '大於 16 歲')->rule('numeric_max', 16)
			  ->add('custom', '自定格式不正確')->rule('custom', function($val) {
						return preg_match('/2001\-10/', $val) > 0;
				})->rule('valid_string', array('utf8', 'alpha', 'lowercase'));
				
Finally, when you added rules completely, you can run the checking.

	if ($validator->run() === true) {
		foreach($validator->errors() as $error) {
			echo "<p>",$error,"</p>";
		}
	}
	
The method `$validator->run()` will return the status of validation (true is contain error), the `$validator->error()` will contain all error messages. if you just want to show first error message, you can use method: `$validator->first_error()`

	if ($validator->run() === true) {
		echo $validator->first_error(); // pop first error
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
