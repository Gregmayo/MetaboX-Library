<?php
/**
 * 
 */
 
ini_set('memory_limit', '-1');

include('_init.php');

// Track execution time
$start = microtime(true);

if( $argc != 2 ){
	echo "[Usage]: php " . basename(__FILE__) . " <genes_file.csv> [<config.ini>]\n";
	exit;
}

$config_file = file_exists($argv[2]) ? $argv[2] : 'config.ini';

if( !file_exists( $argv[1] ) ){
	echo "[Error]: genes list file does not exists (" . $argv[1] . ")\n";
	exit;
}

$content = file_get_contents($argv[1]);

if( !$content ){
	echo "[Error]: genes list file is empty (" . $argv[1] . ")\n";
	exit;
}

$genes   = explode(',', $content);
$config  = parse_ini_file($config_file, true);

$hydra_pathway_list = array();
$processed_genes = array();

$hydragenesLoaderConfig = array(
	'remoteRP'     => new MetaboX\Resource\Provider\Remote\KEGG($config['url']['resource_baseurl'], $config['url']['gene']),
	'localRP'      => new MetaboX\Resource\Provider\Local\JSON($config['directory']['resource_basepath'], $config['directory']['gene'])
); 

foreach( $genes as $gene ){
	$_gn_id = trim($gene);
	
	$gn_loader = new MetaboX\Resource\Loader\Gene($_gn_id, $hydragenesLoaderConfig);
	$processed_genes[$_gn_id] = $gn_loader->load();
}

foreach( $processed_genes as $_gene ){
	$hydra_pathway_list = array_merge($hydra_pathway_list, $_gene->pathwayIdCollection);
}

$pathways = array_unique($hydra_pathway_list);

print_r($pathways);
echo implode(',', $pathways);

// How long did it take?
$time_taken = microtime(true) - $start;
echo "Time taken " . number_format($time_taken, 2) . " seconds.\n";