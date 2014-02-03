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

abstract class AbstractGraphBuilder{
	protected $_global_graph = array(
		'node_collection'   => array(),
		'weighted_edgelist' => array()
	);
	
	protected $_sub_graph = array(
		'node_collection'   => array(),
		'weighted_edgelist' => array(),
		'connected'         => array(),
		'not_connected'     => array()
	);
		
	abstract public function build( $compounds = false );
	
	public function getGlobalGraph(){ return $this->_global_graph; }
	public function getSubGraph(){ return $this->_sub_graph; }
	
	protected function _prepareCouple($A, $B){
		$couple = array($A, $B);
		sort($couple);
		
		return $couple;
	}
	
	/**
	 * @param $weighted_edgelist array
	 * 
	 * @return $network_interactions array
	 */
	protected function _prepareOutput( $weigthed_edgelist ){
		if( count($weigthed_edgelist) <= 0 ){ return false; }
		
		$network_interactions = array();
		
		foreach( $weigthed_edgelist as $couple => $weight ){
			$items = explode(',', $couple);
			$source = trim($items[0]);
			$target = trim($items[1]);
			
			$graph_couple = array(
				'source' => $source,
				'weight' => $weight,
				'target' => $target
			);
			
			$network_interactions[] = $graph_couple;
		}
		
		return $network_interactions;
	}
}
