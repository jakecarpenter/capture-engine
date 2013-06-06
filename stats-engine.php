<?php
//db info
require("auth-info.php");

/**
 * 
 */
class logger {
	private $con;
	private $db_host = "localhost";
	private $db_user = "capture";
	private $db_pass = "2insecure";
	private $db_db = "capture";
	private $db_stats_table = "stats";
	
	private $et;
	
	private $this_file;
	
	function __construct($file_name) {
		//start time
		$this->et = microtime(TRUE);
		
		//this file
		$file_array = explode("/",$file_name);
		$this->this_file = $file_array[count($file_array) - 1];
				
		// Create DB connection
		$this->con=mysqli_connect($this->db_host,$this->db_user,$this->db_pass,$this->db_db);
		
		// Check connection
		if (mysqli_connect_errno($this->con))
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }
	}
	
	public function logThis($data){

	  
	//current time
	$now = date(DATE_ATOM, time());
	
	//how long did it take?
	$elapsed = microtime(TRUE) - $this->et;
	
	//what are we sending up?
	$message = isset($data['message'])? $data['message'] : "--";
	
	$value = isset($data['value'])? $data['value'] : "--";
	
	
	 	
	$query = "INSERT INTO `stats` (message, value, elapsed, script) VALUES ('".$message."','".$value."','".$elapsed."','".$this->this_file."')";

	mysqli_query($this->con,$query);
	
	return $now." -- ".$this->this_file." -- ".$message.": ".$value." in: ".$elapsed." seconds.";
	

}
}




?>