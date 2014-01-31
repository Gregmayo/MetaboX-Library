<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Graph;

class EnzymesBipartiteGraph extends AbstractGraphBuilder{
	protected $_cpd_list = array();
	protected $_ec_list  = array();
	protected $_rn_list  = array();
	
	public function __construct($cpd_list, $ec_list, $rn_list){
		$this->_cpd_list = $cpd_list;
		$this->_ec_list  = $ec_list;
		$this->_rn_list  = $rn_list;
	}
	
	/**
	 * For each input compound A
	 * for each reaction X of compound A
	 * if compound A is a reactant or a product of X
	 * connect A to the enzyme E that catalyzes X
	 *
	 */
	
	public function build( $compounds = false ){
		$_all_edgelist = array();
		$_sub_edgelist = array();
		
		$_ec_size  = count($this->_ec_list);
		$_input_cpd_size = !$compounds ? 0 : count($compounds);
		
		foreach( $this->_cpd_list as $cpd ){
			$rns = $cpd->reactionIdCollection;
			
			if( $rns ){
				foreach( $rns as $rn ){
					$reactants = $this->_rn_list[$rn]->reactants->compounds;
					$products  = $this->_rn_list[$rn]->products->compounds;
					$enzyme    = $this->_rn_list[$rn]->enzyme;
					
					if( in_array($cpd->ID, $reactants) || in_array($cpd->ID, $products) ){
						// Connect cpd and enzyme
						$this->_global_graph['node_collection'][] = $cpd->ID;
						$this->_global_graph['node_collection'][] = $enzyme;
						
						$row = implode(',', array($cpd->ID, $enzyme));
						
						$_all_edgelist[] = $row;
						
						if( $_input_cpd_size > 0 ){
							if( in_array($cpd->ID, $compounds) ){
								$this->_sub_graph['node_collection'][] = $cpd->ID;
								$this->_sub_graph['node_collection'][] = $enzyme;
								
								$this->_sub_graph['connected'][] = $cpd->ID;
								$this->_sub_graph['connected'][] = $enzyme;
						
								$_sub_edgelist[] = $row;
							}
						}
					}
				} // endforeach $rns
			}	
		} // endforeach $this->_cpd_list
		
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
	
}