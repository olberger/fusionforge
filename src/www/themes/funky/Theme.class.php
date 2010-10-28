<?php
/**
 * FusionForge Funky Theme
 *
 * Copyright 2010, Antoine Mercadal - Capgemini
 *
 * This file is part of FusionForge.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 * 
 * FusionForge is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FusionForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 */


require_once $gfwww.'include/Layout.class.php';

define('TOP_TAB_HEIGHT', 30);
define('BOTTOM_TAB_HEIGHT', 22);

class Theme extends Layout {

    function Theme() 
    {
        // Parent constructor
        $this->Layout();
        $this->themeurl = util_make_url('themes/funky/');
        $this->imgbaseurl = $this->themeurl . 'images/';
        $this->imgroot = $this->imgbaseurl;
        $this->doctype = 'strict';
        $this->cssbaseurl = $this->themeurl . 'css/';
        $this->cssurls = array(
            util_make_url ('/themes/css/fusionforge.css'),
            $this->cssbaseurl .'theme.css',
            $this->cssbaseurl .'theme-pages.css');
    }
    
    
    function quicknewsbutton()
    {
        return "<div class='quicknews-toggle'><a href=# onclick='jQuery(\".quicknews\").slideToggle()'>news</a></div>";
    }
    
    function quicknews()
    {
        $ret = "<div class='quicknews'>";
        $ret .= "<ul>";
        $ret .= "<li><h1>news de ouf</h1>hello world</li>";
        $ret .= "<li><h1>news de ouf</h1>hello world</li>";
        $ret .= "<li><h1>news de ouf</h1>hello world</li>";
        $ret .= "<li><h1>news de ouf</h1>hello world</li>";
        $ret .= "</ul>";
        $ret .= "</div>";
        return $ret;
    }

    function bodyHeader($params)
    {
        global $user_guide, $HTML;
        echo '<table id="header" class="width-100p100">';
        echo '<tr>';
        echo '<td id="header-col1">';
        echo '<h1>'.  util_make_link ('/', html_image('/header/top-logo.png',null,null,array('alt'=>'FusionForge Home'))) .'</h1>';
        echo '</td>';
        echo '<td id="header-col2">';
        
        $items = $this->navigation->getUserLinks();
        for ($j = 0; $j < count($items['titles']); $j++) 
        {
            $links[] = util_make_link($items['urls'][$j], $items['titles'][$j], array('class'=>'userlink'), true);
        }
        echo implode(' | ', $links);
        plugin_hook ('headermenu', $params);
        
        echo '</td><tr><td colspan=2 id="header-line2">';
        // echo $this->quicknewsbutton();
        $this->quickNav();
        $this->searchBox();
        
        echo '</td></tr>';
        echo '</td><tr><td colspan=2 id="header-news">';
        //echo $this->quicknews();
        echo'</td></tr></table><!-- outer tabs -->';
        echo $this->outerTabs($params);
        echo '<!-- inner tabs -->';
        echo '<div class="innertabs">';
        if (isset($params['group']) && $params['group'])
            echo $this->projectTabs($params['toptab'],$params['group']);
            
        echo '</div>';
        echo '<div id="maindiv">';
    }
        
    function bodyFooter($params) {
        echo '</div><!-- id="maindiv" -->';
    }
        
    function footer($params)
    {
        $this->bodyFooter($params);
        echo '<div class="footer">';
        // echo '<div>Theme designed by Antoine Mercadal. Copyright &copy; 2010 Capgemini';
        echo $this->navigation->getPoweredBy();
        echo $this->navigation->getShowSource();
        echo '<div style="clear:both"></div></body></html>';
    }

    /**
     * boxTop() - Top HTML box
     *
     * @param   string  Box title
     * @param   bool    Whether to echo or return the results
     * @param   string  The box background color
     */
    function boxTop($title, $id = '') 
    {
        $t_result = '';
        $t_result .= '<div id="' . $this->toSlug($id) . '" class="box-surround">';
        $t_result .= '<div id="'. $this->toSlug($id) . '-title" class="box-title">';
        $t_result .= '<div class="box-title-content"  id="'. $this->toSlug($id) .'-title-content">'. $title .'</div>';
        $t_result .= '</div> <!-- class="box-title" -->';
        
        return $t_result;
    }

