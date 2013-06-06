<?php

//pull in the auth/setings stuff
require('auth-info.php');

//our logger
require('stats-engine.php');
$logger = new logger(__FILE__);

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

//get all the users we already know about.
$query = "SELECT * FROM users";
$result = mysqli_query($con,$query);

$engineUsers = array();

while($row = mysqli_fetch_assoc($result)){
	$engineUsers[$row['parse']] = $row;
}

echo "<pre>";
print_r($parseUsers);
print_r($engineUsers);


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