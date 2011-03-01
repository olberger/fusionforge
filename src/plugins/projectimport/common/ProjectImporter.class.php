<?php

/**
 * ProjectImporter Class
 *
 *
 * This file is part of FusionForge.
 *
 * FusionForge is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * FusionForge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GForge; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 */

// Include standard ARC library
include_once("arc/ARC2.php");
// Include the JSON RDF parser ala OSLC developped in COCLICO
include_once('ARC2_OSLCCoreRDFJSONParserPlugin.php');

#require_once $gfcommon.'import/import_users.php';

define('FORGEPLUCKER_NS', 'http://planetforge.org/ns/forgeplucker_dump/');

/**
 * TODO Enter description here ...
 * @author Olivier Berger
 *
 */
class ProjectImporter {
	
	/**
	 * Index of all triples imported
	 * @var ARC2 triples
	 */
	protected $index;
	
	protected $project_dump_res;
	
	/**
	 * Users descriptions found in the dump
	 * @var array of ARC2 resources (keys are URIs)
	 */
	protected $users;
	
	/**
	 * User names for the users found in the dump
	 * @var array of strings (keys are URIs)
	 */
	protected $user_names;
	
	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $providers;
	
	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	static $allowedprovidertypes = array('planetforge:TrackersTool',
					      'planetforge:ForumsTool', 
					      'planetforge:DocumentsTool', 
					      'planetforge:MailingListTool',  
					      'planetforge:TaskTool',
					      'planetforge:ScmTool',
					      'planetforge:NewsTool',
					      'planetforge:FilesReleasesTool',
						  'planetforge:SvnScmTool');
		
	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	static $ns = array(
			    'rdf'	=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
				'foaf' => 'http://xmlns.com/foaf/0.1/',
				'dcterms' => 'http://purl.org/dc/terms/',
				'oslc' => 'http://open-services.net/ns/core#',
				'oslc_cm' => 'http://open-services.net/ns/cm#',
				'forgeplucker' => FORGEPLUCKER_NS,
				'doap' => 'http://usefulinc.com/ns/doap#',
				'sioc' => 'http://rdfs.org/sioc/ns#',
				'planetforge' => 'http://coclico-project.org/ontology/planetforge#'
				);
	/**
	 * TODO Enter description here ...
	 * @param unknown_type $group_id
	 */
	function ProjectImporter($group_id) {
	  $this->group_id = $group_id;
	  $this->index = False;
	  
	  
	  $this->trackers = array();
	  $this->users = False;
	  $this->project_dump_res = False;
	  $this->user_names = array();
	  
	}

	/**
	 * Converts the JSON RDF ala OSLC to ARC2 triples
	 * @param string $json
	 * @return triples
	 */
	function parse_OSLCCoreRDFJSON($json)
	{
	  $conf = array('ns' => ProjectImporter::$ns);

	  // "load" the ARC2 plugin to parse RDF ala OSLC in JSON 
	  $parser = ARC2::getComponent("OSLCCoreRDFJSONParserPlugin", $conf);

/*
			$arr = json_decode($json, true);
			if ($arr) {
//				$feedback = "JSON decoded to :";
//				$message .= '<pre>'. nl2br(print_r($arr, true)) . '</pre>';
				$prefixes=false;
				$result=false;
				foreach($arr as $type => $tabType){
//					$message .= 'type: '.$type.'<br />';
//					$message .= '<pre>'. nl2br(print_r($tabType, true)) . '</pre>';

					if ($type=="users"){
						$users = $tabType;	
					}
					elseif($type=="roles"){
						$roles = $tabType;
					}
					elseif($type=="trackers"){
						$trackers = $tabType;
					}
					elseif($type=="docman"){
						$docman = $tabType;
					}
					elseif($type=="frs"){
						$frs = $tabType;
					}
					elseif($type=='forums'){
						$forums = $tabType;
					}
					elseif($type=='forgeplucker:trackers') {
						foreach($tabType as $bar)
						{
							$result = json_encode($bar);
						}
						break;
					}
					elseif($type=='prefixes') {
						$prefixes = $tabType;
					}
				}
				$result['prefixes']=$prefixes;
				$message .= '<pre>'. nl2br(print_r($arr, true)) . '</pre>';
				$arr = $parser->parseData($result);
*/
	  $parser->parseData($json);
	  $triples = $parser->getTriples();
//			echo 'triples :';
//			print_r($triples);
	  $this->index = ARC2::getSimpleIndex($triples, false);
	  return $triples;
	}

