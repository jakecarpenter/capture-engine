<?php

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
while($row = mysqli_fetch_assoc($result))
  {
  	$request = array(
  						"screen_name"=>$row['twitter'],
  						//"count" => "2"
  						//"since_id"=>'334167927249055744'
						);
	
  	$user_tweets = returnTweet($auth, $request);
	
	foreach($user_tweets as $tweet){
		$tweets[] = array(
							"tweet_id" => $tweet['id_str'],
							"user" => $row['twitter'],
							"created_at" => $tweet['created_at'],
							"text" => $tweet['text'],
							"service" => "twitter"
							);
		}
  }
 
 //TEMP DEBUG!!!!
 echo "<html><pre>";
  
//build our query to insert tweets to local db
foreach($tweets as $tweet){
	$columns = implode(", ",array_keys($tweet));
	$escaped_values = array_map('mysqli_real_escape_string', array_fill(0 , count($tweet) , $con),array_values($tweet));

	$values = array();
	foreach($escaped_values as $value){
		$values[] = '"' . $value . '"';
	}
	
	$values_string  = implode(", ", $values);
	$query = "INSERT INTO `updates` ($columns) VALUES ($values_string)";
	echo $query. "  |  ";
	echo mysqli_query($con,$query);
}

 



//functions that do the dirty work.

function buildBaseString($baseURI, $method, $params) {
    $r = array();
    ksort($params);
    foreach($params as $key=>$value){
        $r[] = "$key=" . rawurlencode($value);
    }
    return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}

function buildAuthorizationHeader($oauth) {
    $r = 'Authorization: OAuth ';
    $values = array();
    foreach($oauth as $key=>$value)
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
    $r .= implode(', ', $values);
    return $r;
}

function returnTweet($auth, $my_request = array()){
    $oauth_access_token         = $auth['oauth_access_token'];
    $oauth_access_token_secret  = $auth['oauth_access_token_secret'];
    $consumer_key               = $auth['consumer_key'];
    $consumer_secret            = $auth['consumer_secret'];

    $twitter_timeline           = "user_timeline";  //  mentions_timeline / user_timeline / home_timeline / retweets_of_me

    //  create request
        $request = array(
            'trim_user'			=> 'true',
            'exclude_replies'	=> 'true'
        );
		
		$request = array_merge($request, $my_request);

    $oauth = array(
        'oauth_consumer_key'        => $consumer_key,
        'oauth_nonce'               => time(),
        'oauth_signature_method'    => 'HMAC-SHA1',
        'oauth_token'               => $oauth_access_token,
        'oauth_timestamp'           => time(),
        'oauth_version'             => '1.0'
    );

    //  merge request and oauth to one array
        $oauth = array_merge($oauth, $request);

    //  do some magic
        $base_info              = buildBaseString("https://api.twitter.com/1.1/statuses/$twitter_timeline.json", 'GET', $oauth);
        $composite_key          = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature            = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature']   = $oauth_signature;

    //  make request
        $header = array(buildAuthorizationHeader($oauth), 'Expect:');
        $options = array( CURLOPT_HTTPHEADER => $header,
                          CURLOPT_HEADER => false,
                          CURLOPT_URL => "https://api.twitter.com/1.1/statuses/$twitter_timeline.json?". http_build_query($request),
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_SSL_VERIFYPEER => false);

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);

    return json_decode($json, true);
}


?>