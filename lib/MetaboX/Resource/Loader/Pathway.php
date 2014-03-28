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

class Pathway extends AbstractResourceLoader{
	
	/** 
	 * 
	 * @return object
	 */
	public function load(){
		$resource = $this->_getLocalRP()->read( $this->_getResourceFullPath() );
		if( $resource ){ return $resource; }
		
		$this->_plain = !is_null($this->_plain) ? $this->_plain : $this->_getRemoteRP()->read( $this->_getResourceFullUrl() );
		
		$resource = (object) array(
			'ID' 		 		    => str_replace('ko', 'map', $this->_resourceId),
			'name' 				    => $this->_extractAttributeByLabel('NAME'),
			'class' 			    => $this->_extractAttributeByLabel('CLASS'),
			'compoundIdCollection'  => $this->_extractCompounds(),
			'diseaseIdCollection'   => $this->_extractDiseases(),
			'moduleIdCollection'    => $this->_extractModules(),
			'orthologyIdCollection' => $this->_extractOrthology(),
			'koPathway' 		    => $this->getResourceId()
		);

		$this->_getLocalRP()->write($this->_getResourceFullPath(), $resource);
		$this->_resource = $resource;
		
		return $resource;
	}
	
	protected function _extractId(){
		preg_match_all('/ko[0-9]{5}/', $this->_plain, $matches);
		return $matches[0][0];
	}
	
	protected function _extractCompounds(){
		preg_match_all('/C[0-9]{5}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}
	
	protected function _extractDiseases(){
		preg_match_all('/H[0-9]{5}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}
	
	protected function _extractModules(){
		preg_match_all('/M[0-9]{5}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}

	protected function _extractOrthology(){
		preg_match_all('/K[0-9]{5}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}
}
