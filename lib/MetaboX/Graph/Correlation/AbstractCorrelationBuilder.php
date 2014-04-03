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
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with The MetaboX Library. If not, see <http://www.gnu.org/licenses/>.
 */
namespace MetaboX\Graph\Correlation;

abstract class AbstractCorrelationBuilder{
	private $_nodes;
	private $_correlationData;
	private $_threshold;
	private $_edgelist = array();

	public function __construct( $nodes, $data, $t ){
		$this->_nodes 			= $nodes;
		$this->_correlationData = $data;
		$this->_threshold       = floatval($t);
	}
	
	abstract public function build();

	protected function _getVector($idx){
		$v = array();

		foreach( $this->_correlationData as $r ){
			$v[] = $r[$idx];
		}
	
		unset($v[0]);
		return $v;
	}
	
	protected function _getPair($A, $B, $w){
		$hA = md5($A);
		$hB = md5($B);
		
		if( $hA < $hB ){
			return array( 'source' => $A, 'weight' => $w, 'target' => $B );
		}else{
			return array( 'source' => $B, 'weight' => $w, 'target' => $A );
		}
	}
	
	public function getNodes(){ return $this->_nodes; }
	public function getEdgelist(){ return $this->_edgelist; }
	
	public function getThreshold(){ return $this->_threshold; }
	public function setThreshold($t){ $this->_threshold = $t; }
	
	public function getCorrelationData(){ return $this->_correlationData; }
	public function setCorrelationData($cd){ $this->_correlationData = $cd; }
	
	public function addEdge($pair){
		$insert = true;
		foreach( $this->_edgelist as $e ){
			if( ($e['source'] == $pair['source']) && ($e['target'] == $pair['target']) ){
				$insert = false;
			}
		}
		
		if( $insert ){ $this->_edgelist[] = $pair; }
	}
}