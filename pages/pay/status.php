<?php

# Status page
# Rootnode http://rootnode.net
#
# Copyright (C) 2011 Marcin Hlybin
# All rights reserved.

?>

<html>
	<head>
		<meta charset="UTF-8" />
		<title>Payment status</title>
	</head>
	<body>
		<?php
		$script_name  = $_SERVER['SCRIPT_NAME'];  # /pay/status.php
		$document_uri = $_SERVER['DOCUMENT_URI']; # /pay/status.php/var1/var2

		$params = str_replace($script_name.'/', NULL, $document_uri);
		$params = explode('/',$params);

		if(sizeof($params) > 2) {
			die("Incorrect parameters.");
		}

		$session_id = $params[0];
		$error = isset($params[1]) ? $params[1] : "";

		# lang
		preg_match('/_(\w{2})$/', $session_id, $lang);
		if(!isset($lang[1])) {
			die("Incorrect parameters.");
		} 	
		$lang = $lang[1];

		# payment failed
		if($error) {
			switch($lang) {
				case 'pl': echo "Płatność niezrealizowana! Błąd numer $error."; break;
				default:   echo "Payment failed! Error code $error";
			}
		# payment approved
		} else {
			switch($lang) {
				case 'pl': echo "Płatność zaakceptowana. Dziękujemy."; break;
				default:   echo "Payment successful. Thank you.";
			}
		}
		?>
	</body>
</html>
