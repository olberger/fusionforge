<?php

require_once $gfconfig.'plugins/sympa/config.php';


class sympaSoap  extends Error {


/*
 *  Constructor.
 *
 */

  function sympaSoap($useremail){
    global  $sympa_urlsoapwsdl,$sympa_appname,$sympa_apppassword,$sympa_listsurl,$sympa_template;
    $this->Error();
    $this->wsdl = $sympa_urlsoapwsdl;
    $this->appname = $sympa_appname;
    $this->apppwd = $sympa_apppassword;
    $this->listusrl = $sympa_listsurl;
    $this->template = $sympa_template;
    $this->useremail = $useremail;
    
try{
       $this->client = @new SoapClient($this->wsdl);
   }catch(SoapFault $f){
      $this->setError(_('Soap::ERROR'));
      return false;
    }
    return true;
  }
  
  function noexist($listname){
    try{ 
      $this->client->authenticateRemoteAppAndRun($this->appname, $this->apppwd, 'USER_EMAIL='.$this->useremail,'info',array($listname));
    }catch(SoapFault $f){
      return true;
    }
    
    $this->setError(_("list already exists"));
    return false;
  }
  
  function delete($listname){
   try{ 

     $this->client->authenticateRemoteAppAndRun($this->appname, $this->apppwd, 'USER_EMAIL='.$this->useremail,'closeList',array($listname));
    }catch(SoapFault $f){
  
      $this->setError(_("Error: list cannot be closed"));
      return false;
    }
    
    return true;

  }
  
  function create($listname,$description,$groupId,$listRoles){
     try{
$subject='Liste Projet Sourcesup __SSUPSEP__'.$groupId.'__SSUPSEP__'.$listRoles;      
$this->client->authenticateRemoteAppAndRun($this->appname, $this->apppwd, 'USER_EMAIL='.$this->useremail,'createList',array($listname,$subject,$this->template,$description,'sourcesup'));
    }catch(SoapFault $f){
      $this->setError(_("Error: list cannot be created"));
      return false;
    }
    
    return true;

    
  }
  
}

?>
