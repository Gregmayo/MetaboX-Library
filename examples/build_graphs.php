<?php
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

// ENZYMES ------------------------------------------------------------------------------------------------------------------------------
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

// Create enzymes unipartite graph
$enzymes_uni_graph = new MetaboX\Graph\EnzymesUnipartiteGraph(array_values($processed_enzymes), $processed_reactions);
$enzymes_uni_graph->build( $compounds );

$ec_network    = $enzymes_uni_graph->getGlobalGraph();
$ec_subnetwork = $enzymes_uni_graph->getSubGraph();
$filename = 'enzyme_unipartite';

// Create enzymes bipartite graph
/*
$enzymes_bi_graph = new MetaboX\Graph\EnzymesBipartiteGraph($processed_compounds, $processed_enzymes, $processed_reactions);
$enzymes_bi_graph->build( $compounds );

$ec_network    = $enzymes_bi_graph->getGlobalGraph();
$ec_subnetwork = $enzymes_bi_graph->getSubGraph();
$filename = 'enzyme_bipartite';
*/

// Write to file
$cytoscape_writer = new MetaboX\Graph\Writer\CytoscapeGraphWriter();
$cytoscape_writer->write($graph_path . $filename . '_all', $ec_network['weighted_edgelist']);

// Write only interactions between compounds in '$compounds'
$cytoscape_writer->write($graph_path . $filename . '_input', $ec_subnetwork['weighted_edgelist']);

// Write it to JSON (D3JS)
// $d3js_writer = new MetaboX\Graph\Writer\D3JSGraphWriter();
// $d3js_writer->write($graph_path . $filename . '_all', $ec_network['weighted_edgelist']);

// Write only interactions between compounds in '$compounds'
// $d3js_writer->write($graph_path . $filename . '_input', $ec_subnetwork['weighted_edgelist']);

// How long did it take?
$time_taken = microtime(true) - $start;
echo "Time taken " . number_format($time_taken, 2) . " seconds.\n";
