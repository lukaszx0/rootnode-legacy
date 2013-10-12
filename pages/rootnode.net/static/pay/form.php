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
    array('street', false),
    array('postcode', '[a-zA-Z0-9-\s]{2,}'),
    array('city', false)
) );



if(!empty($_POST['invoice'])) {
    check_fields( array( array('company_name', false), array('vat_number', '[a-zA-Z]{0,2}[0-9-\s]{4,}') ) );
} else {
    check_fields( array( array('first_name', false), array('last_name', false) ) );
}

if( !empty( $invalid_fields ) ) {
    $_GET['id'] = $_POST['id'];
    require('index.php');die;
}

$_POST['country'] = empty($_POST['country']) ? 'PL' : $_POST['country'];
$_POST['fund'] = empty($_POST['fund']) ? '000' : $_POST['fund'];

if(!isset($_POST['id']) || !preg_match('/^[a-z0-9]+$/',$_POST['id'])) {
	die("Incorrect URL. Please use 'satan account pay' command.");
}

$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if(!$dbh) {
        die("Cannot connect to database!");
}

mysql_select_db(DB_NAME, $dbh);

$request_query = mysql_query("
	SELECT uid, user_name, price
	FROM requests
	WHERE pay_key='".mysql_real_escape_string($_POST['id'])."'"
);

if(!$request_query) {
        die("Invalid query: ".mysql_error());
}

$user = mysql_fetch_array($request_query);

if(empty($user)) {
	die("Incorrect URL. Please use 'satan account pay' command.");
}


$ts = time();
$client_ip = $_SERVER['REMOTE_ADDR'];
$session_id = $user['user_name'].'_'.$ts;
$language = 'en';

$fund = $_POST['fund'];
if(!preg_match('/^[0-9]+$/', $fund)) {
	die("Incorrect fund value");
}

$price = $user['price'] + (int)$fund;
$price = $price*1.23;

# Save user data
$user_query = mysql_query("INSERT INTO users (uid, first_name, last_name, company_name, vat_number, street, postcode, city, country, mail, invoice) VALUES('"
	    . mysql_real_escape_string($user['uid'])."','"
	    . mysql_real_escape_string(@$_POST['first_name'])."','"
	    . mysql_real_escape_string(@$_POST['last_name'])."','"
            . mysql_real_escape_string(@$_POST['company_name'])."','"
            . mysql_real_escape_string(@$_POST['vat_number'])."','"
	    . mysql_real_escape_string($_POST['street'])."','"
	    . mysql_real_escape_string($_POST['postcode'])."','"
	    . mysql_real_escape_string($_POST['city'])."','"
	    . mysql_real_escape_string($_POST['country'])."','"
	    . mysql_real_escape_string($_POST['mail'])."','"
            . mysql_real_escape_string(@$_POST['invoice'])."')"
);

if(!$user_query) {
	die("Cannot add data to database: ".mysql_error());
}

$sig = md5(PAYU_POS_ID
     . $session_id
     . PAYU_POS_AUTH_KEY
     . $price
     . $user['uid']
     . $_POST['first_name']
     . $_POST['last_name']
     . $_POST['street']
     . $_POST['city']
     . $_POST['postcode']
     . $_POST['country']
     . $_POST['mail']
     . $language
     . $client_ip
     . $ts
     . PAYU_KEY_SEND
);
     
$url = "https://www.platnosci.pl/paygw/UTF/NewPayment"
     . "?pos_id="      . PAYU_POS_ID
     . "&session_id="  . $session_id
     . "&pos_auth_key=". PAYU_POS_AUTH_KEY
     . "&amount="      . $price
     . "&desc="        . $user['uid']
     . "&first_name="  . urlencode($_POST['first_name'])
     . "&last_name="   . urlencode($_POST['last_name'])
     . "&street="      . urlencode($_POST['street'])
     . "&city="        . urlencode($_POST['city'])
     . "&post_code="   . urlencode($_POST['postcode'])
     . "&country="     . urlencode($_POST['country'])
     . "&email="       . urlencode($_POST['mail'])
     . "&language="    . $language
     . "&client_ip="   . $client_ip
     . "&ts="          . $ts
     . "&sig="         . $sig;

header("Location: $url");
?>
