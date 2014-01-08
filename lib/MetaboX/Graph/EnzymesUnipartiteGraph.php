<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Graph;

class EnzymesUnipartiteGraph extends AbstractGraphBuilder{
	protected $_ec_list = array();
	protected $_rn_list = array();
	
	public function __construct($ec_list, $rn_list){
		$this->_ec_list = $ec_list;
		$this->_rn_list = $rn_list;
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
						
						$row = implode(',', $this->_prepareCouple($_i_enzyme->ID, $_j_enzyme->ID));
						
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
		return $this->_rn_list[$rn]->reactants->compounds;
	}
	
	protected function _getReactionProducts($rn){
		return $this->_rn_list[$rn]->products->compounds;
	}
	
}
