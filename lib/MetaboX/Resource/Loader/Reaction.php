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
namespace MetaboX\Resource\Loader;

use MetaboX\Resource\Provider\Local as MXL;

class Reaction extends AbstractResourceLoader{
	
	/**
	 * This method tries to retrieve reaction '$rn' information
	 * from cached json file '_RESOURCE_DIR_ . $rn'.
	 * If chached file is not available, we didn't process this
	 * reaction before. We then download reaction information
	 * from external resource '_RESOURCE_URL_ . $rn'.
	 * We use KEGG database to retrieve information about input '$rn'
	 * and other helper methods are used to extract these information
	 * from a plain text file.
	 * 
	 * @param $rn string
	 * 
	 * @return object
	 */
	public function load( $plain = false ){
		$resource = $this->_getLocalRP()->read( $this->_getResourceFullPath() );
		if( $resource ){ return $resource; }
		
		$this->_plain = !is_null($this->_plain) ? $this->_plain : $this->_getRemoteRP()->read( $this->_getResourceFullUrl() );
		
		$eq = $this->_equationToArray();
		
		$ecs = $this->_getEnzyme();
		
		if( !is_array($ecs) ){ $ecs = array($ecs); }
		
		$resource = (object) array(
			'ID' 		 => $this->_resourceId,
			'name' 	     => $this->_extractAttributeByLabel('NAME'),
			'definition' => $this->_extractAttributeByLabel('DEFINITION'),
			'enzyme'     => $ecs,
			'equation' 	 => $eq['eq'],
			'reactants'  => $eq['reactants'],
			'products'   => $eq['products'],
			'organismIdCollection' => $this->_extractOrganism($ecs)
		);

		$this->_getLocalRP()->write($this->_getResourceFullPath(), $resource);
		$this->_resource = $resource;
		
		return $resource;
	}
	
	protected function _extractOrganism( $ecs ){
		$rp = new MXL\JSON($this->_config['directory']['resource_basepath'], $this->_config['directory']['enzyme']);
		$ecs = $rp->readList( $ecs );
		
		$orgs = array();
		foreach( $ecs as $ec ){
			$orgs = array_merge($orgs, $ec->organismIdCollection);
		}

		return $orgs;
	}
	
	protected function _extractId(){
		preg_match_all('/R[0-9]{5}/', $this->_plain, $matches);
		return $matches[0][0];
	}
	
	/**
	 * This method extracts all matching word after 'ENZYME' in
	 * plain text file. We further trim and clean the result from
	 * white spaces.
	 * 
	 * @return string
	 */
	protected function _getEnzyme(){
		preg_match_all('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $this->_plain, $matches);
		return array_unique($matches[0]);
	}
	
	/**
	 * We use a regular expression to retrieve reaction equation.
	 * We then sanitize and fix the extracted equation and explode
	 * it separating reactants from products.
	 * We save a list of reactants, a list of products and lists of
	 * their stoichiometric coefficients.
	 * 
	 * @return array
	 */
	protected function _equationToArray(){
		// NOTE: To prevent equation misinterpretation with DEFINITION
		// which in some cases can be equal to an equation but
		// containing other compound labels that we don't want
		// to include in the network construction.
		
		// $pattern = '/([0-9]*)\s(C[0-9]{5})\s(.)*=(.)*/';
		
		$pattern = '/EQUATION\s*([0-9]*)\s(C[0-9]{5})\s(.)*=(.)*/';
		preg_match($pattern, $this->_plain, $match);
		$eq = $match[0];
		
		$eq = str_replace('EQUATION', '', $eq);
		
		$eq = str_replace('&lt;', '<', $eq);
		$eq = str_replace('&gt;', '>', $eq);
		$eq = str_replace('(n+1)', '', $eq);
		$eq = trim($eq);
		
		$reactionOps = explode('<=>', $eq);
		$reactants   = explode('+', $reactionOps[0]);
		$products    = explode('+', $reactionOps[1]);
		
		list($reactants, $rSto) = $this->_sanitizeEqOperands($reactants);
		list($products, $pSto)  = $this->_sanitizeEqOperands($products);
		
		return array(
			'eq' => $eq,
			'reactants' => array(
				'compounds'    => $reactants,
				'coefficients' => $rSto
			),
			'products'  => array(
				'compounds'    => $products,
				'coefficients' => $pSto
			) 
		);	
	}

	/**
	 * It takes a list of 'coeff compound' and explodes it
	 * in order to return an array of compounds and an array
	 * of coefficients.
	 * 
	 * @param $items array
	 * 
	 * @return array
	 */
	protected function _sanitizeEqOperands( $items ){
		$_items = array();
		$sto = array();
		
		for( $i = 0; $i < count($items); $i++ ){
			
			$k = explode(' ', trim($items[$i]));
			
			if( count($k) > 1 ){
				$sto[]    = intval($k[0]);
				$_items[] = trim($this->_clean($k[1]));
			}else{
				$sto[]    = 1;
				$_items[] = trim($this->_clean($k[0]));
			}
		}
		
		return array($_items, $sto);
	}
	
	/**
	 * The input value is sanitized deleting
	 * some patterns found in reaction equations.
	 * We want to get rid of this patterns in order
	 * to extract compound and stoichiometric coefficient.
	 * 
	 * @param $value string
	 * 
	 * @return string
	 */
	protected function _clean($value){
		$k = str_replace('(n)', '', $value);
		$k = str_replace('(m)', '', $k);
		$k = str_replace('(n+m)', '', $k);
		$k = str_replace('(n+1)', '', $k);
		
		return $k;
	}
}
