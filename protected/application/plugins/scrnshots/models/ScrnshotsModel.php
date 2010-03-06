<?php
/*
 *    Copyright 2008-2009 Laurent Eschenauer, Alard Weisscher, Ryan Paul
 *    and Ryan Paul
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
class ScrnShotsModel extends SourceModel {

	protected $_name 	= 'scrnshots_data';

	protected $_prefix = 'scrnshots';

	protected $_search  = 'description';
	
	protected $_update_tweet = "Uploaded %d images to ScrnShots %s"; 

	public function getServiceName() {
		return "ScrnShots";
	}

	public function isStoryElement() {
		return true;
	}

	public function getServiceURL() {
		if ($username = $this->getProperty('username')) {
			return "http://www.scrnshots.com/users/$username";
		}
		else {
			return "http://www.scrnshots.com/";;
		}
	}

	public function getServiceDescription() {
		return "ScrnShots is a screenshot sharing service.";
	}

  public function importData() {
    $items = $this->updateData(true);
    $this->setImported(true);
    return $items;
  }

  public function updateData($import=false) {

		// Verify that we have the required settings
		if (!($username = ($this->getProperty('username')))) {
			throw new Stuffpress_Exception("Update failed, connector not properly configured");
    }

    $url = "http://www.scrnshots.com/users/$username/screenshots.json";

    $curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT,'Storytlr/1.0');
		
		$response = curl_exec($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close ($curl);

    if (!($data = json_decode($response))) {
			throw new Stuffpress_Exception("ScrnShots did not return any result for url: $url", 0);
		}
			
		// Mark as updated (could have been with errors)
		$this->markUpdated();
		
		return $this->processItems($data, $import);
  }

  private function processItems($stories,$import=false) {
		$result = array();
				
		foreach ($stories as $story) {
      $data = array();
      $data['created_at'] = $story->created_at;
      $data['description'] = $story->description;
      $data['href'] = $story->images->fullsize;
      $data['link'] = $story->url;
      $data['thumbnail'] = $story->images->medium;
      $data['image'] = $story->images->large;
      $data['title'] = "Screenshot";

			$id = $this->addItem($data, strtotime($data['created_at']), SourceItem::IMAGE_TYPE, false, false, false, $data['title']);
			if ($id) $result[] = $id;
		}
		
		return $result;
	}
	
}
