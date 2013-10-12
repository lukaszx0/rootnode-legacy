<?php
# PayU callback
# Rootnode http://rootnode.net
#
# Copyright (C) 2011 Marcin Hlybin
# All rights reserved.

include('config.inc.php');

## check POST parameters
foreach(array('pos_id','session_id','ts','sig') as $param) {
	if(!isset($_POST[$param])) {
		die("Error: missing parameter $param");
	}
}
unset($var);

if($_POST['pos_id'] != PAYU_POS_ID) {
	die("Error: wrong pos_id");
}

$sig_recv = md5(
            $_POST['pos_id']
          . $_POST['session_id'] 
          . $_POST['ts'] 
          . PAYU_KEY_RECV
);

if($_POST['sig'] != $sig_recv) {
	die("Error: wrong signature");
}

## get status from PayU
$ts = time();
$sig_send = md5(
            PAYU_POS_ID
          . $_POST['session_id']
          . $ts
          . PAYU_KEY_SEND
);

$params = "pos_id=".PAYU_POS_ID
	. "&session_id=".$_POST['session_id']
	. "&ts=".$ts 
	. "&sig=".$sig_send;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.platnosci.pl/paygw/UTF/Payment/get");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

# xml to array
$xml = simplexml_load_string($response);
$json = json_encode($xml);
$response = json_decode($json, TRUE); 

# change empty arrays to string
foreach(array_keys($response['trans']) as $name) {
	if(empty($response['trans'][$name])) {
		$response['trans'][$name] = "";
	}
}
unset($name);

# connect to database
$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if(!$dbh) {
	die("Cannot connect to database!");
}

mysql_select_db(DB_NAME, $dbh);

# get login
#preg_match('/^(.+)_\d+$/',$response['trans']['session_id'],$login);
#$login=$login[1];

# save to database
$payment_query = mysql_query("REPLACE INTO payu VALUES('"
	       . mysql_real_escape_string($response['trans']['desc'])."','"
	       . mysql_real_escape_string($response['trans']['id'])."','"
	       . mysql_real_escape_string($response['trans']['session_id'])."','"
	       . mysql_real_escape_string($response['trans']['amount'])."','"
	       . mysql_real_escape_string($response['trans']['status'])."','"
	       . mysql_real_escape_string($response['trans']['pay_type'])."','"
	       . mysql_real_escape_string($response['trans']['create'])."','"
	       . mysql_real_escape_string($response['trans']['init'])."','"
	       . mysql_real_escape_string($response['trans']['sent'])."','"
	       . mysql_real_escape_string($response['trans']['recv'])."','"
	       . mysql_real_escape_string($response['trans']['cancel'])."','"
               . "0')"
);

if(!$payment_query) {
	die("Invalid payment query: ".mysql_error());
}

# remove user hash
if($response['trans']['status'] == 99) {
	$user_query = mysql_query("DELETE FROM requests WHERE uid='"
	            . mysql_real_escape_string(trim($response['trans']['desc']))."'"
	);

	if(!$user_query) {
		die("Invalid user query: ".mysql_error());
	}
}

mysql_close($dbh);
echo 'OK';
?>
