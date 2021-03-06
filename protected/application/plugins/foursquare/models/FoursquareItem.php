<?php
/*
 *    Copyright 2008-2009 Laurent Eschenauer and Alard Weisscher
 *    Copyright 2010 John Hobbs
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
class FoursquareItem extends SourceItem {

	protected $_prefix 	= 'foursquare';

	protected $_preamble = 'foursquare activity: ';

	public function getContent() { return $this->_data['content']; }

	public function getTitle() { return $this->_data['title']; }

	public function getLink() { return $this->_data['link']; }

	public function getType() { return SourceItem::STATUS_TYPE; }
	
	public function getStatus() { 
		return "Checked-in at " . htmlspecialchars(strip_tags($this->_data['title'])); 
	}
	
	public function setStatus($status) {
		$db = Zend_Registry::get('database');
		
		$sql = "UPDATE `foursquare_data` SET `title`=:status "
			 . "WHERE source_id = :source_id AND id = :item_id ";
		
		$data 		= array("source_id" 	=> $this->getSource(),
							"item_id"		=> $this->getID(),
							"status"		=> $status);
							
 		$stmt 	= $db->query($sql, $data);

 		return;
	}

	public function getBackup() {
		$item = array();
		$item['SourceID'] = $this->_data['source_id'];
		$item['Title'] = $this->_data['title'];
		$item['Content'] = $this->_data['content'];
		$item['GUID'] = $this->_data['guid'];
		$item['Link'] = $this->_data['link'];
		$item['Published'] = $this->_data['published'];
		return $item;
	}

}
