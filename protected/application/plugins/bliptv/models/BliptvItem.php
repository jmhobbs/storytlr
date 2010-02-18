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
class BliptvItem extends SourceItem {

	protected $_prefix = 'bliptv';

	public function toArray () {
		return $this->_data;
	}

	public function getTitle () {
		$duration = intval( $this->_data['length'] );

		$composite = "";

		$hours = floor( $duration / 3600 );
		$duration = $duration - ( $hours * 3600 );

		if( $hours == 1 )
			$composite .= "1 Hour ";
		else if (  $hours > 1 )
			$composite .= "$hours Hours ";

		$minutes = floor( $duration / 60 );
		$seconds = $duration - ( $minutes * 60 );

		$composite .= sprintf( "%02d:%02d", $minutes, $seconds );

		return $this->_data['title'] . " ($composite)";
	}

	public function getEmbed () {
		return $this->_data['embed'];
	}

	public function getLink () {
		return $this->_data['link'];
	}

	public function getDescription () {
		return $this->_data['content'];
	}

	public function getType () {
		return SourceItem::VIDEO_TYPE;
	}

	public function getImageUrl ( $size = ImageItem::SIZE_THUMBNAIL ) {
		return $this->_data['thumbnail'];
	}

	public function getBackup() {
		$item = array();
		$item['Link']				= $this->getLink();
		$item['Title']				= $this->_data['title'];
		$item['Date']				= $this->_data['published'];
		return $item;
	}
}