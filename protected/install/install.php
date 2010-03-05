<?php
	require_once( 'shared.php' );

	if( file_exists( $root . 'protected/config/config.ini' ) ) {
		bad( 'Storytlr appears to already be installed, protected/config/config.ini exists.<br/>Please remove or rename protected/install/install.php' );
		return 'Installation';
	}

?>
		<h2>Requirements Check</h2>
<?php
	Check::PHP( "5.0" );
	Check::SettingValue( "magic_quotes_gpc", false );

	if( Check::CanInclude( 'Zend/Version.php', 'Zend Framework' ) )
		if( Zend_Version::compareVersion( '1.0.0' ) > 0 )
			warn( 'Zend Version 1.0.0 or newer is recommended' );

	Check::FunctionExists( 'mcrypt_module_open', 'mcrypt' );
	Check::FunctionExists( 'curl_init', 'cURL' );
	Check::ExtensionExists( 'PDO', 'PDO' );
	Check::ExtensionExists( 'tidy', 'Tidy');

	Check::PathWritable( 'protected/temp/' );
	Check::PathWritable( 'protected/upload/' );
	Check::PathWritable( 'protected/logs/' );
	Check::PathWritable( 'protected/config/config.ini', true );

	if( Check::no_errors() ):
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