	/**
	 * Creates an ARC2 resource
	 * @param ARC2 triples index $index
	 * @param string $uri
	 * @return ARC2 resource
	 */
	static function make_resource($index, $uri) {
	  $conf = array('ns' => ProjectImporter::$ns);
	  $res = ARC2::getResource($conf);
	  $res->setIndex($index);
	  $res->setUri($uri);
	  return $res;
	}

	/**
	 * Returns a Dump object for the index, whose rdf:type is http://planetforge.org/ns/forgeplucker_dump/project_dump#
	 * @return ARC2 resource
	 */
	protected function project_dump() {
		if (! is_array($this->project_dump_res)) {
			$dumpres = array();

			$dumpresuri = False;
			foreach ($this->index as $uri => $resource) {
				$res = $this->make_resource($this->index, $uri);
				if ($res->hasPropValue('rdf:type', 'http://planetforge.org/ns/forgeplucker_dump/project_dump#')) {
					//	    if ($this->is_project_dump($resource)) {
					$dumpresuri = $uri;
					break;
				}
			}
			// found a dump resource
			if ($dumpresuri) {
				//	    $dumpres = $this->index[$dumpresuri];Enter description here ...
				$dumpres = $this->make_resource($this->index, $dumpresuri);
			}
			$this->project_dump_res = $dumpres;
		}
		return $this->project_dump_res;
	}
	
	function has_project_dump() {
		$result = False;
		$dumpres = $this->project_dump();
		if ($dumpres && count($dumpres)) {
			$result = True; 
		}
		return $result;
	}

	function get_user_name($user) {
		return $this->user_names[$user];
	}
	/**
	 * Return a user's email
	 * @param URI $user
	 */
	function get_user_email($user) {
		$res = $this->users[$user];
		return $res->getPropValue('sioc:email');
	}

	/**
	 * Extract users / persons from the dump
	 * @param unknown_type $dumpres
 	 * @return array of ARC2 resource
	 */
	function get_users() {
		if (! $this->users) {
			$dumpres = $this->project_dump();
			$this->users = array();
			// USERS
			$users = $dumpres->getPropValues('forgeplucker:users');
			foreach ($users as $user) {
				//	      print_r($this->index[$user]);
				$res = $this->make_resource($this->index, $user);
				$accountName = $res->getPropValue('foaf:accountName');
				$this->user_names[$user] = $accountName;
				$this->users[$user] = $res;
				//			print 'Found user : '. $accountName . "\n";
			}
			$persons = $dumpres->getPropValues('forgeplucker:persons');
			foreach ($persons as $person) {
				$res = $this->make_resource($this->index, $person);
				 
				//			print 'Found person : '. $res->getPropValue('foaf:name') . "\n";
				//	      print_r($this->index[$person]);
				//print_r($res->getProps());
				$accounts = $res->getPropValues('foaf:holdsAccount');
				foreach($accounts as $account) {
					//				print 'account : '.$account;
					$user = $this->users[$account];
					if (! $user->getPropValue('sioc:account_of')) {
						$user->setProp('sioc:account_of', $person);
					}
				}
			}
			/*		foreach ($this->users as $user) {
			 print 'this->user : ';
			 print_r($user->getProps());
			 }
			 */
		}
		return $this->users;
	}

	/**
	 * Returns a project's name
	 * @param ARC2 resource $projectres
	 */
	function get_project_name($projectres) {
		return $projectres->getPropValue('doap:name');
	}

	/**
	 * Return a project's description
	 * @param ARC2 resource $projectres
	 */
	function get_project_description($projectres) {
		return $projectres->getPropValue('dcterms:description');
	}

