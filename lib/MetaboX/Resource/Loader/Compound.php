<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Resource\Loader;

class Compound extends AbstractResourceLoader{
	
	/**
	 * This method tries to retrieve compound '$cpd' information
	 * from cached json file '_RESOURCE_DIR_ . $cpd'.
	 * If chached file is not available, we didn't process this
	 * compound before. We then download compound information
	 * from external resource '_RESOURCE_URL_ . $cpd'.
	 * We use KEGG database to retrieve information about input '$cpd'
	 * and other helper methods are used to extract these information
	 * from a plain text file.
	 * 
	 * @return object
	 */
	public function load(){
		$resource = $this->_getLocalRP()->read( $this->_getResourceFullPath() );
		if( $resource ){ return $resource; }
		
		$this->_plain = $this->_getRemoteRP()->read( $this->_getResourceFullUrl() );
		
		$resource = (object) array(
			'ID' 	       => $this->getResourceId(),
			'name'         => $this->_extractAttributeByLabel('NAME'),
			'formula'      => $this->_extractAttributeByLabel('FORMULA'),
			'exact_mass'   => $this->_extractAttributeByLabel('EXACT_MASS'),
			'mol_weight'   => $this->_extractAttributeByLabel('MOL_WEIGHT'),
			'dblinks'      => $this->_extractDBLinks(),
			'reactionIdCollection' => $this->_extractReactions(),
			'pathwayIdCollection'  => $this->_extractPathways(),
			'enzymeIdCollection'   => $this->_extractEnzymes()
		);

		$this->_getLocalRP()->write($this->_getResourceFullPath(), $resource);
		$this->_resource = $resource;
		
		return $resource;
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function _extractDBLinks(){
		$plain = explode('ATOM', $this->_plain);
		$plain = $plain[0];
		$plain = explode('DBLINKS', $plain);
		$plain = trim($plain[1]);
		
		$pattern = '/.*:.*/';
		preg_match_all($pattern, $plain, $matches);
		
		$_matches = array();
		
		if( $matches[0] ){
			foreach($matches[0] as $match){
				$_matches[] = trim($match);
			}
		}

		return $_matches;
	}
	
	/**
	 * Matches all reaction IDs from plain text file.
	 * A regular expression is used to extract all
	 * patterns that match a KEGG reaction ID.
	 * 
	 * @return array
	 */
	protected function _extractReactions(){
		preg_match_all('/R[0-9]{5}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}
	
	/**
	 * Matches all pathway IDs from plain text file.
	 * A regular expression is used to extract all
	 * patterns that match a KEGG pathway ID.
	 * 
	 * @return array
	 */
	protected function _extractPathways(){
		preg_match_all('/map[0-9]{5}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}
	
	/**
	 * Matches all enzymes IDs from plain text file.
	 * A regular expression is used to extract all
	 * patterns that match a KEGG enzyme ID.
	 * 
	 * @return array
	 */
	protected function _extractEnzymes(){
		// NOTE: #pattern match fix '([0-9]{1,3}\.){4}' not working
		preg_match_all('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $this->_plain, $matches);
		
		$_matches = array();
		
		if( $matches ){
			foreach( $matches[0] as $match ){
				$_matches[] = trim($match);
			}	
		}
		
		return array_unique($_matches);
	}
}
