<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='alarm_server_camera';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  if ($this->mode=='update') {
   $ok=1;
  // step: default
  if ($this->tab=='cam') {
  //updating '<%LANG_TITLE%>' (varchar, required)
   $rec['TITLE']=gr('title');
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'place' (varchar)
   $rec['PLACE']=gr('place');
  //updating 'serialID' (varchar)
   $rec['SERIALID']=gr('serialid');
  //updating 'Adress' (varchar)
   $rec['ADRESS']=gr('adress');
  //updating '<%LANG_LINKED_OBJECT%>' (varchar)
   $rec['LINKED_OBJECT']=gr('linked_object');
  //updating '<%LANG_LINKED_PROPERTY%>' (varchar)
   $rec['LINKED_PROPERTY']=gr('linked_property');
  //updating '<%LANG_METHOD%>' (varchar)
   $rec['LINKED_METHOD']=gr('linked_method');
  }
  // step: data
  if ($this->tab=='data') {
  }
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
  if ($this->tab=='data') {
  }
  if ($this->tab=='data') {
   //dataset2
   $new_id=0;
   if ($this->mode=='update') {
    global $title_new;
	if ($title_new) {
	 $prop=array('TITLE'=>$title_new,'serialID'=>$rec['ID']);
	 $new_id=SQLInsert('alarm_server',$prop);
	}
   }
   global $delete_id;
   if ($delete_id) {
    SQLExec("DELETE FROM alarm_server WHERE ID='".(int)$delete_id."'");
   }
   $properties=SQLSelect("SELECT * FROM alarm_server WHERE serialID='".$rec['ID']."' ORDER BY ID");
   $total=count($properties);
   for($i=0;$i<$total;$i++) {
    if ($properties[$i]['ID']==$new_id) continue;
    if ($this->mode=='update') {
      $properties[$i]['TITLE']=gr('title'.$properties[$i]['ID'],'trim');
      $properties[$i]['VALUE']=gr('value'.$properties[$i]['ID'],'trim');
      $properties[$i]['LINKED_OBJECT']=gr('linked_object'.$properties[$i]['ID'],'trim');
      $properties[$i]['LINKED_PROPERTY']=gr('linked_property'.$properties[$i]['ID'],'trim');
      $properties[$i]['LINKED_METHOD']=gr('linked_method'.$properties[$i]['ID'],'trim');
      SQLUpdate('alarm_server', $properties[$i]);
      if ($old_linked_object && $old_linked_object!=$properties[$i]['LINKED_OBJECT'] && $old_linked_property && $old_linked_property!=$properties[$i]['LINKED_PROPERTY']) {
       removeLinkedProperty($old_linked_object, $old_linked_property, $this->name);
      }
      if ($properties[$i]['LINKED_OBJECT'] && $properties[$i]['LINKED_PROPERTY']) {
       addLinkedProperty($properties[$i]['LINKED_OBJECT'], $properties[$i]['LINKED_PROPERTY'], $this->name);
      }
     }
   }
   $out['PROPERTIES']=$properties;   
  }
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
