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
	
	// Supplied to Google charts as the basis for our chart colors
	//! \todo Can we pull this from the theme somehow?
	protected $colors = array(
		"3366FF",
		"6633FF",
		"CC33FF",
		"FF33CC",
		"33CCFF",
		"003DF5",
		"002EB8",
		"FF3366",
		"33FFCC",
		"B88A00",
		"F5B800",
		"FF6633",
		"33FF66",
		"66FF33",
		"CCFF33",
		"FFCC33"
	);
	
	public function indexAction() {
		// To do before anything else
		$this->initPage();
		
		$interval = $this->getRequest()->getParam( "from" );

		switch( trim( strtolower( $interval ) ) ) {
			default:
			case 'twentyfour':
				$start = time() - 24 * 60 * 60;
				$this->view->interval_name = "24 Hours";
				break;
			case 'week':
				// Ugh...
				$start = strtotime( date( "Y-m-d 00:00:00", time() - 7 * 24 * 60 * 60 ) );
				$this->view->interval_name = "Week";
				break;
			case 'thirty':
				$start = time() - 30 * 24 * 60 * 60;
				$this->view->interval_name = "30 Days";
				break;
			case 'year':
				$start = time() - 365 * 24 * 60 * 60;
				$this->view->interval_name = "Year";
				break;
			case 'all':
				$this->view->interval_name = false;
				$start = 0;
				break;
		}

		$data = new Data();

		$this->view->items = $data->getItemsByDate( $start, time() );
		
		if( false !== $this->view->items ) {
		
			$this->view->total = count( $this->view->items );
			
			$this->view->latest = $this->view->items[0];
			$this->view->latest_source = SourceModel::getSourceModel( $this->view->latest->getSource() )->getTitle();
			$this->view->latest_ago = $this->ago( $this->view->latest->getTimestamp() );
			
			$this->view->models = $this->getModels();
			
			///// Type Distribution /////
			$type_distribution = array();
			foreach( $this->view->items as $item ) {
				if( ! isset( $type_distribution[$item->getType()] ) )
					$type_distribution[$item->getType()] = 0;
				++$type_distribution[$item->getType()];
			}
			
			$data = array();
			$labels = array();
			foreach( $type_distribution as $type => $count ) {
				$data[] = round( $count / $this->view->total * 100 );
				$labels[] = ucwords( $type );
			}
			$this->view->type_distribution_url = "http://chart.apis.google.com/chart?cht=p3&chd=t:" . implode( ',', $data ) . "&chs=500x200&chl=" . implode( '|', $labels ) . '&chco=' . implode( '|', $this->colors );
			
			///// Source Distribution /////
			$source_distribution = array();
			foreach( $this->view->items as $item ) {
				$source = SourceModel::getSourceModel( $item->getSource() )->getTitle();
				if( ! isset( $source_distribution[$source] ) )
					$source_distribution[$source] = 0;
				++$source_distribution[$source];
			}
			
			$data = array();
			$labels = array();
			foreach( $source_distribution as $type => $count ) {
				$data[] = round( $count / $this->view->total * 100 );
				$labels[] = ucwords( $type );
			}
			$this->view->source_distribution_url = "http://chart.apis.google.com/chart?cht=p3&chd=t:" . implode( ',', $data ) . "&chs=500x200&chl=" . implode( '|', $labels ) . '&chco=' . implode( '|', $this->colors );

			///// Source Over Interval /////
			$intervals = array();
			switch( trim( strtolower( $interval ) ) ) {
				default:
				case 'twentyfour':
					for( $i = 24; $i > 0; --$i ) {
						$s = $start + ( ( 23 - $i ) * 3600 );
						$e = $s + 3599;
						$intervals[] = array( strval( $i ), $s, $e );
					}
					$this->view->total_by_interval = "Total Posts Per Previous Hour";
					break;
			}
			
			$data = array();
			$labels = array();
			foreach( $intervals as $interval ) {
				$data[] = 0;
				$labels[] = $interval[0];
			}
			foreach( $this->view->items as $item ) {
				foreach( $intervals as $key => $interval ) {
					if( $item->getTimestamp() >= $interval[1] && $item->getTimestamp() < $interval[2] ) {
						++$data[$key]; 
					}
				}
			}
			$max = 5;
			foreach( $data as $datum )
				if( $datum > $max )
					$max = $datum;
			$this->view->total_by_interval_url = "http://chart.apis.google.com/chart?cht=lc&chd=t:" . implode( ',', $data ) . "&chs=500x200&chl=" . implode( '|', $labels ) . "&chds=0," . ( $max + floor( $max / 5 ) ) . "&chxt=y&chxr=0,0," . ( $max + floor( $max / 5 ) ) . "," . floor( $max / 5 ) . "&chco=" . $this->colors[0];

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