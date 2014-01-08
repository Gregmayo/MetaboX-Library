<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Graph;

class ReactantsGraph extends AbstractGraphBuilder{
	private $_sourcesCollection;
	
	public function __construct( $reactionCollection ){
		$this->_sourcesCollection = $reactionCollection;
	}
	
	public function build( $compounds = false ){
		$_all_edgelist = array();
		$_sub_edgelist = array();
		
		foreach( $this->_sourcesCollection as $reaction ){
			$reactants = $reaction->reactants->compounds;
			$nReactants = count($reactants);
			
			for( $i = 0; $i < $nReactants; $i++ ){
				for( $j = $i + 1; $j < $nReactants; $j++ ){
					$this->_global_graph['node_collection'][] = $reactants[$i];
					$this->_global_graph['node_collection'][] = $reactants[$j];
					
					$row = implode(',', $this->_prepareCouple($reactants[$i], $reactants[$j]));
					
					$_all_edgelist[] = $row;
					
					if( in_array($reactants[$i], $compounds) && in_array($reactants[$j], $compounds) ){
						$this->_sub_graph['node_collection'][] = $reactants[$i];
						$this->_sub_graph['node_collection'][] = $reactants[$j];
						
						$this->_sub_graph['connected'][] = $reactants[$i];
						$this->_sub_graph['connected'][] = $reactants[$j];
				
						$_sub_edgelist[] = $row;
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
	
	/**
	 * @param $subNodesCollection array
	 * 
	 * @return $this ReactantsGraph
	 
	public function prepareOutput( $subNodesCollection = null ){
		$connected = array();
		
		if( count($this->_weigthed_edgelist) <= 0 ){ return false; }
		
		foreach( $this->_weigthed_edgelist as $couple => $weight ){
			$items = explode(',', $couple);
			$source = trim($items[0]);
			$target = trim($items[1]);
			
			$graph_couple = array(
				'source' => $source,
				'weight' => $weight,
				'target' => $target
			);
			
			$this->_network_interactions[] = $graph_couple;

			if( in_array($source, $subNodesCollection) && in_array($target, $subNodesCollection) ){
				$this->_subnetwork_interactions[] = $graph_couple;
				
				$connected[] = $source;
				$connected[] = $target;
				
				$this->_node_connections[$source][] = $target;
				$this->_node_connections[$target][] = $source;
			}
			
		}

		if( !is_null($subNodesCollection) ){
			$this->_connected     = array_unique($connected);
			$this->_not_connected = array_diff($subNodesCollection, $connected);
			
			if( $this->_not_connected ){
				foreach( $this->_not_connected as $cpd ){
					$this->_subnetwork_interactions[] = array( 'source' => $cpd );
				}
			}
	
		}
		
		return $this;
	}
	*/
	
}
