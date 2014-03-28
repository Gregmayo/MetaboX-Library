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
namespace MetaboX\Graph;

class CorrelationGraphDelegate{
	private $_correlationService;
	private $_correlationData;
	
	public function __construct( $cs = null, $cd = null ){
		$this->_correlationService = $cs;
		$this->_correlationData    = $cd;
	}
	
	public function build(){
		$this->getCorrelationServiceInstance()->build();
	}
	
	public function setCorrelationData($d){ $this->_correlationData = $d; }
	public function getCorrelationData(){ return $this->_correlationData; }
	
	public function setCorrelationService($cs){ $this->_correlationService = $cs; }
	public function getCorrelationService(){ return $this->_correlationService; }
	
	public function getCorrelationServiceInstance(){
		if( !is_null($this->_correlationService) && !is_null($this->_correlationData) ){
			$classname = 'MetaboX\\Graph\\Correlation\\' . $this->_correlationService;
			return new $classname( $this->_correlationData );
		}
		
		return false;
	}
	
}