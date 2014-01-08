<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Resource\Loader;

class Pathway extends AbstractResourceLoader{
	
	/**
	 * FIXME: to be fixed because a generic pathway map has no: geneIdCollection, compoundIdCollection, etc... 
	 * 
	 * @return object
	 */
	public function load(){
		$resource = $this->_getLocalRP()->read( $this->_getResourceFullPath() );
		if( $resource ){ return $resource; }
		
		$this->_plain = $this->_getRemoteRP()->read( $this->_getResourceFullUrl() );
		
		$resource = (object) array(
			'ID' 		 		   => $this->getResourceId(),
			'name' 				   => $this->_extractAttributeByLabel('NAME'),
			'class' 			   => $this->_extractAttributeByLabel('CLASS'),
			'map' 				   => $this->_extractAttributeByLabel('PATHWAY_MAP'),
			'module' 			   => $this->_extractAttributeByLabel('MODULE'),
			'organism' 			   => $this->_extractAttributeByLabel('ORGANISM'),
			'geneIdCollection' 	   => '',
			'compoundIdCollection' => $this->_extractCompounds(),
			'koPathway' 		   => str_replace('map', 'ko', $this->getResourceId())
		);

		$this->_getLocalRP()->write($this->_getResourceFullPath(), $resource);
		$this->_resource = $resource;
		
		return $resource;
	}
	
	protected function _extractCompounds(){
		preg_match_all('/C[0-9]{5}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}
}
