<?php

/*!
	This is a very incomplete implementation of a web request object.  The idea is
	to unify all of the plugins on this one item, and then use etags and modified
	headers to prevent repetitive traffic.

	Lot's of stuff needs to be added. Including all the other HTTP VERBS, and it might
	be better just to subclass Zend_Http_Client.
*/
class Stuffpress_WebRequest {

	private $_url;

	private $_headers = array();

	private $_response;

	public function __construct( $url ) {
		if( ! Stuffpress_URL::validate( $url ) ) {
			throw new Stuffpress_Exception( "Invalid URL to process: $url" );
		}

		$this->_url = $url;
	}

	public function set_etag ( $etag ) {
		$etag = str_replace( '"', '', $etag );
		if( ! empty( $etag ) )
			$this->_headers[] = "If-None-Match: \"$etag\"";
	}

	public function set_last_modified ( $last_modified ) {
		if( ! empty( $last_modified ) )
			$this->_headers[] = "If-Modified-Since: $last_modified";
	}

	public function get () {
		try {
			$http = new Zend_Http_Client( $this->_url );
			$http->setHeaders( $this->_headers );
			$this->_response = $http->request( "GET" );
			if( $this->_response->isSuccessful() ) {
				return true;
			}
			else if ( 304 == $response->getStatus() ) {
				return false;
			}
			else {
				throw new Stuffpress_Exception( "An error occurred while fetching your url, $url: " . $this->_response->getMessage() );
			}
		}
		catch ( Zend_Http_Client_Exception $e ) {
			throw new Stuffpress_Exception( "An error occurred while fetching your url, $url: " . $e->getMessage() );
		}
	}

	public function get_response_body () { return $this->_response->getBody(); }
	public function get_response_headers () { return $this->_response->getHeaders(); }
	public function get_response_last_modified () { return $this->_response->getHeader( 'Last-Modified' ); }
	public function get_response_etag () { return $this->_response->getHeader( 'ETag' ); }

}