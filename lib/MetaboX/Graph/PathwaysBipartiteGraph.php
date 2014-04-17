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

use MetaboX\Resource\Provider\Remote as MXR;

class PathwaysBipartiteGraph extends AbstractGraphBuilder{
	protected $_compoundCollection = array();
	protected $_pathwayCollection  = array();
	
	public function __construct($pc, $pw){
		$this->_compoundCollection = $pc;
		$this->_pathwayCollection  = $pw;
	}
	
	public function build( $compounds = false ){
		$_all_edgelist = array();
		
		$pathways = $this->getPathwayListByOrganism();
		
		foreach( $this->_compoundCollection as $c ){
			$cpathways = $c->pathwayIdCollection;
			
			foreach( $cpathways as $p ){
				if( $this->getOrganism() != 'all' ){
					$p = str_replace('ko', strtolower($this->getOrganism()), $p);
				}
				
				if( !in_array($p, $pathways) ){ continue; }
				
				$this->_global_graph['node_collection'][] = $c->ID;
				$this->_global_graph['node_collection'][] = $p;
				
				$row = implode(',', array($c->ID, $p));

				$_all_edgelist[] = $row;
			}
		}
		
		$this->_global_graph['node_collection'] = array_unique($this->_global_graph['node_collection']);
		$this->_global_graph['weighted_edgelist']  = $this->_prepareOutput( array_count_values($_all_edgelist) );
		
		return $this;
	}
	
	public function getPathwayListByOrganism(){
		if( $this->getOrganism() == 'all' ){ return array(); }
		
		$rp = new MXR\KEGG();
		$pw_list = $rp->read( 'http://rest.kegg.jp/list/pathway/' . strtolower($this->getOrganism()) );
		
		preg_match_all('/' . strtolower($this->getOrganism()) . '[0-9]{5}/', $pw_list, $matches);
		$pws = array_unique($matches[0]);
		
		$org = $this->getOrganism();
		array_walk($pws, function(&$item, $org){ $item = str_replace('ko', $org, $item); });
		
		return $pws;
	}
}
