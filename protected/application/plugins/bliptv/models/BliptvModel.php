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
class BliptvModel extends SourceModel {

	protected $_name 	= 'bliptv_data';

	protected $_prefix = 'bliptv';

	protected $_search  = 'title';

	protected $_update_tweet = "%d videos added from blip.tv %s";

	public function getServiceName() {
		return "blip.tv";
	}

	public function getServiceURL() {
		if( $username = $this->getProperty( 'username' ) ) {
			return "http://$username.blip.tv/";
		}
		else {
			return "http://www.blip.tv/";;
		}
	}

	public function getServiceDescription() {
		return "blip.tv is a video sharing site.";
	}

	public function isStoryElement() {
		return true;
	}

	public function importData() {
		$videos = $this->updateData( true );
		$this->setImported( true );
		return $videos;
	}

	public function updateData() {
		$username = $this->getProperty( 'username' );
		$url = "http://$username.blip.tv/rss";

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_USERAGENT, 'Storytlr/1.0' );

		$response = curl_exec( $curl );
		$http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		curl_close( $curl );

		if( $http_code != 200 )
			throw new Stuffpress_Exception( "blip.tv returned http status $http_code for url: $url", $http_code );

		if( ! ( $items = simplexml_load_string( $response ) ) )
			throw new Stuffpress_Exception( "blip.tv did not return any result for url: $url", 0 );

		if ( count( $items->channel->item ) == 0 ) { return; }

		$items = $this->processItems( $items->channel->item );

		// Mark as updated (could have been with errors)
		$this->markUpdated();

		return $items;
	}
	
	private function processItems ( $items ) {
		$result = array();
		/*

  `title` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `show` varchar(255) NOT NULL,
  `embed_uri` varchar(255) NOT NULL,
  `length` int(6) unsigned NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `license` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `published` datetime NOT NULL,
		*/
		foreach( $items as $item ) {
		
			$blip = $item->children( "http://blip.tv/dtd/blip/1.0" );
			$media = $item->children( "http://search.yahoo.com/mrss/" );
		
			$data = array();
			$data['uri'] = $item->link;
			$data['title'] = $item->title;
			
			$data['show'] = $blip->show;
			$data['embed_uri'] = $blip->embedUrl;
			$data['length'] = $blip->runtime;
			$data['thumbnail'] = $blip->smallThumbnail;
			$data['license'] = $blip->license;
			$data['content'] = $blip->puredescription;
			
			$data['embed'] = $media->player;
			
			$data['published'] = strtotime( $item->pubDate );
			
			// Get all our possible tags
			$tags = array();
			$channels = explode( ',', $blip->adChannel );
			foreach( $channels as $keyword ) {
				$keyword = trim( $keyword );
				if( ! empty( $keyword) )
					$tags[] = $keyword;
			}
			$keywords = explode( ',', $media->keywords );
			foreach( $keywords as $keyword ) {
				$keyword = trim( $keyword );
				if( ! empty( $keyword) )
					$tags[] = $keyword;
			}
			$tags = array_unique( $tags );
			
			
			$id = $this->addItem( $data, $data['published'], SourceItem::VIDEO_TYPE, $tags, false, false, $data['title'] );
			if ($id) $result[] = $id;
		}
		return $result;
	}

	public function getConfigForm ( $populate=false ) {
		$form = new Stuffpress_Form();

		// Add the username element
		$element = $form->createElement( 'text', 'username', array( 'label' => 'Username', 'decorators' => $form->elementDecorators ) );
		$element->setRequired( true );
		$form->addElement( $element );

		// Populate
		if( $populate ) {
			$values = $this->getProperties();
			$form->populate( $values );
		}

		return $form;
	}

	public function processConfigForm( $form ) {
		$values = $form->getValues();
		$update = false;

		if( $values['username'] != $this->getProperty( 'username' ) ) {
			$this->_properties->setProperty( 'username',   $values['username'] );
			$update = true;
		}

		return $update;
	}

}
