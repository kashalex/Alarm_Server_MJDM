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
  include_once(DIR_MODULES . 'telegram/telegram.class.php');
  $this->telegram_module = new telegram();
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
 $out['API_URL']=$this->config['IP'];
 if (!$out['IP']) {
  $out['IP']='192.168.1.126';
 }
 $out['PORT']=$this->config['PORT'];
  if (!$out['PORT']) {
  $out['PORT']='15002';
 }
 $out['PATCH']=$this->config['PATCH'];
 $out['ID_USERNAME']=$this->config['ID_USERNAME'];
 $out['FOTO']=$this->config['FOTO'];
 
 if ($this->view_mode=='update_settings') {
   global $ip;
   $this->config['IP']=$ip;
   global $port;
   $this->config['PORT']=$port;
   global $id_username;
   $this->config['ID_USERNAME']=$id_username;
   global $foto;
   $this->config['FOTO']=$foto;
   global $patch;
   $this->config['PATCH']=$patch;
   
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
//-------------Работает---------------------------
// поиск записей о камерах и вывод в таблицу
if ($this->data_source=='camera') {
  if ($this->view_mode=='' || $this->view_mode=='search_camera') {
   $this->search_alarm_server_camera($out);
  }
//добавление новой камеры если id пустой, если не пустой, то редактирование
  if ($this->view_mode=='edit_camera') {
   $this->edit_alarm_server_camera($out, $this->id);
   //DebMes("result=".$this->result);
  }  
// удаление записи об выбранной камере
	if ($this->view_mode=='delete_camera') {
		//DebMes ("SELECT * FROM alarm_server WHERE ID=$this->id");
		SQLExec("DELETE FROM alarm_server_camera WHERE ID='".(int)$this->id."'");
		$this->redirect("?data_source=camera");
	}   
}
//--------------не трогать------------------------  


 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 
//----------------- работает ---------------------------
//вывод событий на страничку
 if ($this->data_source=='events' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_events') {
   $this->search_alarm_server_event($out);
  }
//удаление всех записей о событиях 
 if ($this->view_mode=='delete_all') {
	SQLExec("DELETE FROM alarm_server WHERE 1");
	$this->redirect("?data_source=events");	   
  }
//удаление записи о выбранном событии
  if ($this->view_mode=='delete_events') {
	SQLExec("DELETE FROM alarm_server WHERE ID='".(int)$this->id."'");
	$this->redirect("?data_source=events");
  }
 }		
//--------------- не трогать ---------------------------
}
//}
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

public static function response($data,$id_user,$foto,$telegram_module,$patch='/var/www/html/out.jpg') {
	$table_name='alarm_server_camera';
	
//Выбираем из таблицы камер записи по полученному серийнику
	$rec=SQLSelectOne("SELECT * FROM $table_name WHERE SERIALID='$data[SerialID]'");	
// Проверяем на наличие записей		
	if (!$rec[SERIALID])
	{
// Если нет таких записей, то надо эту камеру добавить в базу.
	DebMes("Такой камеры нет в базе ". $table_name.count($res));
	$title="NoName";	
	//$Record = Array();
	//$Record['SERIALID'] = $data[SerialID];
	//$Record['ADRESS'] = $data[Address];
	//$Record['PLACE'] = "Надо изменить на свое";
	//$Record['ID']=SQLInsert($table_name, $Record);
	}
	else
	{
// Если запись есть, значит и камера с таким серийником есть, забираем название месторасположения и добавляем в событие.
	$title=$rec[PLACE];
	$rtsp=$rec[RTSP];	
	}	
	$text = $title." ".$data[Type]." ".$data[Event]." ".$data[StartTime];	   	
// полученные  данные пишем в базу	
	$Record = Array();
	$Record['TYPE'] = $data[Type];
	$Record['STATUS'] = $data[Status];
	$Record['EVENT'] = $data[Event];
	$Record['StartTime'] = $data[StartTime];
	$Record['TITLE'] = $title;
	$Record['SERIALID'] = $data[SerialID];
	$Record['ID']=SQLInsert('alarm_server', $Record);	
// отправка в телеграмм, надо настроить динамическое присвоение ID пользователя.
//include_once(DIR_MODULES . 'telegram/telegram.class.php');
//$telegram_module = new telegram();	
	if ($data[Event]=='HumanDetect'){
	$telegram_module->sendMessageToUser($id_user, $text);
	if ($foto==true){
	//exec('sudo ffmpeg -i rtsp://192.168.1.63:554/user=admin_password=imfzZCJe_channel=0_stream=0.sdp?real_stream -y -f mjpeg -t 0.110 -s 1280x720 
	//DebMes('sudo ffmpeg -i '.$rtsp.' -y -f mjpeg -t 0.001 -s 1280x720 /mnt/media/out.jpg');
	//$patch="/mnt/media/out.jpg";	
	exec('sudo ffmpeg -i '.$rtsp.' -y -f mjpeg -t 0.001 -s 1280x720 .'.$patch);

	$telegram_module->sendImageToUser($id_user, $patch);
	}
	}
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
alarm_server -  
*/
  $data = <<<EOD
 alarm_server_camera: ID int(10) unsigned NOT NULL auto_increment
 alarm_server_camera: TITLE varchar(100) NOT NULL DEFAULT ''
 alarm_server_camera: PLACE varchar(255) NOT NULL DEFAULT ''
 alarm_server_camera: SERIALID varchar(255) NOT NULL DEFAULT ''
 alarm_server_camera: ADRESS varchar(255) NOT NULL DEFAULT ''
 alarm_server_camera: IP varchar(15) NOT NULL DEFAULT ''
 alarm_server_camera: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 alarm_server_camera: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
 alarm_server_camera: LINKED_METHOD varchar(100) NOT NULL DEFAULT ''
 alarm_server: ID int(10) unsigned NOT NULL auto_increment
 alarm_server: TITLE varchar(100) NOT NULL DEFAULT ''
 alarm_server: TYPE varchar(255) NOT NULL DEFAULT ''
 alarm_server: EVENT varchar(255) NOT NULL DEFAULT ''
 alarm_server: StartTime datetime NOT NULL DEFAULT ''
 alarm_server: SERIALID int(10) NOT NULL DEFAULT '0'
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
