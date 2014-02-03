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

include('includes/functions.php');
include('includes/_init.php');

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

$processed_compounds = loadCompounds( $compounds, $config );
$processed_reactions = loadReactions( $processed_compounds, $config );
$processed_enzymes   = loadEnzymes( $processed_compounds, $config );

// Create enzymes unipartite graph
$enzymes_uni_graph = new MetaboX\Graph\EnzymesUnipartiteGraph(array_values($processed_enzymes), $processed_reactions);
$enzymes_uni_graph->build( $compounds );

$ec_network    = $enzymes_uni_graph->getGlobalGraph();
$ec_subnetwork = $enzymes_uni_graph->getSubGraph();
$filename = 'enzyme_unipartite';

// Write to file
$cytoscape_writer = new MetaboX\Graph\Writer\CytoscapeGraphWriter();
$cytoscape_writer->write($graph_path . $filename . '_all', $ec_network['weighted_edgelist']);

// Write only interactions between compounds in '$compounds'
$cytoscape_writer->write($graph_path . $filename . '_input', $ec_subnetwork['weighted_edgelist']);

// Write it to JSON (D3JS)
$d3js_writer = new MetaboX\Graph\Writer\D3JSGraphWriter();
$d3js_writer->write($graph_path . $filename . '_all', $ec_network['weighted_edgelist']);

// Write only interactions between compounds in '$compounds'
$d3js_writer->write($graph_path . $filename . '_input', $ec_subnetwork['weighted_edgelist']);

// How long did it take?
$time_taken = microtime(true) - $start;
echo "Time taken " . number_format($time_taken, 2) . " seconds.\n";