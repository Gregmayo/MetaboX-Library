<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Resource\Provider\Local;

class JSON{
	private $_resource_dir;
	private $_resource_type;
	private $_ext = '.json';
	
	public function __construct( $dir, $type ){
		$this->_resource_dir  = $dir;
		$this->_resource_type = $type;
	}
	
	public function getPath(){ return getcwd() . '/' . $this->_resource_dir; }
	public function getPathByResourceId( $id ){ return $this->getPath() . $this->_resource_type . $id . $this->_ext; }
	
	public function write($file, $data){
		$fh = fopen($file, 'w') or die("[JSONResource] can't write to file " . $file);
		fwrite($fh, json_encode($data));
		fclose($fh);
	}
	
	public function read($file){
		if( file_exists($file) ){
			return json_decode(file_get_contents($file));
		}
		
		return '';
	}
}
