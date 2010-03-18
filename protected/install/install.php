<?php
	require_once( 'shared.php' );

	if( file_exists( $root . '/protected/config/config.ini' ) ) {
		Check::bad( 'Storytlr appears to already be installed, protected/config/config.ini exists.<br/>Please remove or rename protected/install/install.php' );
		return 'Installation';
	}

	// Preset some variables...
	$form_errors = array();
	$form_values = array(
		'mysql_host' => 'localhost',
		'mysql_database' => 'storytlr',
		'config_username' => 'admin'
	);
	
	if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		$form_values = $_POST;
		
		// Validation
		$required = array(
			'mysql_host',
			'mysql_database',
			'mysql_user',
			'mysql_password'
		);
		foreach( $required as $field ) {
			if( empty( $_POST[$field] ) )
				$form_errors[$field] = 'Field is required.';
		}
		
		// Installation
		try {
			if( 0 == count( $form_errors ) ) {
				$res = Database::Connect( $_POST['mysql_host'], $_POST['mysql_database'], $_POST['mysql_user'], $_POST['mysql_password'] );
				if( true !== $res ) {
					$form_errors['mysql_host'] = 'Please check this field.';
					$form_errors['mysql_database'] = 'Please check this field.';
					$form_errors['mysql_user'] = 'Please check this field.';
					$form_errors['mysql_password'] = 'Please check this field.';
					throw new Exception( $res );
				}
				Check::good( '[' . date( 'H:i:s' ) .'] Connected to database.' );
				
				$res = Database::RunFile( $root . '/protected/install/schema.sql' );
				
				if( true !== $res )
					throw new Exception( 'Error loading database schema:<br/><div class="nested-error">' . $res . '</div>' );

				Check::good( '[' . date( 'H:i:s' ) .'] Loaded database schema.' );
				
				$subs = array( 'username' => $_POST['config_username'], 'userpass' => md5( $_POST['config_password'] ) );
				$res = Database::RunFile( $root . '/protected/install/data.sql', $subs );
				
				if( true !== $res )
					throw new Exception( 'Error loading database data:<br/><div class="nested-error">' . $res . '</div>' );
					
				Check::good( '[' . date( 'H:i:s' ) .'] Loaded database data.' );
				
				return 'Installation';
			}
			else {
				throw new Exception( 'Your configuration has errors, please see below.' );
			}
		}
		catch ( Exception $e ) {
			Check::bad( $e->getMessage() );
			Check::restart();
		}
		
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

	Check::PathWritable( 'protected/temp/' );
	Check::PathWritable( 'protected/upload/' );
	Check::PathWritable( 'protected/logs/' );
	Check::PathWritable( 'protected/install/version/' );
	Check::PathWritable( 'protected/config/', true );

	if( Check::no_errors() ):
		$form = new Form( $form_errors, $form_values );
?>
	<h2>Configuration</h2>
	<form action="" method="POST">
		<fieldset>
			<legend>MySQL</legend>
			<?php
				$form->text( 'mysql', 'host' );
				$form->text( 'mysql', 'database', 'Database Name' );
				$form->text( 'mysql', 'user' );
				$form->password( 'mysql', 'password' );
			?>
		</fieldset>
		<fieldset>
			<legend>User</legend>
			<?php
				$form->text( 'config', 'username', 'Username' );
				$form->password( 'config', 'password', 'Password' );
			?>
		</fieldset>
		<fieldset>
			<legend>API Keys (optional)</legend>
			<?php
				$form->text( 'config', 'google_maps_api_key' );
				$form->text( 'config', 'flickr_api_key' );
			?>
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