<?php

/**
*
*  Include auth info for the  various services, also include settings that are relevant to all three scripts.
*
*/

$capture_domain = "trycapture.com";

$capture_event =  "TUGT7fbghB";

//db stuff
$db_host = "localhost";
$db_user = "capture";
$db_pass = "2insecure";
$db_db = "capture";

//twitter stuff
$auth = array();
$auth['oauth_access_token'] = '13847502-UBL9TqXz17T93lLkIwMvCeqWQTf5bJFUaZYuoo';
$auth['oauth_access_token_secret'] = 'KBXmWjEzZQivp5Y3dpIOpVVySEnAanfi4CaRY9A6Y2M';
$auth['consumer_key'] = 'WTjy8aqpg5fo7MNrjjYfQ';
$auth['consumer_secret'] = 'SNeFuSp9UXyXZX2HujW2lnojmvv4VyoskDXG02PYCM';


$parse_url = "https://api.parse.com";
$parse_headers = array(
   'X-Parse-Application-Id:  JfuHcRkELk91tbejwxCllYPRyauk3s4jCnTKQjah',
   'X-Parse-REST-API-Key: LO8kGXmk83QlC2vQI1QGcEkt3cNDeIC2RHNogfpn'
);

?>
