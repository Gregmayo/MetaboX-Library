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
namespace MetaboX\Graph\Writer;

class D3JSGraphWriter extends AbstractGraphWriter{
	protected $_ext = '.json';
	
	/**
	 * @param $file string
	 * @param $interactions array
	 */
	public function write($file, $interactions){
		if( !$interactions ){ return false; }
		
		$filename = $file . $this->_ext;
		$fh = fopen($filename, 'w') or die("[D3JSGraphWriter] can't write to file " . $filename);
		fwrite($fh, json_encode($interactions));
		fclose($fh);
	}

}
