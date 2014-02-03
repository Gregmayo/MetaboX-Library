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
 
ini_set('memory_limit', '-1');

include('_init.php');

// Track execution time
$start = microtime(true);

if( $argc != 2 ){
	echo "[Usage]: php " . basename(__FILE__) . " <compound_file.csv> [<config.ini>]\n";
	exit;
}

$config_file = file_exists($argv[2]) ? $argv[2] : 'config.ini';

if( !file_exists( $argv[1] ) ){
	echo "[Error]: compound list file does not exists (" . $argv[1] . ")\n";
	exit;
}

$content = file_get_contents($argv[1]);

if( !$content ){
	echo "[Error]: compound list file is empty (" . $argv[1] . ")\n";
	exit;
}

$compounds   = explode(',', $content);
$config      = parse_ini_file($config_file, true);

$processed_compounds = array();

$graph_path = getcwd() . '/' . $config['directory']['graph'];

$compoundLoaderConfig = array(
	'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['compound']),
	'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['compound'])
);

// Retrieve and collect compound information
foreach( $compounds as $compound ){
	$_cpd_id = trim($compound);
	
	$cpd_loader = new MetaboX\Resource\Loader\Compound($_cpd_id, $compoundLoaderConfig);
	$processed_compounds[$_cpd_id] = $cpd_loader->load();
}

$processed_reactions = array();
$reactionLoaderConfig = array(
	'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['reaction']),
	'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['reaction'])
);

// Retrieve and collect reactions information
foreach( $processed_compounds as $id => $compound ){
	$reaction_list = $compound->reactionIdCollection;
	
	if( $reaction_list ){
		foreach( $reaction_list as $rn ){
			$_rn_id = trim($rn);
			
			$rn_loader = new MetaboX\Resource\Loader\Reaction($_rn_id, $reactionLoaderConfig);
			$processed_reactions[$_rn_id] = $rn_loader->load();
		}	
	}
}

// Create reactants graph
$reactants_graph = new MetaboX\Graph\ReactantsGraph($processed_reactions);
$reactants_graph->build($compounds);

$reactants_network    = $reactants_graph->getGlobalGraph();
$reactants_subnetwork = $reactants_graph->getSubGraph();

// Write to file
$cytoscape_writer = new MetaboX\Graph\Writer\CytoscapeGraphWriter();
$cytoscape_writer->write($graph_path . 'reactants_all', $reactants_network['weighted_edgelist']);

// Write only interactions between compounds in '$compounds'
$cytoscape_writer->write($graph_path . 'reactants_input', $reactants_subnetwork['weighted_edgelist']);

// Write it to JSON (D3JS)
$d3js_writer = new MetaboX\Graph\Writer\D3JSGraphWriter();
$d3js_writer->write($graph_path . 'reactants_all', $reactants_network['weighted_edgelist']);

// Write only interactions between compounds in '$compounds'
$d3js_writer->write($graph_path . 'reactants_input', $reactants_subnetwork['weighted_edgelist']);

// Print node connection information
/*
echo count($reactants_graph->getConnectedNodeList()) . " compounds are connected in this network.\n";
foreach( $compounds as $cpd ){
	echo $cpd . " has " . count($reactants_graph->getNodeConnections($cpd)) . " input interactions\n";
}
*/

// How long did it take?
$time_taken = microtime(true) - $start;
echo "Time taken " . number_format($time_taken, 2) . " seconds.\n";
