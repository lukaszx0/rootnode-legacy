<header id="overview" class="jumbotron subhead">
    <div class="container">
        <h1>Payment</h1>
        <p class="lead">Shell account and recovery fund</p>
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="span9">

        <section id="pricing">
          <div class="page-header">
            <h1>Payment form</h1>
          </div>

	<p>Please fill up the form. Your personal data will be used only for accounting purposes. Real data is required by law.</p>
	<p><span class="label label-warning">Notice</span> Prices exclude tax 23% VAT.</p>

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

$pay_key = $_GET['id'];
if(!isset($pay_key) || !preg_match('/^[a-z0-9]+$/',$pay_key)) {
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
        SELECT uid, user_name, price
        FROM requests
        WHERE pay_key='".mysql_real_escape_string($pay_key)."'"
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
		<div class="row">
		<div class="span9">
		<form action="/pay/form.php" method="POST" name="form" class="form-horizontal validate-me">
            <div class="row">
                <div class="control-group">
                    <div class="controls">
                        <label class="checkbox" for="invoice">
                            <input type="checkbox" name="invoice" id="invoice"<?php echo isset($_POST['invoice']) ? ' checked="checked"' : ''; ?>> I would like to get an invoice
                        </label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">User name</label>
                    <div class="controls controls-row">
                        <span class="span4 uneditable-input"><?php echo $user['user_name']; ?></span>
                    </div>
                </div>
            </div>
            <div id="splitdata">
                <div id="privateperson" class="row">
                    <div class="control-group<?php valid_value( 'first_name' ); ?>">
                        <label class="control-label" for="first_name">First name</label>
                        <div class="controls controls-row">
                            <input type="text" id="first_name" name="first_name" class="span4" data-required="1" value="<?php show_value( 'first_name' ); ?>" />
                            <span class="help-inline error-message">First name cannot be empty</span>
                        </div>
                    </div>
                    <div class="control-group<?php valid_value( 'last_name' ); ?>">
                        <label class="control-label" for="last_name">Last name</label>
                        <div class="controls controls-row">
                            <input type="text" id="last_name" name="last_name" class="span4" data-required="1" value="<?php show_value( 'last_name' ); ?>" />
                            <span class="help-inline error-message">Last name cannot be empty</span>
                        </div>
                    </div>
                </div>
                <div id="company" class="row">
                    <div class="control-group<?php valid_value( 'company_name' ); ?>">
                        <label class="control-label" for="company_name">Company name</label>
                        <div class="controls controls-row">
                            <input type="text" id="company_name" name="company_name" class="span4" data-required="1" value="<?php show_value( 'company_name' ); ?>" />
                            <span class="help-inline error-message">Company name cannot be empty</span>
                        </div>
                    </div>
                    <div class="control-group<?php valid_value( 'vat_number' ); ?>">
                        <label class="control-label" for="vat_number">VAT number</label>
                        <div class="controls controls-row">
                            <input type="text" id="vat_number" name="vat_number" class="span4" placeholder="PL123-456-78-90" data-required="1" data-format="[a-zA-Z]{0,2}[0-9-\s]{4,}" value="<?php show_value( 'vat_number' ); ?>" />
                            <span class="help-inline error-message">Invalid VAT format</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="common-info row">
                <div class="control-group<?php valid_value( 'mail' ); ?>">
                    <label class="control-label" for="mail">E-mail</label>
                    <div class="controls controls-row">
                        <input type="text" id="mail" name="mail" class="span4" data-required="1" data-format="[A-Za-z0-9._%+-]+@(?:[A-Za-z0-9-]+\.)+[A-Za-z]{2,4}" value="<?php show_value( 'mail' ); ?>" />
                        <span class="help-inline error-message">Invalid Email format</span>
                    </div>
                </div>
                <div class="control-group<?php valid_value( 'street' ); ?>">
                    <label class="control-label" for="street">Address</label>
                    <div class="controls controls-row">
                        <input type="text" id="street" name="street" placeholder="Street" class="span4" data-required="1" value="<?php show_value( 'street' ); ?>" />
                        <span class="help-inline error-message">Street cannot be empty</span>
                    </div>
                </div>
                <div class="control-group<?php echo (valid_value( 'postcode', false ) && valid_value( 'city', false )) ? '' : ' error'; ?>">
                    <div class="controls controls-row">
                        <input type="text" id="postcode" name="postcode" class="span2" placeholder="Postcode" data-required="1"  data-format="[a-zA-Z0-9-\s]{2,}" value="<?php show_value( 'postcode' ); ?>"/>
                        <input type="text" id="city" name="city" class="span2" placeholder="City" data-required="1" value="<?php show_value( 'city' ); ?>"/>
                        <span class="help-inline error-message">One of the fields is wrong</span>
                    </div>
                </div>
                <div class="control-group">
                    <label for="country" class="control-label">Country</label>
                    <div class="controls controls-row">
                        <select class="span4" name="country" id="country">
                            <option value="AF">Afghanistan</option>
                            <option value="AX">Åland Islands</option>
                            <option value="AL">Albania</option>
                            <option value="DZ">Algeria</option>
                            <option value="AS">American Samoa</option>
                            <option value="AD">Andorra</option>
                            <option value="AO">Angola</option>
                            <option value="AI">Anguilla</option>
                            <option value="AQ">Antarctica</option>
                            <option value="AG">Antigua and Barbuda</option>
                            <option value="AR">Argentina</option>
                            <option value="AM">Armenia</option>
                            <option value="AW">Aruba</option>
                            <option value="AU">Australia</option>
                            <option value="AT">Austria</option>
                            <option value="AZ">Azerbaijan</option>
                            <option value="BS">Bahamas</option>
                            <option value="BH">Bahrain</option>
                            <option value="BD">Bangladesh</option>
                            <option value="BB">Barbados</option>
                            <option value="BY">Belarus</option>
                            <option value="BE">Belgium</option>
                            <option value="BZ">Belize</option>
                            <option value="BJ">Benin</option>
                            <option value="BM">Bermuda</option>
                            <option value="BT">Bhutan</option>
                            <option value="BO">Bolivia, Plurinational State of</option>
                            <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                            <option value="BA">Bosnia and Herzegovina</option>
                            <option value="BW">Botswana</option>
                            <option value="BV">Bouvet Island</option>
                            <option value="BR">Brazil</option>
                            <option value="IO">British Indian Ocean Territory</option>
                            <option value="BN">Brunei Darussalam</option>
                            <option value="BG">Bulgaria</option>
                            <option value="BF">Burkina Faso</option>
                            <option value="BI">Burundi</option>
                            <option value="KH">Cambodia</option>
                            <option value="CM">Cameroon</option>
                            <option value="CA">Canada</option>
                            <option value="CV">Cape Verde</option>
                            <option value="KY">Cayman Islands</option>
                            <option value="CF">Central African Republic</option>
                            <option value="TD">Chad</option>
                            <option value="CL">Chile</option>
                            <option value="CN">China</option>
                            <option value="CX">Christmas Island</option>
                            <option value="CC">Cocos (Keeling) Islands</option>
                            <option value="CO">Colombia</option>
                            <option value="KM">Comoros</option>
                            <option value="CG">Congo</option>
                            <option value="CD">Congo, the Democratic Republic of the</option>
                            <option value="CK">Cook Islands</option>
                            <option value="CR">Costa Rica</option>
                            <option value="CI">Côte d'Ivoire</option>
                            <option value="HR">Croatia</option>
                            <option value="CU">Cuba</option>
                            <option value="CW">Curaçao</option>
                            <option value="CY">Cyprus</option>
                            <option value="CZ">Czech Republic</option>
                            <option value="DK">Denmark</option>
                            <option value="DJ">Djibouti</option>
                            <option value="DM">Dominica</option>
                            <option value="DO">Dominican Republic</option>
                            <option value="EC">Ecuador</option>
                            <option value="EG">Egypt</option>
                            <option value="SV">El Salvador</option>
                            <option value="GQ">Equatorial Guinea</option>
                            <option value="ER">Eritrea</option>
                            <option value="EE">Estonia</option>
                            <option value="ET">Ethiopia</option>
                            <option value="FK">Falkland Islands (Malvinas)</option>
                            <option value="FO">Faroe Islands</option>
                            <option value="FJ">Fiji</option>
                            <option value="FI">Finland</option>
                            <option value="FR">France</option>
                            <option value="GF">French Guiana</option>
                            <option value="PF">French Polynesia</option>
                            <option value="TF">French Southern Territories</option>
                            <option value="GA">Gabon</option>
                            <option value="GM">Gambia</option>
                            <option value="GE">Georgia</option>
                            <option value="DE">Germany</option>
                            <option value="GH">Ghana</option>
                            <option value="GI">Gibraltar</option>
                            <option value="GR">Greece</option>
                            <option value="GL">Greenland</option>
                            <option value="GD">Grenada</option>
                            <option value="GP">Guadeloupe</option>
                            <option value="GU">Guam</option>
                            <option value="GT">Guatemala</option>
                            <option value="GG">Guernsey</option>
                            <option value="GN">Guinea</option>
                            <option value="GW">Guinea-Bissau</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haiti</option>
                            <option value="HM">Heard Island and McDonald Islands</option>
                            <option value="VA">Holy See (Vatican City State)</option>
                            <option value="HN">Honduras</option>
                            <option value="HK">Hong Kong</option>
                            <option value="HU">Hungary</option>
                            <option value="IS">Iceland</option>
                            <option value="IN">India</option>
                            <option value="ID">Indonesia</option>
                            <option value="IR">Iran, Islamic Republic of</option>
                            <option value="IQ">Iraq</option>
                            <option value="IE">Ireland</option>
                            <option value="IM">Isle of Man</option>
                            <option value="IL">Israel</option>
                            <option value="IT">Italy</option>
                            <option value="JM">Jamaica</option>
                            <option value="JP">Japan</option>
                            <option value="JE">Jersey</option>
                            <option value="JO">Jordan</option>
                            <option value="KZ">Kazakhstan</option>
                            <option value="KE">Kenya</option>
                            <option value="KI">Kiribati</option>
                            <option value="KP">Korea, Democratic People's Republic of</option>
                            <option value="KR">Korea, Republic of</option>
                            <option value="KW">Kuwait</option>
                            <option value="KG">Kyrgyzstan</option>
                            <option value="LA">Lao People's Democratic Republic</option>
                            <option value="LV">Latvia</option>
                            <option value="LB">Lebanon</option>
                            <option value="LS">Lesotho</option>
                            <option value="LR">Liberia</option>
                            <option value="LY">Libya</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lithuania</option>
                            <option value="LU">Luxembourg</option>
                            <option value="MO">Macao</option>
                            <option value="MK">Macedonia, the former Yugoslav Republic of</option>
                            <option value="MG">Madagascar</option>
                            <option value="MW">Malawi</option>
                            <option value="MY">Malaysia</option>
                            <option value="MV">Maldives</option>
                            <option value="ML">Mali</option>
                            <option value="MT">Malta</option>
                            <option value="MH">Marshall Islands</option>
                            <option value="MQ">Martinique</option>
                            <option value="MR">Mauritania</option>
                            <option value="MU">Mauritius</option>
                            <option value="YT">Mayotte</option>
                            <option value="MX">Mexico</option>
                            <option value="FM">Micronesia, Federated States of</option>
                            <option value="MD">Moldova, Republic of</option>
                            <option value="MC">Monaco</option>
                            <option value="MN">Mongolia</option>
                            <option value="ME">Montenegro</option>
                            <option value="MS">Montserrat</option>
                            <option value="MA">Morocco</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Namibia</option>
                            <option value="NR">Nauru</option>
                            <option value="NP">Nepal</option>
                            <option value="NL">Netherlands</option>
                            <option value="NC">New Caledonia</option>
                            <option value="NZ">New Zealand</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Niger</option>
                            <option value="NG">Nigeria</option>
                            <option value="NU">Niue</option>
                            <option value="NF">Norfolk Island</option>
                            <option value="MP">Northern Mariana Islands</option>
                            <option value="NO">Norway</option>
                            <option value="OM">Oman</option>
                            <option value="PK">Pakistan</option>
                            <option value="PW">Palau</option>
                            <option value="PS">Palestinian Territory, Occupied</option>
                            <option value="PA">Panama</option>
                            <option value="PG">Papua New Guinea</option>
                            <option value="PY">Paraguay</option>
                            <option value="PE">Peru</option>
                            <option value="PH">Philippines</option>
                            <option value="PN">Pitcairn</option>
                            <option value="PL" selected="selected">Poland</option>
                            <option value="PT">Portugal</option>
                            <option value="PR">Puerto Rico</option>
                            <option value="QA">Qatar</option>
                            <option value="RE">Réunion</option>
                            <option value="RO">Romania</option>
                            <option value="RU">Russian Federation</option>
                            <option value="RW">Rwanda</option>
                            <option value="BL">Saint Barthélemy</option>
                            <option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
                            <option value="KN">Saint Kitts and Nevis</option>
                            <option value="LC">Saint Lucia</option>
                            <option value="MF">Saint Martin (French part)</option>
                            <option value="PM">Saint Pierre and Miquelon</option>
                            <option value="VC">Saint Vincent and the Grenadines</option>
                            <option value="WS">Samoa</option>
                            <option value="SM">San Marino</option>
                            <option value="ST">Sao Tome and Principe</option>
                            <option value="SA">Saudi Arabia</option>
                            <option value="SN">Senegal</option>
                            <option value="RS">Serbia</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leone</option>
                            <option value="SG">Singapore</option>
                            <option value="SX">Sint Maarten (Dutch part)</option>
                            <option value="SK">Slovakia</option>
                            <option value="SI">Slovenia</option>
                            <option value="SB">Solomon Islands</option>
                            <option value="SO">Somalia</option>
                            <option value="ZA">South Africa</option>
                            <option value="GS">South Georgia and the South Sandwich Islands</option>
                            <option value="SS">South Sudan</option>
                            <option value="ES">Spain</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="SD">Sudan</option>
                            <option value="SR">Suriname</option>
                            <option value="SJ">Svalbard and Jan Mayen</option>
                            <option value="SZ">Swaziland</option>
                            <option value="SE">Sweden</option>
                            <option value="CH">Switzerland</option>
                            <option value="SY">Syrian Arab Republic</option>
                            <option value="TW">Taiwan, Province of China</option>
                            <option value="TJ">Tajikistan</option>
                            <option value="TZ">Tanzania, United Republic of</option>
                            <option value="TH">Thailand</option>
                            <option value="TL">Timor-Leste</option>
                            <option value="TG">Togo</option>
                            <option value="TK">Tokelau</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinidad and Tobago</option>
                            <option value="TN">Tunisia</option>
                            <option value="TR">Turkey</option>
                            <option value="TM">Turkmenistan</option>
                            <option value="TC">Turks and Caicos Islands</option>
                            <option value="TV">Tuvalu</option>
                            <option value="UG">Uganda</option>
                            <option value="UA">Ukraine</option>
                            <option value="AE">United Arab Emirates</option>
                            <option value="GB">United Kingdom</option>
                            <option value="US">United States</option>
                            <option value="UM">United States Minor Outlying Islands</option>
                            <option value="UY">Uruguay</option>
                            <option value="UZ">Uzbekistan</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VE">Venezuela, Bolivarian Republic of</option>
                            <option value="VN">Viet Nam</option>
                            <option value="VG">Virgin Islands, British</option>
                            <option value="VI">Virgin Islands, U.S.</option>
                            <option value="WF">Wallis and Futuna</option>
                            <option value="EH">Western Sahara</option>
                            <option value="YE">Yemen</option>
                            <option value="ZM">Zambia</option>
                            <option value="ZW">Zimbabwe</option>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label for="price" class="control-label">Price</label>
                    <div class="controls">
                        <select class="span4" name="price" id="price">
                            <option value="12000">120 PLN / yr</option>
                        </select>
                        <span class="help-block">Prices exclude tax (23% VAT).</span>
                    </div>
                </div>
                <div class="control-group">
                    <label for="fund" class="control-label">Recovery fund</label>
                    <div class="controls">
                        <select class="span4" name="fund" id="fund">
                            <option value="000">0 PLN</option>
                            <option value="3000">30 PLN</option>
                            <option value="6000">60 PLN</option>
                            <option selected="selected" value="9000">90 PLN</option>
                            <option value="12000">120 PLN</option>
                        </select>
                        <span class="help-block">Extra money from <strong>recovery fund</strong> will get us back on our feet.</span>
                    </div>
                </div>
            </div>

			<input type="hidden" name="id" id="id" value="<?php echo $pay_key; ?>">
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
                    <a href="#pricing">
                        <i class="icon-chevron-left"></i>
			Pricing
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
