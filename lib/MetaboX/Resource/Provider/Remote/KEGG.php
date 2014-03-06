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
namespace MetaboX\Resource\Provider\Remote;

class KEGG{
	private $_resource_url;
	private $_resource_type;
	private $_resource_ids;
	
	public function __construct( $url, $type, $resourceIds = false ){
		$this->_resource_url  = $url;
		$this->_resource_type = $type;
		$this->_resource_ids  = $resourceIds;
	}
	
	public function getUrl(){ return $this->_resource_url; }
	public function getUrlByResourceId( $id ){ return $this->getUrl() . $this->_resource_type . $id; }
	
	public function getUrlByResourceIds( $ids = false ){
		$list = $ids ? $ids : $this->_resource_ids;
		if( !$list ){ return ''; }
		
		return $this->getUrl() . $this->_resource_type . implode('+' . $this->_resource_type, $list);
	}
	
	public function read( $url ){
		$c = curl_init();
	    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($c, CURLOPT_URL, $url);
	    $contents = curl_exec($c);
	    curl_close($c);
	
	    if( $contents ){ return $contents; }
	    else{ return FALSE; }
	}
}
