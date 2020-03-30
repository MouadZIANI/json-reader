<?php 

/*
* Constants
*/
define('LIMIT_REQUESTS_PER_ACCOUNT', 3);
define('COUNT_ACCOUNTS', 2);


/*
* Helpers
* Read data from file and parse it to php array 
*/
function parse_json_file($file_path) {
	$file_content = file_get_contents($file_path);
	return ($file_content !== false) ? json_decode($file_content, true) : [];
}

/*
* Increment requests count 
*/
function increment_requests_count() {
	set_setting("requests_count", (get_setting("requests_count") + 1));
}

/*
* Decrement requests count 
*/
function decrement_requests_count() {
	set_setting("requests_count", (get_setting("requests_count") - 1));
}

/*
* Get setting from settings file 
*/
function get_setting($key) {
	return parse_json_file("settings.json")[$key];
}

/*
* update setting 
*/
function set_setting($key, $value) {
	$settings = parse_json_file("settings.json");
	$settings[$key] = $value;
	file_put_contents("settings.json", json_encode($settings));
}

/*
* Get account by index 
*/
function get_account($index) {
	$account = parse_json_file("accounts.json")[$index];
	return json_encode($account);
}

function get_right_account() {
	$requests_count = get_setting("requests_count");
	$current_account_index = get_setting("current_account_index");
	$account = NULL;

	if($requests_count < LIMIT_REQUESTS_PER_ACCOUNT) {
		$account = get_account($current_account_index);
	} else {
		$next_account_index = $current_account_index + 1;
		if(($current_account_index + 1) >= COUNT_ACCOUNTS) {
			$next_account_index = 0;
		}
		set_setting("current_account_index", $next_account_index);
		set_setting("requests_count", 0);
		$account = get_account($current_account_index);
	}

	return $account;
}

increment_requests_count();

$account = get_right_account();

echo $account;
