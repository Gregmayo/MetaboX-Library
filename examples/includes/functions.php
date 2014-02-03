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
 
function loadCompounds( $compounds, $config ){
	$processed_compounds = array();
	
	$graph_path = getcwd() . '/' . $config['directory']['graph'];

	$compoundLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['compound']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['compound'])
	);
	
	// Retrieve and collect compound information
	foreach( $compounds as $compound ){
		$_cpd_id = trim($compound);
		
		echo "Loading compound " . $_cpd_id . "\n";
		
		$cpd_loader = new MetaboX\Resource\Loader\Compound($_cpd_id, $compoundLoaderConfig);
		$processed_compounds[$_cpd_id] = $cpd_loader->load();
	}
	
	return $processed_compounds;
}

function loadReactions( $processed_compounds, $config ){
	$processed_reactions = array();
	$reactionLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['reaction']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['reaction'])
	);
	
	// Retrieve and collect reactions information
	foreach( $processed_compounds as $id => $compound ){
		$reaction_list = $compound->reactionIdCollection;
		
		echo "[CPD: " . $id . "] Loading " . count($reaction_list) . " reactions.\n";
		
		if( $reaction_list ){
			foreach( $reaction_list as $rn ){
				$_rn_id = trim($rn);
				
				$rn_loader = new MetaboX\Resource\Loader\Reaction($_rn_id, $reactionLoaderConfig);
				$processed_reactions[$_rn_id] = $rn_loader->load();
			}	
		}
	}
	
	return $processed_reactions;
}

function loadEnzymes( $processed_compounds, $config ){
	$processed_enzymes = array();
	$enzymeLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['enzyme']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['enzyme'])
	);
	
	// Retrieve and collect reactions information
	foreach( $processed_compounds as $id => $compound ){
		$enzyme_list = $compound->enzymeIdCollection;
		
		if( $enzyme_list ){
			echo "[CPD: " . $id . "] Loading " . count($enzyme_list) . " enzymes.\n";
			
			foreach( $enzyme_list as $ec ){
				$_ec_id = trim($ec);
				
				$ec_loader = new MetaboX\Resource\Loader\Enzyme($_ec_id, $enzymeLoaderConfig);
				$processed_enzymes[$_ec_id] = $ec_loader->load();
			}	
		}
	}
	
	return $processed_enzymes;
}

function deleteCacheFolder($folder){
	$path     = getcwd();
	$fullpath = str_replace(basename($path), '', $path); 
	$files    = glob($fullpath . $folder);
	
	array_map('unlink', $files);
}
