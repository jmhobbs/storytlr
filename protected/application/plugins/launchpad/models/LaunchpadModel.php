<?php
/*
 *    Copyright 2008-2009 Laurent Eschenauer and Alard Weisscher
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
class LaunchpadModel extends SourceModel {

	protected $_name 	= 'launchpad_data';

	protected $_prefix = 'launchpad';
	
	protected $_search  = 'title';
	
	protected $_update_tweet = "Syndicated %d blog entries on my Lifestream %s";

	public function getServiceName() {
		return "Launchpad";
	}
	
	public function isStoryElement() {
		return true;
	}

	public function getServiceURL() {
		if ($username = $this->getProperty('username')) {
			return "http://feeds.launchpad.net/~$username/revisions.atom";
		}
		else {
			return "";
		}
	}

	public function getServiceDescription() {
		return "Launchpad is an open source project management platform.";
	}

	public function importData() {
		$username = $this->getProperty('username');
		$url = "http://feeds.launchpad.net/~$username/revisions.atom";

		try {
			$feeds = Zend_Feed::findFeeds($url);
		}
		catch (Zend_Feed_Exception $e) {
			return 0;
		}
		
		if (!$feeds) {
			try {
				$items = Zend_Feed::import($url);
			}
			catch (Zend_Feed_Exception $e) {
				return 0;
			}
			$feed_url = $url;
		} else {
			$items = $feeds[0];
			$feed_url = Zend_Feed::getHttpClient()->getUri(true);
		}
		
		$title = $items->title();
		$this->setProperty('feed_title', $title);
		$this->setProperty('feed_url', $feed_url);
		$items = $this->updateData();
		$this->setImported(true);
		return $items;

	}

	public function updateData() {
		$username = $this->getProperty('username');
		$feed_url = "http://feeds.launchpad.net/~$username/revisions.atom";

		// Fetch the latest headlines from the feed
		try {
			$items = $feed = Zend_Feed::import($feed_url);
			return $this->processItems($items);
		} catch (Zend_Feed_Exception $e) {
			return;
		}
	
		// Mark as updated (could have been with errors)
		$this->markUpdated();
	}

	private function processItems($items) {
		$result = array();	
		$config = array(
           'indent'         => true,
           'output-xhtml'   => true,
           'wrap'           => 200);

           // Abandon all hope ye who edit here
	   $exp = '%.*"(https://code.launchpad.net/[^"]+)".*(lp:~?[^ ]+)\n.*<th>Revno:</th>\n<td>([0-9]+)</td>.*<th>Project:</th>\n<td>\n<a href=".*">\n\s+(.*)\n\s+ </a>.*<th>Log message:</th>\n<td>\n<p>(.+)</p>\n</td>.*%sm';

        foreach ($items as $item) {
			$data = array();
			$published			= strtotime((string) $item->published);
			$updated			= strtotime((string) $item->updated);
			$content		 	= (string) $item->content;

			if (preg_match($exp, $content, $matches)) {
				$data['link'] = "http://bazaar.launchpad.net/" . preg_replace("/lp:/", "", $matches[2]) . "/revision/" . $matches[3];
				$data['title'] = (string) $item->title();
				$data['published'] = max($published, $updated);
				$data['content'] = $content;
				$data['branch_url'] = $matches[1];
				$data['branch'] = $matches[2];
				$data['revision'] = $matches[3];
				$data['project'] = $matches[4];
				$data['message'] = $matches[5];

				// Save the item in the database
				$id = $this->addItem($data, $data['published'], SourceItem::LINK_TYPE, false, false, false, $data['title']);
				if ($id) $result[] = $id;
				if (count($result)> 100) break;
			} else {
				echo('Failed to read entry.\n');
			}

		}
		return $result;
	}
}
