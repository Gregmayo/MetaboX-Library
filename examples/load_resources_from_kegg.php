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
 
include('includes/_init.php');
include('includes/functions.php');

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

// How long did it take?
$time_taken = microtime(true) - $start;
echo "Time taken " . number_format($time_taken, 2) . " seconds.\n";