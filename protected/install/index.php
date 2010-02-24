<?php

	$root = dirname( __FILE__ ) . '/../../';
	$all_clear = true;

// 	if( file_exists( $root . 'protected/config/config.ini' ) )
// 		die( 'Config file exists. Please remove this file or remove the config.ini to re-install.' );

	function show_check_div ( $string, $class ) {
		echo '<div class="' . $class . '">' . $string . '</div>';
	}

	function good ( $string ) { show_check_div( $string, 'good' ); }
	function bad ( $string ) { global $all_clear; $all_clear = false; show_check_div( $string, 'bad' ); }
	function warn ( $string ) { show_check_div( $string, 'warn' ); }

	// Check the level of php available
	function check_php ( $required ) {
		if( 1 == version_compare( $required, phpversion() ) )
			bad( "Your PHP version is too low, the minimum required is $required." );
		else
			good( "PHP Version " . phpversion() . " meets requirement." );
	}

	function check_setting ( $setting, $expected ) {
		if( $expected != ini_get( $setting ) )
			bad( "PHP Setting '$setting' should be '". var_export( $expected, true ) . "'." );
		else
			good( "PHP Setting '$setting' is '" . var_export( $expected, true ) ."'." );
	}

	// Check if a class exists
	function check_class ( $class, $name, $warn_only=false ) {
		if( class_exists( $class, false ) )
			good( "Found $name." );
		else if( $warn_only )
			warn( "Can not find $name." );
		else
			bad( "Can not find $name." );
	}

	// Check if a function exists.
	function check_function ( $function, $name, $warn_only=false ) {
		if( function_exists( $function ) )
			good( "Found $name." );
		else if( $warn_only )
			warn( "Can not find $name." );
		else
			bad( "Can not find $name." );
	}

	// Check if a file can be included, is on the path.
	function check_include ( $include, $name, $warn_only=false ) {
		global $include_found;
		$include_found = true;
		set_error_handler( 'include_error_handler', E_WARNING );
		include_once( $include );
		restore_error_handler();
		if( $include_found )
			good( "Found $name." );
		else if( $warn_only )
			warn( "Can not find $name." );
		else
			bad( "Can not find $name." );
		return $include_found;
	}
	$include_found = true;
	function include_error_handler ( $errno, $errstr ) {
		global $include_found;
		$include_found = false;
	}

	// Checks an extension existence by phpversion. Doesn't work for all extensions.
	function check_extension ( $extension, $name, $warn_only=false ) {
		if( false !== phpversion( $extension ) )
			good( "Found $name." );
		else if( $warn_only )
			warn( "Can not find $name." );
		else
			bad( "Can not find $name." );
	}

	function check_writable( $path, $warn_only=false ) {
		global $root;
		if( is_writable( $root . $path ) )
			good( "$path is writable." );
		else if( $warn_only )
			warn( "$path is not writable." );
		else
			bad( "$path is not writable." );
	}

?>
<html>
	<head>
		<title>Storytlr Install</title>
		<style type="text/css">
			.good, .bad, .warn {
				width: 400px;
				padding: 5px;
				font-weight: bold;
				text-shadow: 1px 1px 2px #AAA;
				color: #FFF;
			}
			.good {
				background-color: #00BF00;
			}
			.bad {
				background-color: #BF2C00;
			}
			.warn {
				background-color: #BFB900;
			}
			label {
				display: inline-block;
				width: 150px;
				text-align: right;
			}
			legend {
				font-weight: bold;
			}
		</style>
	</head>
	<body>
		<h1>Storytlr Installation</h1>
		<p>
			<a href="http://storytlr.googlecode.com/">http://storytlr.googlecode.com/</a>
		</p>
		<h2>Check Requirements</h2>
<?php
	check_php( "5.0" );
	check_setting( "magic_quotes_gpc", false );

	if( check_include( 'Zend/Version.php', 'Zend Framework' ) )
		if( Zend_Version::compareVersion( '1.0.0' ) > 0 )
			warn( 'Zend Version 1.0.0 or newer is recommended' );

	check_function( 'mcrypt_module_open', 'mcrypt' );
	check_function( 'curl_init', 'cURL' );
	check_extension( 'PDO', 'PDO' );
	check_extension( 'tidy', 'Tidy');

	check_writable( 'protected/temp/' );
	check_writable( 'protected/upload/' );
	check_writable( 'protected/logs/' );
	check_writable( 'protected/config/config.ini', true );

	if( $all_clear ):
?>
	<h2>Configuration</h2>
	<form action="" method="POST">
		<fieldset>
			<legend>MySQL</legend>
			<label for="mysql_host">Host:</label> <input type="text" id="mysql_host" name="mysql_host" /><br/>
			<label for="mysql_database">Database Name:</label> <input type="text" id="mysql_database" name="mysql_database" /><br/>
			<label for="mysql_user">User:</label> <input type="text" id="mysql_user" name="mysql_user" /><br/>
			<label for="mysql_password">Password:</label> <input type="password" id="mysql_password" name="mysql_password" /><br/>
		</fieldset>
		<fieldset>
			<legend>User</legend>
			<label for="config_adminusername">Username:</label> <input type="text" id="config_adminusername" name="config_adminusername" /><br/>
			<label for="config_adminpassword">Password:</label> <input type="password" id="config_adminpassword" name="config_adminpassword" /><br/>
		</fieldset>
		<fieldset>
			<legend>API Keys</legend>
			<label for="google_maps">Google Maps API Key:</label> <input type="text" id="google_maps" name="google_maps" /><br/>
			<label for="flickr_maps">Flickr API Key:</label> <input type="text" id="flickr_maps" name="flickr_maps" /><br/>
		</fieldset>
		<input type="submit" value="Install" />
	</form>
<?
	else:
?>
	<p>Please fix the above issues before continuing.</p>
<?php endif; ?>
	</body>
</html>
