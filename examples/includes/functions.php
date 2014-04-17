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
 
function loadCompoundCollection($compounds, $config){
	$processed_compounds = array();

	$compoundLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['compound']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['compound'])
	);
	
	// Split compounds array in collections of size chunk_max_size
	$collections = array_chunk($compounds, $config['settings']['chunk_max_size']);

	foreach( $collections as $collection ){
		echo "[CPD] Loading collection " . implode(', ', $collection) . "\n";

		$collection_loader = new MetaboX\Resource\Loader\EntityCollection('Compound', $collection, $compoundLoaderConfig);
		$pcs = $collection_loader->load();

		foreach( $pcs as $pc ){
			$processed_compounds[$pc->ID] = $pc;
		}
	}
	
	return $processed_compounds;
}
 
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

function loadReactionCollection($processed_compounds, $config){
	$processed_reactions = array();
	$reactionLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['reaction']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['reaction']),
		'config' 	   => $config
	);
	
	$reactions = array();
	
	foreach( $processed_compounds as $id => $compound ){
		$reactions = array_merge($reactions, $compound->reactionIdCollection);
	}
	
	// Split reaction array in collections of size chunk_max_size
	$collections = array_chunk($reactions, $config['settings']['chunk_max_size']);
	
	foreach( $collections as $collection ){
		echo "[RN] Loading collection " . implode(', ', $collection) . "\n";
		
		$collection_loader = new MetaboX\Resource\Loader\EntityCollection('Reaction', $collection, $reactionLoaderConfig);
		$prs = $collection_loader->load();
		
		foreach( $prs as $pr ){
			$processed_reactions[$pr->ID] = $pr;	
		}
	}
	
	return $processed_reactions;
}

function loadReactions( $processed_compounds, $config ){
	$processed_reactions = array();
	$reactionLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['reaction']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['reaction']),
		'config' 	   => $config
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

function loadEnzymeCollection($processed_compounds, $config){
	$processed_enzymes = array();
	$enzymeLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['enzyme']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['enzyme'])
	);
	
	$enzymes = array();
	
	foreach( $processed_compounds as $id => $compound ){
		$enzymes = array_merge($enzymes, $compound->enzymeIdCollection);
	}
	
	// Split array in collections of size chunk_max_size
	$collections = array_chunk($enzymes, $config['settings']['chunk_max_size']);
	
	foreach( $collections as $collection ){
		echo "[EC] Loading collection " . implode(', ', $collection) . "\n";
		
		$collection_loader = new MetaboX\Resource\Loader\EntityCollection('Enzyme', $collection, $enzymeLoaderConfig);
		$pes = $collection_loader->load();
		
		foreach( $pes as $pe ){
			if( $pe->ID == '2.4.1.1' ){ var_dump('trovato');exit; }
			$processed_enzymes[$pe->ID] = $pe;
		}
	}
	
	return $processed_enzymes;
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

function loadPathwayCollection($processed_compounds, $config){
	$processed_pathways = array();
	$pathwayLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['pathway']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['pathway'])
	);
	
	$pathways = array();
	
	foreach( $processed_compounds as $id => $compound ){
		$pathways = array_merge($pathways, $compound->pathwayIdCollection);
	}
	
	// Split array in collections of size chunk_max_size
	$collections = array_chunk($pathways, $config['settings']['chunk_max_size']);
	
	foreach( $collections as $collection ){
		echo "[PW] Loading collection " . implode(', ', $collection) . "\n";
		
		$collection_loader = new MetaboX\Resource\Loader\EntityCollection('Pathway', $collection, $pathwayLoaderConfig);
		$pes = $collection_loader->load();
		
		foreach( $pes as $pe ){
			$processed_pathways[$pe->ID] = $pe;
		}
	}
	
	return $processed_pathways;
}

function loadPathways( $processed_compounds, $config ){
	$processed_pathways = array();
	$pathwayLoaderConfig = array(
		'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['pathway']),
		'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['pathway'])
	);
	
	// Retrieve and collect reactions information
	foreach( $processed_compounds as $id => $compound ){
		$pathway_list = $compound->pathwayIdCollection;
		
		if( $pathway_list ){
			echo "[CPD: " . $id . "] Loading " . count($pathway_list) . " pathways.\n";
			
			foreach( $pathway_list as $pw ){
				$_pw_id = trim($pw);
				
				$pw_loader = new MetaboX\Resource\Loader\Pathway($_pw_id, $pathwayLoaderConfig);
				$processed_pathways[$_pw_id] = $pw_loader->load();
			}	
		}
	}
	
	return $processed_pathways;
}

function getFullpath(){
	$path     = getcwd();
	$fullpath = str_replace(basename($path), '', $path);
	
	return $fullpath;
}

function deleteCacheFolder($folder){ 
	$files    = glob(getFullpath() . $folder);
	
	array_map('unlink', $files);
}

function createCacheDirectories(){
	
	if( !is_dir(getFullpath() . 'cache') && !file_exists(getFullpath() . 'cache') ){
		mkdir(getFullpath() . 'cache');
	}
	
	if( !is_dir(getFullpath() . 'cache/resources') && !file_exists(getFullpath() . 'cache/resources') ){
		mkdir(getFullpath() . 'cache/resources');
	}
	
	if( !is_dir(getFullpath() . 'graphs') && !file_exists(getFullpath() . 'graphs') ){
		mkdir(getFullpath() . 'graphs');
	}
	
	$fullpath = getFullpath() . 'cache/resources/';
	$directories = array('compounds', 'reactions', 'enzymes', 'pathways');
	
	foreach( $directories as $dir ){
		if( !is_dir($fullpath . $dir) && !file_exists($fullpath . $dir) ){
			mkdir($fullpath . $dir);
		}	
	}
}