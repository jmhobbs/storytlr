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

class IconSets {

	public static function getAvailableIconSets() {

		$cache_key = 'IconSets_getAvailableIconSets';

		if( Zend_Registry::isRegistered( "cache" ) )
			$cache = Zend_Registry::get( "cache" );
		else
			$cache = false;
		
		if( $cache && ( $result = $cache->load( $cache_key ) ) )
			return $result;
		
		$root = Zend_Registry::get( "root" );
		$dir = dirname( $root ) . "/images/icons/";
		$icon_sets = array();
		$files  = array();
		if( is_dir( $dir ) ) {
			$dirents = scandir( $dir );
			foreach( $dirents as $file ) {
				if( $file != "." && $file != ".." && $file != "CVS" && $file != "SVN") {
					$name = basename( $file );
					$files[]= $name;
				}
			}
		}
		
		sort( $files );
		foreach( $files as $file ) {
			$config = "$dir/$file/config.ini";

			if( ! file_exists( $config ) )
				continue; 

			$icon_set         = parse_ini_file( $config );
			$icon_set['name'] = $file;
			$icon_sets[$file] = $icon_set; 
		}
		
		if( $cache )
			$cache->save( $icon_sets, $cache_key );
		
		return $icon_sets;
	}
}