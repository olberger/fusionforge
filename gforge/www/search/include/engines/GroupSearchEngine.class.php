<?php
/**
 * GForge Search Engine
 *
 * Copyright 2004 (c) Guillaume Smet
 *
 * http://gforge.org
 *
 * @version $Id$
 */

require_once('www/search/include/engines/SearchEngine.class.php');

class GroupSearchEngine extends SearchEngine {
	var $Group;
	
	function GroupSearchEngine($type, $rendererClassName, $label) {
		$this->SearchEngine($type, $rendererClassName, $label);
	}
	
	function isAvailable($parameters) {
		if(isset($parameters[SEARCH__PARAMETER_GROUP_ID]) && $parameters[SEARCH__PARAMETER_GROUP_ID]) {
			$Group =& group_get_object($parameters[SEARCH__PARAMETER_GROUP_ID]);
			if($Group && is_object($Group) && !$Group->isError()) {
				$this->Group =& $Group;
				return true;
			}
		}
		return false;
	}
	
	function & getSearchRenderer($words, $offset, $exact, $parameters) {
		$this->includeSearchRenderer();
		$rendererClassName = $this->rendererClassName;
		$renderer = new $rendererClassName($words, $offset, $exact, $parameters[SEARCH__PARAMETER_GROUP_ID]);
		return $renderer;
	}
}

?>
