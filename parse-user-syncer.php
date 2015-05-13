<?php

//pull in the auth/setings stuff
require('auth-info.php');


// Create DB connection
$con=mysqli_connect($db_host,$db_user,$db_pass,$db_db);

// Check connection
if (mysqli_connect_errno($con))
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//where are we lookgin for parse users?
$parse_url = "https://api.parse.com/1/users";

//get all the users from parse
$parseUsers = parseGet($parse_headers, $parse_url);
$parseUsers = $parseUsers['results'];
echo "<pre>";

$users = array();
foreach ($parseUsers as $user) {
	if(!array_key_exists('authData', $user)){
		continue;
	}
	
	$users[] = array(
		"parse" => $user['objectId'],
		"twitter" => array_key_exists("twitter", $user['authData']) ? $user['authData']['twitter']['screen_name']: "",
		"facebook" => (array_key_exists("facebook", $user['authData']))? $user['authData']['facebook']['id'] : "",
		"facebook_token" =>(array_key_exists("facebook", $user['authData']))? $user['authData']['facebook']['access_token']: ""
	);
}
//build our query
$values = array();
$columns = "";
foreach($users as $user){
	
	$columns = implode(", ",array_keys($user));
	$escaped_values = array_map('mysqli_real_escape_string', array_fill(0 , count($user) , $con),array_values($user));

	$userArray = array();
	foreach($escaped_values as $value){
		$userArray[] = '"' . $value . '"';
	}
	$values[] = "(" . implode(", ", $userArray) . ")";
	
}


 	
	$query = "INSERT INTO `users` ($columns) VALUES ".implode(", ", $values).
	" ON DUPLICATE KEY UPDATE parse = parse";
	
	
	if(count($values) != 0){
		$result = mysqli_query($con,$query);
	}

echo $result;


//util functions
function parseGet($headers, $url){

	$curl = curl_init();
	
	curl_setopt($curl, CURLOPT_URL,  $url);
	curl_setopt($curl, CURLOPT_USERAGENT, 'trycapture.com-update-engine/1.0');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	
	$curl_response = curl_exec($curl);
    curl_close($curl);
	return json_decode($curl_response,TRUE);
}


echo $logger->logThis(array("message"=>"users synced", "value" => count($parseUsers)));

?>