<?php
/**
 * WikiPlugin Class
 * Wiki Search Engine for Fusionforge
 *
 * Copyright 2006 (c) Alain Peyrat
 *
 * This file is part of Fusionforge.
 *
 * Fusionforge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Fusionforge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Fusionforge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 */

require_once $GLOBALS['gfwww'].'search/include/engines/GroupSearchEngine.class.php';

class WikiSearchEngine extends SearchEngine {
	
	/**
	* name of the external site
	*
	* @var string $name
	*/
	var $rendererClassName;
	var $groupId;
	
	function WikiSearchEngine($type, $rendererClassName, $label, $groupId) {
		$this->groupId = $groupId;
		$this->rendererClassName = $rendererClassName;
		
		$this->SearchEngine($type, $rendererClassName, $label);
	}
	
	function isAvailable($parameters) {
		return true;
	}
	
	function getSearchRenderer($words, $offset, $exact, $parameters) {
		require_once($this->rendererClassName.'.class.php');
		$renderer = new $this->rendererClassName($words, $offset, $exact, 
			$this->groupId);
		return $renderer;
	}
}

?>
