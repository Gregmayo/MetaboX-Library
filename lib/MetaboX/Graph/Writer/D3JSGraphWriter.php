<?php
/**
* MetaboX (http://labgtp.na.icar.cnr.it/metabox)
* 
* @link       http://github.com/labgtp/metabox/ for the canonical source repository
* @copyright  Copyright (c) 2013 ICAR-CNR Italy (http://www.icar.cnr.it)
* @license    http://labgtp.na.icar.cnr.it/metabox/license   BSD License
*/
namespace MetaboX\Graph\Writer;

class D3JSGraphWriter extends AbstractGraphWriter{
	protected $_ext = '.json';
	
	/**
	 * @param $file string
	 * @param $interactions array
	 */
	public function write($file, $interactions){
		if( !$interactions ){ return false; }
		
		$filename = $file . $this->_ext;
		$fh = fopen($filename, 'w') or die("[D3JSGraphWriter] can't write to file " . $filename);
		fwrite($fh, json_encode($interactions));
		fclose($fh);
	}

}
