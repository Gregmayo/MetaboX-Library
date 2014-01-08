<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Graph\Writer;

abstract class AbstractGraphWriter{
	protected $_nodeCollection = array();
	protected $_edgelist       = array();
	
	public function __construct( $graph = null ){
		if( !is_null($graph) ){
			$this->_nodeCollection = $graph['node_collection'];
			$this->_edgelist       = $graph['weighted_edgelist'];	
		}
	}
	
	abstract public function write($filename, $data);
}
