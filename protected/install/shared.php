<?php
	//! \todo Namespace/class this stuff.

	$all_clear = true;
	$root = dirname( __FILE__ ) . '/../../';
	
	function checks_ok () {
		global $all_clear;
		return $all_clear;
	}
	
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