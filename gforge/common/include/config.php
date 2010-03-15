<?php
/**
 * FusionForge configuration functions
 *
 * Copyright 2009, Roland Mas
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

class FusionForgeConfig {
	static private $instance = NULL ;
	private $settings ;
    
	public function get_instance () {
		if (self::$instance == NULL) {
			self::$instance = new FusionForgeConfig () ;
		}
		return self::$instance ;
	}
  
	public function get_value ($section, $var) {
		if (!isset (self::$instance->settings[$section])
		    || !isset (self::$instance->settings[$section][$var])) {
			return NULL ;
		}
		return self::$instance->settings[$section][$var] ;
	}

	public function set_value ($section, $var, $value) {
		if (!isset (self::$instance->settings[$section])) {
			self::$instance->settings[$section] = array () ;
		}

		if (!isset (self::$instance->settings[$section][$var])) {
			self::$instance->settings[$section][$var] = $value ;
		}
	}

	function read_config_file ($file) {
		$sections = parse_ini_file ($file, true) ;
		foreach ($sections as $section => $options) {
			if (!isset (self::$instance->settings[$section]))
				continue ;
			foreach ($options as $var => $value) {
				if (!isset (self::$instance->settings[$section][$var]))
					continue ;
				self::$instance->settings[$section][$var] = $value ;
			}
		}
		return ;
	}

  }

if (!isset ($fusionforge_config)) {
	$fusionforge_config = new FusionForgeConfig () ;
}

function fusionforge_get_config ($var, $section = 'core') {
	$c = FusionForgeConfig::get_instance () ;

	return $c->get_value ($section, $var) ;
}

function fusionforge_get_config_array ($arr) {
	$c = FusionForgeConfig::get_instance () ;

	$ret = array () ;

	foreach ($arr as $item) {
		$var = $arr[0] ;
		if (isset ($arr[1])) {
			$section = $arr[1] ;
		} else {
			$section = 'core' ;
		}
		$ret[] = $c->get_value ($section, $var) ;
	}

	return $ret ;
}

function fusionforge_define_config_item ($var, $section, $default) {
	$c = FusionForgeConfig::get_instance () ;

	return $c->set_value ($section, $var, $default) ;
}

function fusionforge_read_config_file ($file) {
	$c = FusionForgeConfig::get_instance () ;

	return $c->read_config_file ($file) ;
}

// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

?>
