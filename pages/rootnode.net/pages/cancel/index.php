<header id="overview" class="jumbotron subhead">
    <div class="container">
        <h1>Cancel</h1>
        <p class="lead">This action will remove your Rootnode account</p>
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="span9">

        <section id="cancel">
          <div class="page-header">
            <h1>Remove account</h1>
          </div>
<?php
include_once("config.inc.php");

$cancel_key = $_GET['id'];
if(!isset($cancel_key) || !preg_match('/^[a-z0-9]+$/', $cancel_key)) {
        $error_message = "Incorrect URL. Please use 'satan account pay' command.";
        goto ERROR;
}

$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if(!$dbh) {
        $error_message = "Cannot connect to database!";
        goto ERROR;
}

mysql_select_db(DB_NAME, $dbh);

$user_query = mysql_query("
        SELECT uid, user_name
        FROM requests
        WHERE cancel_key='".mysql_real_escape_string($cancel_key)."'"
);

if(!$user_query) {
        $error_message = "Invalid query: " . mysql_error();
        goto ERROR;
}

$user = mysql_fetch_array($user_query);

if(empty($user)) {
        $error_message = "Incorrect URL. Please use 'satan account pay' command.";
        goto ERROR;
}
?>
		<form action="/cancel/form.php" method="POST" name="form" class="form-horizontal">
			    <div class="row">
				<div class="control-group">
				    <div class="controls">
					<label class="checkbox" for="invoice">
					    <input type="checkbox" name="cancel" id="cancel"/> Remove account for user <strong><?php echo $user['user_name']; ?></strong>
					</label>
				    </div>
				</div>

				<input type="hidden" name="id" id="id" value="<?php echo $cancel_key; ?>">
           		        <div class="form-actions">
         				<button type="submit" class="btn btn-primary">Submit</button>
        			</div>
		</form>

<?php
ERROR:
        if (!empty($error_message)) {
                echo "<p>$error_message</p>";
        }
?>
	</section>
        </div>
        <div class="span3 bs-docs-sidebar">
            <ul class="nav nav-list bs-docs-sidenav">
                <li>
                    <a href="#cancel">
                        <i class="icon-chevron-left"></i>
			Remove account
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
