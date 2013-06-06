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

//we are gonna need a place for the tweets we find
$tweets = array();

//get users for the event we're tracking.
$query = "SELECT * FROM users WHERE event='". $capture_event ."'";
$result = mysqli_query($con,$query);

$posts = array();
while($row = mysqli_fetch_assoc($result)){
	$posts[] = get_posts($row['facebook'],$row['facebook_token']);
}
echo "<html><pre>";
print_r($posts);

function get_posts($user, $access_token){
	


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/".$user."/posts?access_token=".$access_token);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT_MS,20000);

$sendCH = curl_exec($ch);
curl_close($ch);
return $sendCH;
};

?>