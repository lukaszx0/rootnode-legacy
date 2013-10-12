<?php
include("config.inc.php");

global $invalid_fields;
$invalid_fields = array();

function check_fields( $fields ) {
    global $invalid_fields;
    foreach( $fields as $req ) {
        if( empty($_POST[$req[0]]) || ($req[1] && !preg_match('/' . $req[1] . '/', $_POST[$req[0]])) ) { $invalid_fields[] = $req[0]; }
    }
}

check_fields( array(
    array('mail', '[A-Za-z0-9._%+-]+@(?:[A-Za-z0-9-]+\.)+[A-Za-z]{2,4}'),
    array('user_name', '^[a-z0-9]{2,32}$')
) );

if( !empty( $invalid_fields ) ) {
    #$_GET['id'] = $_POST['id'];
    require('index.php');die;
}

$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if(!$dbh) {
        die("Cannot connect to database!");
}

mysql_select_db(DB_NAME, $dbh);

# Save user data
$user_query = mysql_query("INSERT INTO users (created_at, user_name, mail, ip_addr) VALUES(NOW(), '"
            . mysql_real_escape_string($_POST['user_name']) . "','"
            . mysql_real_escape_string($_POST['mail']) . "','"
	    . $_SERVER['REMOTE_ADDR'] . "')"
);

if(!$user_query) {
	die ("Cannot add data to database: " . mysql_error());
}

header("Location: http://rootnode.net/signup/status/ok");

?>
