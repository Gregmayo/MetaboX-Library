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
namespace MetaboX\Graph\Correlation;

class Pearson extends AbstractCorrelationBuilder{
	
	// ref. http://www.davegardner.me.uk/blog/tag/pearson-correlation/
	public function getCorrelation($fa, $fb){
		$sA = count($fa);
		$sB = count($fb);

		if( $sA != $sB ){ return false; }
		
		$sumOfA = $sumOfB = $sumOfSquareA = $sumOfSquareB = $sumOfProducts = 0;

		for( $i = 0; $i < $sA; $i++ ){
			$sumOfA        += $fa[$i];
			$sumOfSquareA  += pow($fa[$i], 2);
			$sumOfB        += $fb[$i];
			$sumOfSquareB  += pow($fb[$i], 2);
			$sumOfProducts += $fa[$i] * $fb[$i];
		}
		
		// Calculate Pearson
		$num = $sumOfProducts - ( ($sumOfA * $sumOfB) / $sA );
		$den = sqrt( ($sumOfSquareA - pow($sumOfA, 2) / $sA) * ($sumOfSquareB - pow($sumOfB, 2) / $sA) );

		$p = $den > 0 ? $num/$den : 0;

		return (float)$p;
	}
	
	public function build(){
		foreach( $this->getNodes() as $idxA => $A ){
			$vA = $this->_getVector($idxA, $this->getCorrelationData());
			
			foreach( $this->getNodes() as $idxB => $B ){
				// Do not compute self correlation
				if( $A == $B ){ continue; }
				
				$vB = $this->_getVector($idxB, $this->getCorrelationData());
				$w  = $this->getCorrelation($vA, $vB);
				
				if( $w > $this->getThreshold() ){ $this->addEdge( $this->_getPair($A, $B, $w) ); }
			}
		}
	}

}