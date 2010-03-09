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

class Properties extends Stuffpress_Db_Properties
{
	private static $_property_cache = array();

	protected $_name = 'properties';

	protected $_primary = 'user_id';
	
	public function getDefault($key) {
		$config = Zend_Registry::get("configuration");
		if (isset($config->default->$key)) {
			return 	$config->default->$key;
		}
	}
	
	public static function getPropertyWithCache ( $property, $default = false ) {
		if( ! isset( Properties::$_property_cache[$property] ) ) {
			$properties = new Properties();
			Properties::$_property_cache[$property] = $properties->getProperty( $property, $default );
		}
		return Properties::$_property_cache[$property];
	}
	
}