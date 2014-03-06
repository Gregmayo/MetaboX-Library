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
namespace MetaboX\Resource\Provider\Local;

class JSON{
	private $_resource_dir;
	private $_resource_type;
	private $_ext = '.json';
	
	public function __construct( $dir, $type ){
		$this->_resource_dir  = $dir;
		$this->_resource_type = $type;
	}
	
	public function getPath(){ return $this->_resource_dir; }
	public function getPathByResourceId( $id ){ return $this->getPath() . $this->_resource_type . $id . $this->_ext; }
	
	public function write($file, $data){
		$fh = fopen($file, 'w') or die("[JSONResource] can't write to file " . $file);
		fwrite($fh, json_encode($data));
		fclose($fh);
	}
	
	public function read($file){
		if( file_exists($file) ){
			return json_decode(file_get_contents($file));
		}
		
		return '';
	}
}
