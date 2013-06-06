<?php
//our logger
require('stats-engine.php');
$logger = new logger(__FILE__);


//pull in the auth/setings stuff
require('auth-info.php');

//grab the users that we're watching, and the last tweet 
$users = array();
// Create DB connection
$con=mysqli_connect($db_host,$db_user,$db_pass,$db_db);

// Check connection
if (mysqli_connect_errno($con))
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//get users for the event we're tracking.
$query = "SELECT * FROM updates WHERE inserted='false' AND approved='true' ";
$result = mysqli_query($con,$query);

//setup counters to keep track of results. 
$updates = 0;

while($row = mysqli_fetch_assoc($result)){
	//take each update, insert it to parse
	$parseData = array(
		"serviceTime" => array(
			"__type" => "Date",
			"iso"  => date("c", strtotime($row['created_at']))
		),
		
		//"user"=> $row['parse_user'],
		"user"=> array(
			"__type"=> "Pointer",
			"className" => "_User",
			"objectId" => $row['parse_user']
		),
		"service"=> $row['service'],
		"data" => $row['text'],
		"event" => array(
			"__type" => "Pointer",
			"className" => "Event",
			"objectId" => $capture_event
		)
	);
	
	$curl = curl_init();
	
	//add the content type for the data to the headers;
	$parse_headers[] = "Content-type: application/json";
	
	curl_setopt($curl, CURLOPT_URL,  $parse_url . "/1/classes/Photo");
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_USERAGENT, 'trycapture.com-update-engine/1.0');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $parse_headers);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($parseData));
	
	$curl_response = curl_exec($curl);
    curl_close($curl);
	//echo $curl_response;
	//mark it as inserted.
	
	mysqli_query($con, "UPDATE updates SET inserted=true WHERE id='". $row['id'] ."'");
	$updates += 1;
}

echo $logger->logThis(array("message"=>"updates inserted", "value" => $updates));


?>
