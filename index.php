<?php
define("STORYTLR_VERSION","0.9.3.jmhobbs.master");
define("STORYTLR_VERSION_NUMBER",0.93);

// Update after deployment for location of non-public files
$root = dirname(__FILE__);

// Run the install stuff if it is there.
if( file_exists( $root . '/protected/install/install.php' ) ) {
	$template = array();
	ob_start();
	$template['title'] = require_once( $root . '/protected/install/install.php' );
	$template['content'] = ob_get_contents();
	ob_end_clean();
	require_once( $root . '/protected/install/template.php' );
	exit();
}

// Run the upgrade stuff, if it is there
/*if( ! file_exists( $root . '/protected/install/version/' . STORYTLR_VERSION_NUMBER ) ) {
	ob_start();
	$template['title'] = require_once( $root . '/protected/install/upgrade.php' );
	$template['content'] = ob_get_contents();
	ob_end_clean();
	require_once( $root . '/protected/install/template.php' );
	exit();
}*/

// We're assuming the Zend Framework is already on the include_path
// TODO this should be moved to the boostrap file
set_include_path(
		  $root . '/protected/application' . PATH_SEPARATOR
		. $root . '/protected/application/admin/models' . PATH_SEPARATOR
		. $root . '/protected/application/public/models' . PATH_SEPARATOR
		. $root . '/protected/application/pages/models' . PATH_SEPARATOR
		. $root . '/protected/application/widgets/models' . PATH_SEPARATOR
		. $root . '/protected/library' . PATH_SEPARATOR
		. $root . '/protected/library/Feedcreator' . PATH_SEPARATOR
		. $root . '/protected/library/htmLawed' . PATH_SEPARATOR
		. get_include_path()
);

require_once 'Bootstrap.php';

Bootstrap::run();
