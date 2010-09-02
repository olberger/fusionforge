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

global $gfplugins;
require_once $gfplugins.'wiki/common/WikiSearchEngine.class.php';

class GforgeWikiPlugin extends Plugin {
	function GforgeWikiPlugin () {
		$this->Plugin() ;
		$this->name = "wiki" ;
		$this->text = "Wiki" ; // To show in the tabs, use...
//		$this->hooks[] = "user_personal_links"; //to make a link to the user's personal wiki
//		$this->hooks[] = "usermenu" ;
		$this->hooks[] = "groupmenu";
		$this->hooks[] = "groupisactivecheckbox" ; // The "use ..." checkbox in editgroupinfo
		$this->hooks[] = "groupisactivecheckboxpost" ; // 
		$this->hooks[] = "project_admin_plugins"; // to show up in the project admin page
//		$this->hooks[] = "userisactivecheckbox" ; // The "use ..." checkbox in user account
//		$this->hooks[] = "userisactivecheckboxpost" ; // 
		$this->hooks[] = 'search_engines';
		$this->hooks[] = 'full_search_engines';
		$this->hooks[] = 'cssfile';
		$this->hooks[] = 'activity';
	}

	function CallHook ($hookname, & $params) {
		global $G_SESSION,$HTML;
		if (is_array($params) && isset($params['group']))
			$group_id=$params['group'];
		$use_wikiplugin = getIntFromRequest('use_wikiplugin');
		if ($hookname == "usermenu") {
			$text = $this->text;
			if ( ($G_SESSION) && ($G_SESSION->usesPlugin("wiki")) ) {
				$param = '?id=' . $G_SESSION->getId() . '&type=u';
				echo ' | ' . $HTML->PrintSubMenu (array ($text),
						  array ('/wiki/u/'. $user_name.'/HomePage' ));
			} else {
				$this->hooks["usermenu"] = "" ;
				//$param = "?off=true";
			}
			
		} elseif ($hookname == "groupmenu") {
			$project = &group_get_object($group_id);
			if (!$project || !is_object($project))
				return;
			if ($project->isError())
				return;
			if (!$project->isProject())
				return;
			if ( $project->usesPlugin ( $this->name ) ) {
				$params['TITLES'][]=$this->text;
				$params['DIRS'][]='/wiki/g/'.$project->getUnixName().'/HomePage';
			} else {
				$this->hooks["groupmenu"] = "";
				//$params['TITLES'][]=$this->text." [Off]";
				//$params['DIRS'][]='/plugins/wiki/index.php?off=true';
			}
							
			(($params['toptab'] == $this->name) ? $params['selected']=(count($params['TITLES'])-1) : '' );
		} elseif ($hookname == "groupisactivecheckbox") {
                        //Check if the group is active
			$group = &group_get_object($group_id);
			echo "<tr>";
			echo "<td>";
			echo ' <input type="checkbox" name="use_wikiplugin" value="1" ';
			// checked or unchecked?
			if ( $group->usesPlugin ( $this->name ) ) {
				echo "checked=\"checked\"";
                            }
			echo " /><br/>";
			echo "</td>";
			echo "<td>";
			echo "<strong>Use ".$this->text." Plugin</strong>";
			echo "</td>";
			echo "</tr>";
		} elseif ($hookname == "groupisactivecheckboxpost") {
		        $group = &group_get_object($group_id);
			if ( $use_wikiplugin == 1 ) {
				$group->setPluginUse ( $this->name );
			} else {
				$group->setPluginUse ( $this->name, false );
			}
                } elseif ($hookname == "project_admin_plugins") {
                        // this displays the link in the project admin options page to its administration page.
                        $group_id = $params['group_id'];
                        $group = &group_get_object($group_id);
                        if ( $group->usesPlugin ( $this->name ) ) {
                                echo '<p><a href="/plugins/wiki/wikiadmin.php?id=' . $group->getID() . '&amp;type=admin&amp;pluginname=' . $this->name . '">' . _('Wiki Admin') . '</a></p>';
                        }
		} elseif ($hookname == "userisactivecheckbox") {
			//check if user is active
			$user = $params['user'];
			echo "<tr>";
			echo "<td>";
			echo ' <input type="checkbox" name="use_wikiplugin" value="1" ';
			// checked or unchecked?
			if ( $user->usesPlugin ( $this->name ) ) {
				echo "checked=\"checked\"";
                            }

			echo " />    Use ".$this->text." Plugin";
			echo "</td>";
			echo "</tr>";
		} elseif ($hookname == "userisactivecheckboxpost") {
			$user = $params['user'];
			if ( getIntFromRequest('use_wikiplugin') == 1 ) {
				$user->setPluginUse ( $this->name );
			} else {
				$user->setPluginUse ( $this->name, false );
			}
			echo "<tr>";
			echo "<td>";
			echo ' <input type="checkbox" name="use_wikiplugin" value="1" ';
			// checked or unchecked?
			if ( $user->usesPlugin ( $this->name ) ) {
				echo "checked=\"checked\"";
                            }

			echo " />    Use ".$this->text." Plugin";
			echo "</td>";
			echo "</tr>";
		} elseif ($hookname == "user_personal_links") {
			$userid = $params['user_id'];
			$user = user_get_object($userid);
			$text = $params['text'];
			//check if the user has the plugin activated
			if ($user->usesPlugin($this->name)) {
				echo '	<p>
					<a href="/plugins/wiki/index.php?id=' . $userid . '&type=u">' . _("View Personal Wiki") .'</a></p>';
			}
		} elseif ($hookname == 'search_engines') {
			// FIXME: when the hook is called, the group_id is not set.
			// So I use the global variable instead.
			$group_id = $GLOBALS['group_id'];
			if ($group_id) {
				$group = group_get_object($group_id);
				if (!$group || !is_object($group)) {
					return;
				}
				if ($group->usesPlugin('wiki')) {
					$searchManager = $params['object'];
					$searchManager->addSearchEngine(
						SEARCH__TYPE_IS_WIKI,
						new WikiSearchEngine(SEARCH__TYPE_IS_WIKI,
								'WikiHtmlSearchRenderer', 
						_("This projects's wiki"), $group_id)
					);
				}
			}
		} elseif ($hookname == 'full_search_engines') {
			// FIXME: when the hook is called, the group_id is not set.
			// So I use the global variable instead.
			$group_id = $GLOBALS['group_id'];
			$group = &group_get_object($group_id);
			if ($group->usesPlugin ( $this->name)) {
				require_once('plugins/wiki/common/WikiHtmlSearchRenderer.class.php');
				$wikiRenderer = new WikiHtmlSearchRenderer($params->words, $params->offset, $params->isExact, $params->groupId);
				$validLength = (strlen($params->words) >= 3);
				if ($validLength || (is_numeric($params->words) && $wikiRenderer->searchQuery->implementsSearchById())) {
					$html = $params->getPartResult($wikiRenderer, 'short_wiki', 'Wiki');
					return $html;
				}
			}
		} elseif ($hookname == 'cssfile') {
			if (strncmp(preg_replace('/^\/+/', '/', $_SERVER['REQUEST_URI']), '/wiki/', 6) == 0) {
				echo '<link rel="alternate" type="application/x-wiki" title="Edit this page!" href="'.$_SERVER['PHP_SELF'].'?action=edit" />';
				echo '<link rel="stylesheet" type="text/css" href="/wiki/themes/fusionforge/fusionforge.css" />';
				echo "\n".'<link rel="alternate stylesheet" type="text/css" href="/wiki/themes/fusionforge/fusionforge-fullscreen.css" media="screen" title="Fullscreen" />';
				echo "\n".'<link rel="alternate stylesheet" type="text/css" href="/wiki/themes/fusionforge/fusionforge-autonumbering.css" title="Autonumbering" />';
				echo "\n".'<link rel="alternate stylesheet" type="text/css" href="/wiki/themes/fusionforge/fusionforge-rereading.css" title="Rereading Mode" />';
				echo "\n".'<link rel="stylesheet" type="text/css" href="/wiki/themes/fusionforge/fusionforge-print.css" media="print" />';
				echo "\n".'<base href="'.PHPWIKI_BASE_URL.'" />';
				echo "\n";
			}
		} elseif ($hookname == 'activity') {
			$group = &group_get_object($group_id);
			if ($group->usesPlugin ( $this->name)) {
				// Add activities from the wiki plugin if active.
				// Only major edits are included.
				$params['ids'][] = 'wiki';
				$params['texts'][] = $this->text;

				if (count($params['show']) < 1 || array_search('wiki',$params['show']) !== false) {

					$pat = '_g'.$group_id.'_';
					$len = strlen($pat)+1;
					$wres = db_query_params ('SELECT plugin_wiki_page.id AS id,
							substring(plugin_wiki_page.pagename from $1) AS pagename,
							plugin_wiki_version.version AS version, 
							plugin_wiki_version.mtime AS activity_date, 
							plugin_wiki_version.minor_edit AS minor_edit,
							plugin_wiki_version.versiondata AS versiondata
						FROM plugin_wiki_page, plugin_wiki_version 
						WHERE plugin_wiki_page.id=plugin_wiki_version.id 
							AND mtime BETWEEN $2 AND $3
							AND minor_edit=0
							AND substring(plugin_wiki_page.pagename from 0 for $1) = $4
						ORDER BY mtime DESC',
                                                                 array ($len,
                                                                        $params['begin'],
                                                                        $params['end'],
                                                                        $pat));

					$cache = array();
					while ($arr = db_fetch_array($wres)) {
						$group_name = $group->getUnixName();
						$data = unserialize($arr['versiondata']);
						if (!isset($cache[$data['author']])) {
							$r = db_query_params ('SELECT user_name FROM users WHERE realname = $1',
										array ($data['author']));

							if ($a = db_fetch_array($r)) {
								$cache[$data['author']] = $a['user_name'];
							} else {
								$cache[$data['author']] = '';
							}
						}
						$arr['user_name'] = $cache[$data['author']];
						$arr['realname'] = $data['author'];
						$arr['icon']=html_image("ic/wiki20g.png","20","20",array("alt"=>"Wiki"));
						$arr['title'] = 'Wiki Page '.$arr['pagename'];
						$arr['link'] = '/wiki/g/'.$group_name.'/'.urlencode($arr['pagename']);
						$arr['description']= $arr['title'];
						$params['results'][] = $arr;
					}
				}
			}
		}		
	}
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
