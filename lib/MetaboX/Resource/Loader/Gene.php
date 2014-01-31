<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Resource\Loader;

class Gene extends AbstractResourceLoader{
	
	public function load(){
		$resourceFullPath = str_replace(':', '_', $this->_getResourceFullPath());
		$resource = $this->_getLocalRP()->read( $resourceFullPath );
		if( $resource ){ echo $resource->ID . "\n"; return $resource; }
		/*
		$this->_plain = $this->_getRemoteRP()->read( $this->_getResourceFullUrl() );
		
		$pathwayList = array_unique($this->_extractPathways());
		
		$resource = (object) array(
			'ID' 	               => $this->getResourceId(),
			'name'                 => $this->_extractAttributeByLabel('NAME'),
			'definition'           => $this->_extractAttributeByLabel('DEFINITION'),
			'orthology'            => $this->_extractAttributeByLabel('ORTHOLOGY'),
			'organism'             => $this->_extractAttributeByLabel('ORGANISM'),
			'class'                => $this->_extractAttributeByLabel('CLASS'),
			'position'             => $this->_extractAttributeByLabel('POSITION'),
			'motif'                => $this->_extractAttributeByLabel('MOTIF'),
			// 'dblinks'              => $this->_extractDBLinks(),
			'aaseq'                => $this->_extractAttributeByLabel('AASEQ'),
			'ntseq'                => $this->_extractAttributeByLabel('NTSEQ'),
			'pathwayIdCollection'  => $pathwayList
		);

		if( $pathwayList ){
			echo "Gene " . $this->getResourceId() . " has " . count($pathwayList) . " pathways.\n";
			$this->_getLocalRP()->write($resourceFullPath, $resource);
			$this->_resource = $resource;
			
			return $resource;
		}*/
	}
	
	/**
	 * Matches all pathway IDs from plain text file.
	 * A regular expression is used to extract all
	 * patterns that match a KEGG pathway ID.
	 * 
	 * @return array
	 */
	protected function _extractPathways(){
		$label = explode(':', $this->getResourceId());
		$pattern = '/' . $label[0] . '[0-9]{5}/';
		
		preg_match_all($pattern, $this->_plain, $matches);
		return array_unique($matches[0]);
	}

}