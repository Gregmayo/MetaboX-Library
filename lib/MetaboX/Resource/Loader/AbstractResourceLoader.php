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

abstract class AbstractResourceLoader{
	protected $_resourceId;
	protected $_resource;
	
	private $_remoteResourceProvider;
	private $_localResourceProvider;
	
	protected $_plain = null;
	
	public function __construct( $resource_id, $config, $plain = false ){
		$this->_resourceId = $resource_id;
		
		$this->_remoteResourceProvider = $config['remoteRP'];
		$this->_localResourceProvider  = $config['localRP'];
		
		if( $plain ){
			$this->_plain = $plain;
			$this->_resourceId = $this->_extractId($plain);
		}
	}
	
	abstract public function load();
	
	public function getResourceId(){ return $this->_resourceId; }
	public function getResource(){ return $this->_resource; }
	
	protected function _getRemoteRP(){ return $this->_remoteResourceProvider; }
	protected function _getLocalRP(){ return $this->_localResourceProvider; }
	
	protected function _getResourceFullUrl(){ return $this->_getRemoteRP()->getUrlByResourceId($this->_resourceId); }
	protected function _getResourceFullPath(){ return $this->_getLocalRP()->getPathByResourceId($this->_resourceId); }
	
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
