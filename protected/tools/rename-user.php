#!/usr/bin/php
<?php
	if ( $argc != 3 ) {
		die( "Usage: {$argv[0]} old-user-name new-user-name\r\n" );
	}
	else {
		$old_user = $argv[1];
		$new_user = $argv[2];
	}

	// Update after deployment for location of non-public files
	$root = dirname(dirname(__FILE__));

	// TODO this should be moved to the boostrap file
	set_include_path(
			$root . '/library/Zend' . PATH_SEPARATOR
		. $root . '/application' . PATH_SEPARATOR
		. $root . '/application/admin/models' . PATH_SEPARATOR
		. $root . '/application/public/models' . PATH_SEPARATOR
		. $root . '/library' . PATH_SEPARATOR
		. $root . '/library/Feedcreator' . PATH_SEPARATOR
		. $root . '/library/htmLawed' . PATH_SEPARATOR
		. get_include_path()
	);

	require_once 'Bootstrap.php';
	Bootstrap::prepare();

	$usersTable = new Users();
	$user = $usersTable->getUserFromUsername( $old_user );
	if( ! $user ) {
		echo "User $old_user does not exist.\r\n";
		die();
	}

	$old_user = $user->username;
	$user = $usersTable->setUsername( $user->id, $new_user );
	if( false === $user )
		echo "Could not change username for {$old_user} to {$new_user}. Conflict perhaps?\r\n";
	else
		echo "Changed username for {$old_user} to {$new_user}.\r\n";