<?php
/**
* Alarm server 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 10:02:34 [Feb 26, 2023])
*/
//
//
class alarm_server extends module {
/**
* alarm_server
*
* Module class constructor
*
* @access private
*/
function __construct() {
  $this->name="alarm_server";
  $this->title="Alarm server";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();

 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
  //DebMes ("tab=".$this->tab." view_mode=".$this->view_mode." ID=".$this->id);  для отладки
  
  //if ($this->data_source=='alarm_server_camera' || $this->data_source=='') { зачем не знаю, это было
//-------------Работает---------------------------
// поиск записей о камерах и вывод в таблицу

	if ($this->tab=='cam') {
	$this->search_alarm_server_camera($out);
  }
  
//добавление новой камеры если id не пустой  
  if ($this->view_mode=='add_cam') {
   $this->edit_alarm_server_camera($out, $this->id);   
  }  

//если id пустой то редактирование текущей  
  if ($this->view_mode=='edit_cam') {
   $this->edit_alarm_server_camera($out, $this->id);  
  }

// удаление записи об выбранной камере
   if ($this->view_mode=='delete_camera') {
	   //DebMes ("SELECT * FROM alarm_server WHERE ID=$this->id");
	   SQLExec("DELETE FROM alarm_server_camera WHERE ID='".(int)$this->id."'");
	   $this->redirect("?tab=cam");
  }

//--------------не трогать------------------------  


 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 
//----------------- работает ---------------------------
// поиск записей о событиях и вывод в таблицу
	if ($this->tab=='' || $this->tab=='event') {  
		$this->search_alarm_server_event($out);
	}

// удаление записи одного события
	if ($this->view_mode=='delete_event') {
		//DebMes ("SELECT * FROM alarm_server WHERE ID=$this->id");
		SQLExec("DELETE FROM alarm_server WHERE ID='".(int)$this->id."'");
		$this->redirect("?");
	}
// удаление всех записей из событий	
	if ($this->view_mode=='delete_all'){
	   SQLExec("DELETE FROM alarm_server WHERE 1");
	   $this->redirect("?");
	}		
//--------------- не трогать ---------------------------

}
/**
* FrontEnd
*
* Module frontend
*
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* alarm_server_camera search
*
*/
 function search_alarm_server_camera(&$out) {
  require(dirname(__FILE__).'/alarm_server_camera_search.inc.php');
 }
/**
* alarm_server_camera edit/add
*
*/
 function edit_alarm_server_camera(&$out, $id) {
  require(dirname(__FILE__).'/alarm_server_camera_edit.inc.php');
 }
/**
* alarm_server search
*/
 function search_alarm_server_event(&$out) {
  require(dirname(__FILE__).'/alarm_server_event_search.inc.php');
 }
 
 
 function propertySetHandle($object, $property, $value) {
  $this->getConfig();
   $table='alarm_server';
   $properties=SQLSelect("SELECT ID FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //to-do
    }
   }
 }
 function processSubscription($event, $details='') {
 $this->getConfig();
  if ($event=='SAY') {
   $level=$details['level'];
   $message=$details['message'];
   //...
  }
 }
 function processCycle() {
 $this->getConfig();
  //to-do
 }

public static function response($data) {
	$table_name='alarm_server_camera';
//Выбираем из таблицы камер записи по полученному серийнику
	$rec=SQLSelectOne("SELECT * FROM $table_name WHERE serialID='$data[SerialID]'");	
// Проверяем на наличие записей		
	if (!rec[SerialID])
	{
// Если нет таких записей, то надо эту камеру добавить в базу.
	DebMes("Такой камеры нет в базе $table_name count($res)");
	$title="NoName";	
	//$Record = Array();
	//$Record['SerialID'] = $data[SerialID];
	//$Record['Address'] = $data[Address];
	//$Record['Place'] = "Надо изменить на свое";
	//$Record['ID']=SQLInsert($table_name, $Record);
	}
	else
	{
// Если запись есть, значит и камера с таким серийником есть, забираем название месторасположения и добавляем в событие.
	$title=$rec[place];
	}	
	$text = $title." ".$data[Type]." ".$data[Event]." ".$data[StartTime];	   
	DebMes($text);	
// полученные  данные пишем в базу	
	$Record = Array();
	$Record['TYPE'] = $data[Type];
	$Record['STATUS'] = $data[Status];
	$Record['EVENT'] = $data[Event];
	$Record['StartTime'] = $data[StartTime];
	$Record['TITLE'] = $title;
	$Record['SerialID'] = $data[SerialID];
	$Record['ID']=SQLInsert('alarm_server', $Record);	
// отправка в телеграмм, надо настроить динамическое присвоение ID пользователя.
	include_once(DIR_MODULES . 'telegram/telegram.class.php');
	$telegram_module = new telegram();
	if ($data[Event]=='HumanDetect')
	$telegram_module->sendMessageToUser(1396815589, $text);	   
    }


/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  subscribeToEvent($this->name, 'SAY');
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS alarm_server_camera');
  SQLExec('DROP TABLE IF EXISTS alarm_server');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
alarm_server_camera - 
alarm_server -  alarm_server: serialID int(10) NOT NULL DEFAULT '0'
*/
  $data = <<<EOD
 alarm_server_camera: ID int(10) unsigned NOT NULL auto_increment
 alarm_server_camera: TITLE varchar(100) NOT NULL DEFAULT ''
 alarm_server_camera: place varchar(255) NOT NULL DEFAULT ''
 alarm_server_camera: serialID varchar(255) NOT NULL DEFAULT ''
 alarm_server_camera: Adress varchar(255) NOT NULL DEFAULT ''
 alarm_server_camera: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 alarm_server_camera: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 alarm_server_camera: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
 alarm_server: ID int(10) unsigned NOT NULL auto_increment
 alarm_server: TITLE varchar(100) NOT NULL DEFAULT ''
 alarm_server: TYPE varchar(255) NOT NULL DEFAULT ''
 alarm_server: EVENT varchar(255) NOT NULL DEFAULT ''
 alarm_server: StartTime datetime NOT NULL DEFAULT ''

 alarm_server: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 alarm_server: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 alarm_server: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*  alarm_server: SerialID varchar(255) NOT NULL DEFAULT ''
*
* TW9kdWxlIGNyZWF0ZWQgRmViIDI2LCAyMDIzIHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