    /**
     * boxMiddle() - Middle HTML box
     *
     * @param   string  Box title
     * @param   string  The box background color
     */
    function boxMiddle($title, $id = '')
    {
        $t_result ='<div id="title-'. $this->toSlug($id).'" class="box-middle">'.$title.'</div>';
        
        return $t_result;
    }
    
    /**
     * boxContent() - Content HTML box
     *
     * @param   string  Box content
     */
    function boxContent($content, $id = '')
    {
        $t_result ='<div id="'. $this->toSlug($id) .'-content" class="box-content">'.$content.'</div>';
        return $t_result;
    }

    /**
     * boxBottom() - Bottom HTML box
     *
     */
    function boxBottom() 
    {
        $t_result='</div><!-- class="box-surround" -->';
        
        return $t_result;
    }

    /**
     * boxGetAltRowStyle() - Get an alternating row style for tables
     *
     * @param               int             Row number
     */
    function boxGetAltRowStyle($i) 
    {
        if ($i % 2 == 0)
            return 'class="bgcolor-white"';
        else
            return 'class="bgcolor-grey"';
    }

    /**
     * listTableTop() - Takes an array of titles and builds the first row of a new table.
     *
     * @param       array   The array of titles
     * @param       array   The array of title links
     * @param       boolean Whether to highlight or not the entry
     */
    function listTableTop ($title_arr, $links_arr=false, $selected=false)
    {
        $return = '<table class="width-100p100 listTable';
        if ($selected == true)
            $return .= ' selected';
        $return .= '"><tr>';
        
        $count=count($title_arr);
        
        if ($links_arr)
            for ($i=0; $i<$count; $i++)
            {
                $return .= '<th scope="col"><a class="sortbutton" href="';
                $return .=util_make_url ($links_arr[$i]);
                $return .= '"><strong>'.$title_arr[$i].'</strong></a></th>';
            }
        else
            for ($i=0; $i<$count; $i++)
                $return .= '<th scope="col"><strong>'.$title_arr[$i].'</strong></th>';
        
        return $return.'</tr>';
    }

    function listTableBottom()
    {
        return '</table>';
    }


    function tabGenerator($TABS_DIRS, $TABS_TITLES, $nested=false,  $selected=false, $sel_tab_bgcolor='WHITE',  $total_width='100%')
    {
        $count=count($TABS_DIRS);
        
        if ($count < 1)
            return;
        
        $return = '<!-- start tabs -->';
        $return .= '<table class="tabGenerator width-100p100" summary="" ';
        
        if ($total_width != '100%')
            $return .= 'style="width:' . $total_width . ';"';
        
        $return .= ">\n";
        $return .= '<tr>';
        
        $accumulated_width = 0;
        
        for ($i=0; $i<$count; $i++)
        {
            $tabwidth = intval(ceil(($i+1)*100/$count)) - $accumulated_width ;
            $accumulated_width += $tabwidth ;

            $return .= "\n";

            // middle part
            $return .= '<td class="tg-middle" style="width:'.$tabwidth.'%;"><a href="'.$TABS_DIRS[$i].'">' . "\n";
            $return .= '<div';
            
            if ($selected == $i)
                $return .= ' class="selected"';
            
            $return .= '>';
            $return .= '<div';
            
            if ($nested)
                $return .= ' class="nested"';
            
            $return .= '>' . "\n";
            $return .= ''.$TABS_TITLES[$i].'' . "\n";
            $return .= '</div>';
            $return .= '</div>' . "\n";
            $return .= '</a></td>' . "\n";
            
        }
        
        $return .= '</tr></table><!-- end tabs -->';
        
        return $return;
    }


    /**
     * beginSubMenu() - Opening a submenu.
     *
     * @return    string    Html to start a submenu.
     */
    function beginSubMenu () 
    {
        $return = '<ul class="submenu">';
        
        return $return;
    }

