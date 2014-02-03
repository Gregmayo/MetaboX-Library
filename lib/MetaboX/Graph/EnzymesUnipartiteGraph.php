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

class EnzymesUnipartiteGraph extends AbstractGraphBuilder{
	protected $_ec_list = array();
	protected $_rn_list = array();
	protected $_rn_keys = array();
	
	public function __construct($ec_list, $rn_list){
		$this->_ec_list = $ec_list;
		$this->_rn_list = $rn_list;
		$this->_rn_keys = array_keys($this->_rn_list);
	}
	
	protected function _getCouple($i, $j){
		$A = $i->ID;
		$B = $j->ID;
		
		$intA = (int)str_replace('.', '', $i->ID);
		$intB = (int)str_replace('.', '', $j->ID);
		
		$_c = array( $intA => $A, $intB => $B );
		$_couple = array($intA, $intB);
		
		sort($_couple);
		
		//var_dump($_c[$_couple[0]], $_c[$_couple[1]]);exit;
		
		return array($_c[$_couple[0]], $_c[$_couple[1]]);
	}
	
	public function build( $compounds = false ){
		$_all_edgelist = array();
		$_sub_edgelist = array();
		
		$_ec_size  = count($this->_ec_list);
		$_cpd_size = !$compounds ? 0 : count($compounds);
		
		for( $i = 0; $i < $_ec_size; $i++ ){
			$_i_enzyme    = $this->_ec_list[$i];
			$_i_products = $this->_getReactionProducts($_i_enzyme->reaction);
			
			for( $j = $i + 1; $j < $_ec_size; $j++ ){
				$_j_enzyme    = $this->_ec_list[$j];
				$_j_reactants = $this->_getReactionSubstrates($_j_enzyme->reaction);
				
				if( count($_i_products) > 0 && count($_j_reactants) > 0 ){
					if( count(array_intersect($_i_products, $_j_reactants)) > 0 ){
						$this->_global_graph['node_collection'][] = $_i_enzyme->ID;
						$this->_global_graph['node_collection'][] = $_j_enzyme->ID;
						
						$couple = $this->_getCouple($_i_enzyme, $_j_enzyme);

						$row = implode(',', $couple);
						
						$_all_edgelist[] = $row;
						
						if( $_cpd_size > 0 ){
							if( count(array_intersect($_i_products, $compounds)) > 0 && count(array_intersect($_j_reactants, $compounds)) > 0 ){
								$this->_sub_graph['node_collection'][] = $_i_enzyme->ID;
								$this->_sub_graph['node_collection'][] = $_j_enzyme->ID;
								
								$this->_sub_graph['connected'][] = $_i_enzyme->ID;
								$this->_sub_graph['connected'][] = $_j_enzyme->ID;
						
								$_sub_edgelist[] = $row;
							}	
						}
					}					
				}

			}
		}
		
		$this->_global_graph['node_collection'] = array_unique($this->_global_graph['node_collection']);
		$this->_sub_graph['node_collection']    = array_unique($this->_sub_graph['node_collection']);
		
		$this->_global_graph['weighted_edgelist']  = $this->_prepareOutput( array_count_values($_all_edgelist) );
		$this->_sub_graph['weighted_edgelist']     = $this->_prepareOutput( array_count_values($_sub_edgelist) );
		
		if( $compounds ){
			$this->_sub_graph['connected']     = array_unique($this->_sub_graph['connected']);
			$this->_sub_graph['not_connected'] = array_diff($compounds, $this->_sub_graph['connected']);
		}
		
		return $this;
	}
	
	protected function _getReactionSubstrates($rn){
		//return $this->_rn_list[$rn]->reactants->compounds;
		
		foreach( $rn as $r ){
			if( in_array($r, $this->_rn_keys) ){
				return $this->_rn_list[$r]->reactants->compounds;
			} 
		}
	}
	
	protected function _getReactionProducts($rn){
		//return $this->_rn_list[$rn]->products->compounds;
		
		foreach( $rn as $r ){
			if( in_array($r, $this->_rn_keys) ){
				return $this->_rn_list[$r]->products->compounds;
			} 
		}
	}
	
}
