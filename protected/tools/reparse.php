#!/usr/bin/php
<?php

	/*
	 *  Copyright 2008-2009 Laurent Eschenauer and Alard Weisscher
	 *  Copyright 2010 John Hobbs
	 *
	 *  Licensed under the Apache License, Version 2.0 (the "License");
	 *  you may not use this file except in compliance with the License.
	 *  You may obtain a copy of the License at
	 *
	 *      http://www.apache.org/licenses/LICENSE-2.0
	 *
	 *  Unless required by applicable law or agreed to in writing, software
	 *  distributed under the License is distributed on an "AS IS" BASIS,
	 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 *  See the License for the specific language governing permissions and
	 *  limitations under the License.
	 *
	 */

	if ( $argc != 2 || $argv[1] == '--help' )
		die( "Usage: {$argv[0]} [user]\r\n" );
	else
		$user_name = $argv[1];

	// Update after deployment for location of non-public files
	$root = dirname( dirname( __FILE__ ) );

	// We're assuming the Zend Framework is already on the include_path
	// TODO this should be moved to the boostrap file
	set_include_path(
		  $root . '/application' . PATH_SEPARATOR
		. $root . '/application/admin/models' . PATH_SEPARATOR
		. $root . '/application/public/models' . PATH_SEPARATOR
		. $root . '/library' . PATH_SEPARATOR
		. $root . '/library/Feedcreator' . PATH_SEPARATOR
		. get_include_path()
	);

	// We want to track how long the update takes
	$start_time = time();

	require_once 'Bootstrap.php';
	Bootstrap::prepare();

	// We don't want to limit this script in time
	ini_set('max_execution_time', 0);

	// Setup a logger
	$logger = new Zend_Log();
	$logger->addWriter( new Zend_Log_Writer_Stream( $root . '/logs/reparse.log' ) );
	Zend_Registry::set( 'logger', $logger );

	// Prepare models we need to access
	echo "Memory usage on startup: " . memory_get_usage() . "\r\n";

	$usersTable = new Users();
	$user = $usersTable->getUserFromUsername( $user_name );
	if ( ! $user || $user->is_suspended ) {
		echo "User {$user->username} is suspended.\r\n";
		$logger->log("User {$user->username} is suspended.\r\n", Zend_Log::INFO);
		die();
	}

	Zend_Registry::set( "shard", $user->id );

	// Get the user sources
	$sourcesTable = new Sources();
	$sources = $sourcesTable->getSources();
	if ( ! $sources ) {
		echo "No sources found to update.\r\n";
		$logger->log( "No sources found to update.", Zend_Log::INFO );
		die();
	}

	// Log an entry
	$logger->log( "Reparsing {$user->username}", Zend_Log::INFO );

	shuffle( $sources );

	$success = 0;
	$failure = 0;
	$total = count( $sources );

	foreach( $sources as $source ) {
		echo "Memory: " . memory_get_usage() . "\r\n";

		if ( $source['service'] == 'stuffpress' ) { continue; }
		if ( ! $source['enabled'] ) { continue; }

		$model = SourceModel::newInstance( $source['service'], $source );

		try {
			if ( $source['imported'] ) {
				echo "Reparsing source {$source['service']} for user {$user->username} [" . ($success + $failure) . "/$total] ({$source['id']})....";
				$count = $model->reparse();
				echo " reparsed " . $count . " items\r\n";
			}
			else {
				echo "Source not imported {$source['service']} ({$source['id']}). Skipping.\r\n";
			}
			$success++;
		}
		catch (Exception $e) {
			echo "Could not reparse source {$source['id']}: " . $e->getMessage();
			$logger->log( "Could not reparse source {$source['id']}: " . $e->getMessage(), Zend_Log::ERR );
			echo $e->getTraceAsString();
			$failure++;
		}
	}

	// Wrap up
	$end_time = time();

	$total_time = $end_time - $start_time;
	echo "Reparsed $success out of $total sources in $total_time seconds.\r\n";