    /**
     * endSubMenu() - Closing a submenu.
     *
     * @return    string    Html to end a submenu.
     */
    function endSubMenu ()
    {
        $return = '</ul>';
        
        return $return;
    }

    /**
     * printSubMenu() - Takes two array of titles and links and builds the contents of a menu.
     *
     * @param       array   The array of titles.
     * @param       array   The array of title links.
     * @return    string    Html to build a submenu.
     */
    function printSubMenu ($title_arr,$links_arr) 
    {
        $count  = count($title_arr) - 1;
        $return = '';

        for ($i=0; $i<$count; $i++)
            $return .= "<li><span>" . util_make_link ($links_arr[$i], $title_arr[$i]) . "</span></li>";
        
        $return .= "<li><span>" . util_make_link ($links_arr[$i], $title_arr[$i]) . "</span></li>";
        
        return $return;
    }

    /**
     * subMenu() - Takes two array of titles and links and build a menu.
     *
     * @param       array   The array of titles.
     * @param       array   The array of title links.
     * @return    string    Html to build a submenu.
     */
    function subMenu ($title_arr,$links_arr) 
    {
        $return  = $this->beginSubMenu () ;
        $return .= $this->printSubMenu ($title_arr,$links_arr) ;
        $return .= $this->endSubMenu () ;
    
        return $return;
    }

    /**
     * multiTableRow() - create a mutlilevel row in a table
     *
     * @param    string    the row attributes
     * @param    array    the array of cell data, each element is an array,
     *                      the first item being the text,
     *                    the subsequent items are attributes (dont include
     *                    the bgcolor for the title here, that will be
     *                    handled by $istitle
     * @param    boolean is this row part of the title ?
     *
     */
    function multiTableRow($row_attr, $cell_data, $istitle) 
    {
        $return= '<tr class="ff" '.$row_attr;
        
        if ( $istitle )
            $return .=' align="center"';
        
        $return .= '>';
        
        for ( $c = 0; $c < count($cell_data); $c++ )
        {
            $return .='<td class="ff" ';
            
            for ( $a=1; $a < count($cell_data[$c]); $a++)
                $return .= $cell_data[$c][$a].' ';
                
            $return .= '>';
            
            if ( $istitle )
                $return .='<strong>';
            
            $return .= $cell_data[$c][0];
            
            if ( $istitle )
                $return .='</strong>';
            
            $return .= '</td>';
            
        }
        $return .= '</tr>';
        
        return $return;
    }

    /**
     * getThemeIdFromName()
     *
     * @param    string  the dirname of the theme
     * @return    integer the theme id
     */
    function getThemeIdFromName($dirname)
    {
        $res = db_query_params ('SELECT theme_id FROM themes WHERE dirname=$1', array($dirname));
        
        return db_result($res,0,'theme_id');
    }
    
    /**
     * headerJS() - creates the JS headers and calls the plugin javascript hook
     * @todo generalize this
     */
    function headerJS()
    {
		use_javascript('/scripts/jquery/jquery-1.4.2.min.js');

        echo '<script type="text/javascript" src="'. util_make_uri('/js/common.js') .'"></script>';
        echo '<script type="text/javascript" src="/scripts/codendi/LayoutManager.js"></script>';
        echo '<script type="text/javascript" src="/scripts/codendi/ReorderColumns.js"></script>';
        echo '<script type="text/javascript" src="/scripts/codendi/codendi-1236793993.js"></script>';
        echo '<script type="text/javascript" src="/scripts/codendi/validate.js"></script>';
        echo '<script type="text/javascript" src="/scripts/codendi/Tooltip.js"></script>';
        
        ?>
        <script>
            var JQuery = jQuery.noConflict();
            JQuery(window).load(function(){
                    jQuery(".quicknews").hide();
            });
        </script>
        <?php
    
        echo '<script type="text/javascript">';
        plugin_hook ("javascript",false);
        echo '</script>'."\n";
        
        plugin_hook ("javascript_file",false);
		echo $this->getJavascripts();
    }
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
