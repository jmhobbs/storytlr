<?php
/*
 * Copyright 2008-2009 Laurent Eschenauer and Alard Weisscher
 * Copyright 2010 John Hobbs
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


              /**********************************************
               *  Inspiration from http://busterbenson.com/ *
               **********************************************/
              
class Pages_InfographicController extends Pages_BaseController {
    
	protected $_prefix = 'infographic';
	
	public function indexAction() {
		// To do before anything else
		$this->initPage();
		
		$interval = $this->getRequest()->getParam( "from" );

		switch( trim( strtolower( $interval ) ) ) {
			default:
			case 'twentyfour':
				$start = time() - 24 * 60 * 60;
				$this->view->interval = "24 Hours";
				break;
			case 'week':
				$start = time() - 7 * 24 * 60 * 60;
				$this->view->interval = "Week";
				break;
			case 'month':
				$start = time() - 30 * 24 * 60 * 60;
				$this->view->interval = "30 Days";
				break;
			case 'year':
				$start = time() - 365 * 24 * 60 * 60;
				$this->view->interval = "Year";
				break;
			case 'all':
				$this->view->interval = false;
				$start = 0;
				break;
		}

		$data = new Data();

		$this->view->items = $data->getItemsByDate( $start, time() );
		if( false !== $this->view->items ) {
		
			$this->view->total = count( $this->view->items );
			
			$this->view->latest_ago = $this->ago( $this->view->items[0]->getTimestamp() );
			$this->view->latest_source = SourceModel::getSourceModel( $this->view->items[0]->getSource() )->getTitle();
			$this->view->models = $this->getModels();
			
			$this->view->type_distribution = array();
			foreach( $this->view->items as $item ) {
				if( ! isset( $this->view->type_distribution[$item->getType()] ) )
					$this->view->type_distribution[$item->getType()] = 0;
				++$this->view->type_distribution[$item->getType()];
			}
			
			$this->view->source_distribution = array();
			foreach( $this->view->items as $item ) {
				$source = SourceModel::newInstance( $item->getPrefix(), $item->getSource() );
				if( ! isset( $this->view->source_distribution[$source->getTitle()] ) )
					$this->view->source_distribution[$source->getTitle()] = 0;
				++$this->view->source_distribution[$source->getTitle()];
			}

		}
		
		// Prepare the common elements
		$this->common();
	}

	protected function ago ( $timestamp ) {
		$diff = time() - $timestamp;
		if( $diff < 60 )
			$ago = "just a bit";
		else if ( $diff < 3600 )
			$ago = floor( $diff / 60 ) . " minutes";
		else if( $diff > 3600 and $diff < 7200  )
			$ago = "about an hour";
		else if( $diff > 7200 )
			$ago = floor( $diff / 3600 ) . " hours";
		return $ago;
	}

}