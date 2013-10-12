<?php
include("config.inc.php");

if(!isset($_POST['id']) || !preg_match('/^[a-z0-9]+$/',$_POST['id'])) {
	die("Incorrect URL. Please use 'satan account pay' command.");
}

$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if(!$dbh) {
        die("Cannot connect to database!");
}

mysql_select_db(DB_NAME, $dbh);

$request_query = mysql_query("
	SELECT uid, user_name
	FROM requests
	WHERE cancel_key='".mysql_real_escape_string($_POST['id'])."'"
);

if(!$request_query) {
        die("Invalid query: ".mysql_error());
}

$user = mysql_fetch_array($request_query);

if(empty($user)) {
	die("Incorrect URL. Please use 'satan account pay' command.");
}


# Save user data
$user_query = mysql_query("INSERT INTO cancel (uid) VALUES('"
            . mysql_real_escape_string($user['uid'])."')"
);

if(!$user_query) {
	die("Cannot add data to database: ".mysql_error());
}

# Remove request field
$request_query = mysql_query("DELETE FROM requests WHERE uid='"
	    . mysql_real_escape_string($user['uid'])."'"
);

if(!$request_query) {
	die("Invalid user query: ".mysql_error());
}

header("Location: http://rootnode.net/cancel/status/ok");
?>
