<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Resource\Loader;

class Enzyme extends AbstractResourceLoader{
	
	/**
	 * 
	 * @return object
	 */
	public function load(){
		$resource = $this->_getLocalRP()->read( $this->_getResourceFullPath() );
		if( $resource ){ return $resource; }
		
		$this->_plain = $this->_getRemoteRP()->read( $this->_getResourceFullUrl() );
		
		$resource = (object) array(
			'ID' 		=> $this->getResourceId(),
			'name'      => $this->_extractAttributeByLabel('NAME'),
			'class'     => $this->_extractAttributeByLabel('CLASS'),
			'sysname'   => $this->_extractAttributeByLabel('SYSNAME'),
			'reference' => $this->_extractAttributeByLabel('REFERENCE'),
			'reaction'  => $this->_extractReaction()
		);

		$this->_getLocalRP()->write($this->_getResourceFullPath(), $resource);
		$this->_resource = $resource;
		
		return $resource;
	}
	
	protected function _extractReaction(){
		//preg_match('/RN:R[0-9]{5}/', $this->_plain, $matches);
		//return str_replace('RN:', '', $matches[0]);
		
		preg_match_all('/R[0-9]{5}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}
}
