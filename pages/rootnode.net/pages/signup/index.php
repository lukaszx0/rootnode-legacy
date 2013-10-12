<header id="overview" class="jumbotron subhead">
    <div class="container">
        <h1>Sign up!</h1>
        <p class="lead">Create a new shell account</p>
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="span9">

        <section id="signup">
          <div class="page-header">
            <h1>Add account</h1>
          </div>
	
	<p class="lead">Account price is €50 per year + 23% VAT.</p>

	<p><span class="label label-warning">Notice</span> New account will be created within 1 week.<br/>You will receive further information by e-mail.</p>
	<p>Please fill up the form.<p> 
<br/>

<?php
include_once("config.inc.php");

function show_value( $val ) {
    echo isset($_POST[$val]) ? $_POST[$val] : '';
}

function valid_value( $val, $show = true ) {
    global $invalid_fields;

    if(empty($invalid_fields)) {
        $valid=true;
    } else {
        $valid = !in_array($val, $invalid_fields);
    }

    if($show) {
        echo !$valid ? ' error' : '';
    } else {
        return $valid;
    }
}

$dbh = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if(!$dbh) {
        $error_message = "Cannot connect to database!";
        goto ERROR;
}

mysql_select_db(DB_NAME, $dbh);

?>
	<div class="row">
		<div class="span9">
			<form action="/signup/form.php" method="POST" name="form" class="form-horizontal validate-me">
				<div id="splitdata">
					<div class="common-info row">
						<div class="control-group<?php valid_value( 'user_name' ); ?>">
							<label class="control-label" for="user_name">User name</label>
							<div class="controls controls-row">
								<input type="text" id="user_name" name="user_name" class="span4" data-required="1" data-format="^[a-z0-9]{2,32}$" value="<?php show_value( 'user_name' ); ?>" />
								<span class="help-inline error-message">Invalid user name</span>
							</div>
						</div>

						<div class="control-group<?php valid_value( 'mail' ); ?>">
							<label class="control-label" for="mail">Mail</label>
							<div class="controls controls-row">
								<input type="text" id="mail" name="mail" class="span4" data-required="1" data-format="[A-Za-z0-9._%+-]+@(?:[A-Za-z0-9-]+\.)+[A-Za-z]{2,4}" value="<?php show_value( 'mail' ); ?>" />
								<span class="help-inline error-message">Invalid e-mail format</span>
							</div>
						</div>
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>

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
                    <a href="#signup">
                        <i class="icon-chevron-left"></i>
			Signup
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