	/**
	 * Analyze the tools description found in the dump
	 */
	function get_tools() {
		global $feedback;

		$dumpres = $this->project_dump();
		
		// TOOLS
		
		$tools = $dumpres->getPropValues('forgeplucker:tools');
		//	    print_r($tools);
		$providers = array();
		foreach ($tools as $tool) {
			$toolres = $this->make_resource($this->index, $tool);
			//	      print_r($toolres->getProps()); echo "\n";
			$provider = $toolres->getPropValue('planetforge:provided_by');
			if ($provider) {
				$providerres = $this->make_resource($this->index, $provider);
				$types = $providerres->getPropValues('rdf:type');
				foreach ($types as $type) {
					if (!in_array($type, ProjectImporter::$allowedprovidertypes)) {
						$feedback .= 'Need provider type : '. $type . "\n";
					}
					else {
						if (!in_array($provider, $providers)) {
							$providers[] = $provider;
						}
					}
				}
			}
		}
		//	    echo 'PROVIDERS';
		//	    print_r($providers); echo "\n";
		$this->providers = $providers;
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $dumpres
	 * @return Ambigous <multitype:, ARC2>
	 */
	function get_projects() {
		global $feedback;

		$dumpres = $this->project_dump();
		
		$results = array();

	    
		$this->get_users($dumpres);

		// PROJECT
		//	    $projects = $this->dump_project_uris($dumpres);
		$projects = $dumpres->getPropValues('forgeplucker:project');

		foreach ($projects as $project) {
			//	      print 'Found project : '. $project . "\n";
			//	      print_r($this->index[$project]);
			$res = $this->make_resource($this->index, $project);
	      
			//	      print_r($res->getProps());
			$name = $res->getPropValue('doap:name');
			$description = $res->getPropValue('dcterms:description');
			$homepage = $res->getPropValue('doap:homepage');
			$hosted_by = $res->getPropValue('planetforge:hosted_by');
			//	      print 'Project: '. $name . ' - '. $description . ' ('. $homepage . ') hosted on: '. $hosted_by;
//			$results[] = array($name, $description, $homepage, $hosted_by);
			$results[] = $res;
			// handle project's roles
			$users_roles=array();
			$roles = $res->getPropValues('sioc:scope_of');
			foreach($roles as $role) {
				$roleres = $this->make_resource($this->index, $role);
				$name = $roleres->getPropValue('sioc:name');
				$users = $roleres->getPropValues('sioc:function_of');
				//		print_r($name);
				//print_r($users);
				foreach($users as $user) {
					$users_roles[$this->user_names[$user]] = array('role' => $name);
				}
			}
			//	      print_r($users_roles);
//			echo "creating roles of existing users in the project\n";
//			echo "calling user_fill(".'$users'.", $this->group_id)\nwhere ".'$users'." is";
			// check user_fill : True == check mode
//			user_fill($users_roles, $this->group_id, True);
//			print_r($users_roles);
//			echo "\n";

		}

		return $results;
	}

	// handle project's spaces

	function decode_space($space, $spaceres) {
		$types = $spaceres->getPropValues('rdf:type');
		$supported_type = False;
		foreach($types as $type) {

			// Case of the trackers
			if ($type == 'http://coclico-project.org/ontology/planetforge#Tracker') {
				$supported_type = True;
				//		      print 'Found tracker :'. $space . "\n";
				$tracker = array('uri' => $space);
				// Decode TRACKER
				$artifacts = $spaceres->getPropValues('oslc:results');
				$tracker['artifacts'] = array();
				foreach ($artifacts as $artifact) {
					// Decode ARTIFACTS
					//			print 'Found tracker artifact :'. $artifact . "\n";
					$cmres = $this->make_resource($this->index, $artifact);
					$tracker['artifacts'][] = array('uri' => $artifact,
									'details' => $cmres->getProps());
				}
//				$this->trackers[] = $tracker;
				echo '<pre>'. htmlspecialchars(print_r($tracker, True)) . '</pre>';
				break;
			}
			// other cases 
			//  ...
		}
	}

	/**
	 * Return the spaces used by a project
	 * @param ARC2 resource $projectres
	 * @return array of ARC2 resources
	 */
	function project_get_spaces($projectres) {
		global $feedback;

		$results = array();

		$spaces = $projectres->getPropValues('sioc:has_space');
		foreach ($spaces as $space) {
			$spaceres = $this->make_resource($this->index, $space);
			$provider = $spaceres->getPropValue('planetforge:provided_by');
			if (!in_array($provider, $this->providers)) {
				$feedback .= 'error : no supported provider for '. $space .': '. $provider."!\n";
			}
			else {
				$results[$space] = $spaceres;
			}
		}
		return $results;
	}

}
// Local Variables:
// mode: php
// c-file-style: "bsd"
// End:

