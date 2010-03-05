<?php
	require_once( 'shared.php' );

	if( file_exists( $root . 'protected/config/config.ini' ) ) {
		bad( 'Storytlr appears to already be installed, protected/config/config.ini exists.<br/>Please remove or rename protected/install/install.php' );
		return 'Installation';
	}

?>
		<h2>Requirements Check</h2>
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

	if( checks_ok() ):
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
	<form action=""><input type="submit" value="Check Again" onclick="this.disabled = true; this.value = 'Please Wait...';" /></form>
<?php
	endif;
	return 'Installation';
?>