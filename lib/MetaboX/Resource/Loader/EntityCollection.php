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

class EntityCollection{
	protected $_collection = array();
	protected $_resources = array();
	
	private $_remoteResourceProvider;
	private $_localResourceProvider;
	private $_cf;
	private $_plain;
	private $_className;
	
	public function __construct($cn, $cl, $cf){
		$this->_collection = $cl;
		
		$this->_remoteResourceProvider = $cf['remoteRP'];
		$this->_localResourceProvider  = $cf['localRP'];
		$this->_cf = $cf;
		
		$this->_className = $cn;
	}
	
	/**
	 * Retrieve the set of resources information using collections.
	 * 
	 * FIXME: Testare il funzionamento!!!
	 *        Necessario refactoring per centralizzare i metodi di
	 *        manipolazione del plain text per ogni entità.
	 *        Estendere il tutto anche alle reazioni, enzimi e pathway.
	 */
	public function load(){
		$loaded = array();
		
		foreach( $this->_collection as $entityId ){
			$resource = $this->_getLocalRP()->read( $this->_getResourceFullPath($entityId) );
			if( $resource ){ $loaded[] = $entityId; $this->_resources[] = $resource; }
		}

		$this->_collection = array_diff($this->_collection, $loaded);
		
		$plain = $this->_getRemoteRP()->read( $this->_getResourceFullUrl() );
		
		$raw_collection = array_filter(explode('///', $plain), function($v){ return strlen($v) < 10 ? false : true; });
		
		foreach( $raw_collection as $item ){
			$classname = 'MetaboX\\Resource\\Loader\\' . $this->_className;
			$cl = new $classname(null, $this->_getConfig(), $item);
			$resource = $cl->load();
			
			$this->_resources[] = $resource;
		}
		
		return $this->_resources;
	}
	
	protected function _getEntityClassname(){ return $this->_className; }
	protected function _getRemoteRP(){ return $this->_remoteResourceProvider; }
	protected function _getLocalRP(){ return $this->_localResourceProvider; }
	protected function _getConfig(){ return $this->_cf; }
	
	protected function _getResourceFullUrl(){ return $this->_getRemoteRP()->getUrlByResourceIds($this->_collection); }
	protected function _getResourceFullPath($resourceId){ return $this->_getLocalRP()->getPathByResourceId($resourceId); }
	
}