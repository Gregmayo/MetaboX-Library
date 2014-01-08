<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Resource\Provider\Remote;

class KEGG{
	private $_resource_url;
	private $_resource_type;
	private $_resource_ids;
	
	public function __construct( $url, $type, $resourceIds = false ){
		$this->_resource_url  = $url;
		$this->_resource_type = $type;
		$this->_resource_ids  = $resourceIds;
	}
	
	public function getUrl(){ return $this->_resource_url; }
	public function getUrlByResourceId( $id ){ return $this->getUrl() . $this->_resource_type . $id; }
	
	public function getUrlByResourceIds( $ids = false ){
		$list = $ids ? $ids : $this->_resource_ids;
		if( !$list ){ return ''; }
		
		return $this->getUrl() . implode('+' . $this->_resource_type . ':', $list);
	}
	
	public function read( $url ){
		$c = curl_init();
	    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($c, CURLOPT_URL, $url);
	    $contents = curl_exec($c);
	    curl_close($c);
	
	    if( $contents ){ return $contents; }
	    else{ return FALSE; }
	}
	
}
