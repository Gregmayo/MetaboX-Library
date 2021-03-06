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

class ReactantsGraph extends AbstractGraphBuilder{
	private $_sourcesCollection;
	
	public function __construct( $reactionCollection ){
		$this->_sourcesCollection = $reactionCollection;
	}
	
	public function build( $compounds = false ){
		$_all_edgelist = array();
		$_sub_edgelist = array();
		
		if( $this->getOrganism() != 'all' ){
			$this->_sourcesCollection = $this->_filterByOrganism( $this->_sourcesCollection );
		}
		
		foreach( $this->_sourcesCollection as $reaction ){
			$reactants = $reaction->reactants->compounds;
			$nReactants = count($reactants);
			
			if( !is_array($reactants) ){ continue; }
			if( !array_intersect($reactants, $compounds) ){ continue; }
			
			for( $i = 0; $i < $nReactants; $i++ ){
				for( $j = $i + 1; $j < $nReactants; $j++ ){
					
					$this->_global_graph['node_collection'][] = $this->_translateGlycan($reactants[$i]);
					$this->_global_graph['node_collection'][] = $this->_translateGlycan($reactants[$j]);
					
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
	
	protected function _filterByOrganism($rns){
		if( !count($rns) ){ return false; }
		
		$list = array();
		foreach( $rns as $rn ){
			if( count($rn->organismIdCollection) ){
				if( in_array($this->getOrganism(), $rn->organismIdCollection) ){
					$list[] = $rn;
				}
			}
		}
		
		return $list;
	}
	
}
