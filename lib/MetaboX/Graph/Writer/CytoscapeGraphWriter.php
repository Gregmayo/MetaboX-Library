<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Graph\Writer;

class CytoscapeGraphWriter extends AbstractGraphWriter{
	protected $_ext = '.sif';

	/**
	 * @param $data array
	 * 
	 * @return $output string
	 */
	protected function _prepare($data){
		$output = '';
		
		foreach( $data as $interaction ){
			$output .= implode("\t", $interaction) . "\n";
		}
		
		return $output;
	}
	
	/**
	 * @param $file string
	 * @param $interactions array
	 */
	public function write($file, $interactions){
		if( !$interactions ){ return false; }
		
		$output = $this->_prepare($interactions);
		
		$filename = $file . $this->_ext;
		$fh = fopen($filename, 'w') or die("[CytoscapeGraphWriter] can't write to file " . $filename);
		fwrite($fh, $output);
		fclose($fh);
	}

}
