<?php
/*
* @version 0.1 (wizard)
*/

  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='alarm_server_camera';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
   //DebMes ("rec=".$rec['ID']." ".$rec['TITLE']." ".$rec['PLACE']." ".rec['SERIALID']." ".$rec['ADRESS']);  
  if ($this->mode=='update') {
   $ok=1;
  //updating TITLE (varchar, required)
   global $title;
   $rec['TITLE']=$title;
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'place' (varchar)
  global $place;
   $rec['PLACE']=$place;
   if ($rec['PLACE']=='') {
    $out['PLACE']=1;
    $ok=0;
   }     
  //updating 'serialID' (varchar)
  global $serialid;
   $rec['SERIALID']=$serialid;
   if ($rec['SERIALID']=='') {
    $out['SERIALID']=1;
    $ok=0;
   }
  //updating 'IP' (varchar)
  global $rtsp;
   $rec['RTSP']=$rtsp;
   if ($rec['RTSP']=='') {
    $out['RTSP']=1;
    $ok=0;
   }   
  //updating 'Adress' (varchar)
   global $adress;
   $rec['ADRESS']=$adress;
   if ($rec['ADRESS']=='') {
    $out['ADRESS']=1;
    $ok=0;
   } 
  //updating LANG_LINKED_OBJECT (varchar)
   $rec['LINKED_OBJECT']=gr('linked_object');
  //updating LANG_LINKED_PROPERTY (varchar)
   $rec['LINKED_PROPERTY']=gr('linked_property');
  //updating LANG_METHOD (varchar)
   $rec['LINKED_METHOD']=gr('linked_method');


  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
  // step: default
  if ($this->tab=='') {
  }
  // step: data
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
