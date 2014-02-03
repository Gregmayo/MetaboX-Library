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