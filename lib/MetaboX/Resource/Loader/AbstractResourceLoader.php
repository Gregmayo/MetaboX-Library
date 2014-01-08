<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Resource\Loader;

abstract class AbstractResourceLoader{
	protected $_resourceId;
	protected $_resource;
	
	private $_remoteResourceProvider;
	private $_localResourceProvider;
	
	protected $_plain;
	
	public function __construct( $resource_id, $config ){
		$this->_resourceId = $resource_id;
		
		$this->_remoteResourceProvider = $config['remoteRP'];
		$this->_localResourceProvider  = $config['localRP'];
	}
	
	abstract public function load();
	
	public function getResourceId(){ return $this->_resourceId; }
	public function getResource(){ return $this->_resource; }
	
	protected function _getRemoteRP(){ return $this->_remoteResourceProvider; }
	protected function _getLocalRP(){ return $this->_localResourceProvider; }
	
	protected function _getResourceFullUrl(){ return $this->_getRemoteRP()->getUrlByResourceId($this->_resourceId); }
	protected function _getResourceFullPath(){ return $this->_getLocalRP()->getPathByResourceId($this->_resourceId); }
	
	/**
	 * It takes an input string that represents the attribute
	 * we want to extract from plain text file '$this->_plain'.
	 * A regular expression is used to extract every word match
	 * after '$label' in plain text file.
	 * 
	 * @param string
	 * 
	 * @return string
	 */
	protected function _extractAttributeByLabel($label){
		$pattern = '/' . $label . '\s*(.*)/';
		preg_match($pattern, $this->_plain, $matches);
		return isset($matches[1]) ? str_replace(';', '', $matches[1]) : '';
	}
}
