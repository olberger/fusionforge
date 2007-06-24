<?php

/**
 * GForge Search Engine
 *
 * Copyright 2004 (c) Dominik Haas, GForge Team
 *
 * http://gforge.org
 *
 * @version $Id$
 */

require_once('pre.php');
require_once('www/search/include/renderers/HtmlGroupSearchRenderer.class.php');
require_once('www/search/include/renderers/ForumsHtmlSearchRenderer.class.php');
require_once('www/search/include/renderers/TrackersHtmlSearchRenderer.class.php');
require_once('www/search/include/renderers/TasksHtmlSearchRenderer.class.php');
require_once('www/search/include/renderers/DocsHtmlSearchRenderer.class.php');
require_once('www/search/include/renderers/FrsHtmlSearchRenderer.class.php');
require_once('www/search/include/renderers/NewsHtmlSearchRenderer.class.php');

class FullProjectHtmlSearchRenderer extends HtmlGroupSearchRenderer {
	
	/**
	 * group id
	 *
	 * @var int $groupId
	 */
	var $groupId;

	/**
	 * the words to search for
	 *
	 * @var string $words
	 */	
	var $words;

	/**
	 * flag to define whether the result must contain all words or only one of them
	 *
	 * @var boolean $isExact
	 */
	var $isExact;
	
	/**
	 * Constructor
	 *
	 * @param string $words words we are searching for
	 * @param int $offset offset
	 * @param boolean $isExact if we want to search for all the words or if only one matching the query is sufficient
	 * @param int $groupId group id
	 *
	 */
	function FullProjectHtmlSearchRenderer($words, $offset, $isExact, $groupId) {
		$this->groupId = $groupId;
		$this->words = $words;
		$this->isExact = $isExact;
		
		$this->HtmlGroupSearchRenderer(SEARCH__TYPE_IS_ADVANCED, $words, $isExact, $searchQuery, $groupId);
	}
	
	/**
	 * flush - overwrites the flush method from htmlrenderer
	 */
	function flush() {
		$this->writeBody();
		$this->writeFooter();
	}

	/**
	 * writeBody - write the Body of the output
	 */
	function writeBody() {
		site_project_header(array('title' => _('Advanced project search'), 'group' => $this->groupId, ));
		echo $this->getResult();
	}
	
	/**
	 * getResult - returns the Body of the output
	 * 
  	 * @return string result of all selected searches
	 */
	function getResult() {
		$html = '';
		
		$forumsRenderer		= new ForumsHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$trackersRenderer	= new TrackersHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$tasksRenderer		= new TasksHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$docsRenderer		= new DocsHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$frsRenderer 		= new FrsHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$newsRenderer		= new NewsHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		
		$validLength = (strlen($this->words) >= 3);

		if ($validLength || (is_numeric($this->words) && $trackersRenderer->searchQuery->implementsSearchById())) {
			$html .= $this->getPartResult($trackersRenderer, 'short_tracker');
		}

		if ($validLength || (is_numeric($this->words) && $forumsRenderer->searchQuery->implementsSearchById())) {
			$html .= $this->getPartResult($forumsRenderer, 'short_forum');
		}

		if ($validLength || (is_numeric($this->words) && $tasksRenderer->searchQuery->implementsSearchById())) {
			$html .= $this->getPartResult($tasksRenderer, 'short_pm');
		}

		if ($validLength || (is_numeric($this->words) && $docsRenderer->searchQuery->implementsSearchById())) {
			$html .= $this->getPartResult($docsRenderer, 'short_docman');
		}
		
		if ($validLength || (is_numeric($this->words) && $frsRenderer->searchQuery->implementsSearchById())) {
			$html .= $this->getPartResult($newsRenderer, 'short_files');
		}

		if ($validLength || (is_numeric($this->words) && $newsRenderer->searchQuery->implementsSearchById())) {
			$html .= $this->getPartResult($newsRenderer, 'short_news');
		}

/*		
		$renderer = new ForumsHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$html .= $this->getPartResult($renderer, 'short_forum');
		
		$renderer = new TrackersHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$html .= $this->getPartResult($renderer,  'short_tracker');
		
		$renderer = new TasksHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$html .= $this->getPartResult($renderer, 'short_pm');

		$renderer = new DocsHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$html .= $this->getPartResult($renderer, 'short_docman');

		$renderer = new FrsHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$html .= $this->getPartResult($renderer, 'short_files');
		
		$renderer = new NewsHtmlSearchRenderer($this->words, $this->offset, $this->isExact, $this->groupId);
		$html .= $this->getPartResult($renderer, 'short_news');
*/

		return $html.'<br />'; 
	}

	/**
	* getPartResult - returns the result of the given renderer 
	* 
  	* @return string result of the renderer
	*/			
	function getPartResult($renderer, $section) {
		$result = '';
		$renderer->searchQuery->executeQuery();
		
		$result .= '<h3><a name="'.$section.'"></a>'.sprintf(_('%1$s Search Result'), $Language->getText('group', $section)).'</h3>';
		
		if ($renderer->searchQuery->getRowsCount() > 0) {
			$result .= $GLOBALS['HTML']->listTabletop($renderer->tableHeaders);
			$result .= $renderer->getRows();			
			$result .= $GLOBALS['HTML']->listTableBottom();			
		} elseif(method_exists($renderer, 'getSections') && (count($renderer->getSections($this->groupId)) == 0)) {
			$result .= '<p>'.sprintf(_('No matches found - No sections available (check your permissions)'), htmlspecialchars($query['words'])).'</p>';
		} else {
			$result .= '<p>'.sprintf(_('No matches found'), htmlspecialchars($query['words'])).'</p>';
		}
		return $result;
	}

}

?>
