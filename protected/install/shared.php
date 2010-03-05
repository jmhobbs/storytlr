<?php

	class Check {

		protected static $errors = 0;
		protected static $include_found = true;

		public static function no_errors () {
			return ( 0 == self::$errors );
		}

		public static function error_count () {
			return self::$errors;
		}

		protected static function show_check_div ( $string, $class ) {
			echo '<div class="' . $class . '">' . $string . '</div>';
		}

		public static function good ( $string ) { self::show_check_div( $string, 'good' ); }
		public static function bad ( $string ) { self::$errors++; self::show_check_div( $string, 'bad' ); }
		public static function warn ( $string ) { self::show_check_div( $string, 'warn' ); }

		// Check the level of php available
		public static function PHP ( $required ) {
			if( 1 == version_compare( $required, phpversion() ) )
				Check::bad( "Your PHP version is too low, the minimum required is $required." );
			else
				Check::good( "PHP Version " . phpversion() . " meets requirement." );
		}

		public static function SettingValue ( $setting, $expected ) {
			if( $expected != ini_get( $setting ) )
				Check::bad( "PHP Setting '$setting' should be '". var_export( $expected, true ) . "'." );
			else
				Check::good( "PHP Setting '$setting' is '" . var_export( $expected, true ) ."'." );
		}

		// Check if a class exists
		public static function ClassExists ( $class, $name, $warn_only=false ) {
			if( class_exists( $class, false ) )
				Check::good( "Found $name." );
			else if( $warn_only )
				Check::warn( "Can not find $name." );
			else
				Check::bad( "Can not find $name." );
		}

		// Check if a function exists.
		public static function FunctionExists ( $function, $name, $warn_only=false ) {
			if( function_exists( $function ) )
				Check::good( "Found $name." );
			else if( $warn_only )
				Check::warn( "Can not find $name." );
			else
				Check::bad( "Can not find $name." );
		}

		// Check if a file can be included, is on the path.
		public static function CanInclude ( $include, $name, $warn_only=false ) {
			self::$include_found = true;
			set_error_handler( 'Check::include_error_handler', E_WARNING );
			include_once( $include );
			restore_error_handler();
			if( self::$include_found )
				Check::good( "Found $name." );
			else if( $warn_only )
				Check::warn( "Can not find $name." );
			else
				Check::bad( "Can not find $name." );
			return self::$include_found;
		}

		protected static function include_error_handler ( $errno, $errstr ) {
			self::$include_found = false;
		}

		// Checks an extension existence by phpversion. Doesn't work for all extensions.
		public static function ExtensionExists ( $extension, $name, $warn_only=false ) {
			if( false !== phpversion( $extension ) )
				Check::good( "Found $name." );
			else if( $warn_only )
				Check::warn( "Can not find $name." );
			else
				Check::bad( "Can not find $name." );
		}

		public static function PathWritable ( $path, $warn_only=false ) {
			$root = dirname( __FILE__ ) . '/../../';
			if( is_writable( $root . $path ) )
				Check::good( "$path is writable." );
			else if( $warn_only )
				Check::warn( "$path is not writable." );
			else
				Check::bad( "$path is not writable." );
		}
	} // Class Check