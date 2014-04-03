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

class CorrelationGraph{
	private $_correlationService;
	private $_correlationData;

	private $_correlationServiceInstance;

	private $_nodes;
	private $_threshold;

	public function __construct( $cs, $cd, $tr ){
		$this->_correlationService = $cs;
		$this->_correlationData    = $cd;

		$this->_nodes     = $cd[0];
		$this->_threshold = $tr;
	}
	
	public function build(){
		$this->getCorrelationServiceInstance()->build();
	}
	
	public function getEdgelist(){
		return $this->getCorrelationServiceInstance()->getEdgelist();
	}
	
	public function setCorrelationData($d){ $this->_correlationData = $d; }
	public function getCorrelationData(){ return $this->_correlationData; }
	
	public function setCorrelationService($cs){ $this->_correlationService = $cs; }
	public function getCorrelationService(){ return $this->_correlationService; }
	
	protected function _getCorrelationServiceInstance(){ return $this->_correlationServiceInstance; }
	
	public function getCorrelationServiceInstance(){
		$instance = null;

		if( !is_null($this->_correlationServiceInstance) ){
			return $this->_getCorrelationServiceInstance();
		}else{
			if( !is_null($this->_correlationService) && !is_null($this->_correlationData) ){
				$classname = 'MetaboX\\Graph\\Correlation\\' . $this->_correlationService;
				$this->_correlationServiceInstance = new $classname( $this->_nodes, $this->_correlationData, $this->_threshold );

				return $this->_getCorrelationServiceInstance();
			}
		}
	}
	
}