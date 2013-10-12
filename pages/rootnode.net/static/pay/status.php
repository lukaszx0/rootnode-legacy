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
                $document_uri = $_SERVER['PATH_INFO'];    # /pay/status.php/var1/var2

                $params = explode('/', $document_uri);
                array_shift($params);

                if(sizeof($params) > 2) {
                        die("Incorrect parameters.");
                }

                $session_id = $params[0];
                $error = isset($params[1]) ? $params[1] : "";

		# payment failed
		if($error) {
			echo "Payment failed! Error code $error";
		# payment approved
		} else {
			echo "Payment successful. Thank you.";
		}
		?>
	</body>
</html>
