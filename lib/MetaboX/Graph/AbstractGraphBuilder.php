<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
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
