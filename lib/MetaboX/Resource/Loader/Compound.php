<?php
/**
 * @author	 Francesco Maiorano <francesco.maiorano@na.icar.cnr.it>
 * @link     https://github.com/Gregmayo/MetaboX-Library
 * @license  http://www.gnu.org/licenses/agpl-3.0.html GNU Affero GPLv3
 *
 * @copyright 2014 LabGTP ICAR-CNR
 *  
 * This file is part of The MetaboX Library.
 *
 *  The MetaboX Library is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The MetaboX Library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with The MetaboX Library. If not, see <http://www.gnu.org/licenses/>.
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
