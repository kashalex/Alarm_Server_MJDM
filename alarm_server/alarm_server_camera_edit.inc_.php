<?php
/*
* @version 0.1 (wizard)
*/

  DebMes("Add");
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='alarm_server_camera';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  DebMes("Title= ".$rec['TITLE']." place= ".$rec['PLACE']." ID= ".$rec['ID']);

  DebMes($this->mode);
  if ($this->mode=='update') {
   $ok=1;
  // step: default
  if ($this->tab=='cam') {
  //updating 'TITLE' (varchar, required)
   global $title;
  DebMes("Title1= ".$title);   
   $rec['TITLE']=$title;
   if ($rec['TITLE']=='') {
    $out['ERR_TITLE']=1;
    $ok=0;
   }
  //updating 'place'
   global $place;
   if (isset($place)) DebMes("OK");
   //DebMes("place= ".$place);
	//$rec['place']=$place;
   //DebMes("$rec['place']=".$place);
   //if ($rec['place'])=='' {
    //$ok=0;
    //$out['ERR_PLACE']=1;
   //}
  //updating 'UPDATE_EVERY' (int)
   global $update_every;
   //$rec['UPDATE_EVERY']=(int)$update_every;
  //updating 'SCRIPT_ID' (int)
   global $script_id;
   //$rec['SCRIPT_ID']=(int)$script_id;
  }
  //UPDATING RECORD
   if ($ok) {

    //$rec['LAST_UPDATE']=date('Y-m-d H:i:s');
    //$rec['NEXT_UPDATE']=date('Y-m-d H:i:s');

    if ($rec['ID']) {
   DebMes("SQLUpdate");
   DebMes($table_name); 
   DebMes($rec);   
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
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);
	DebMes($rec, $out);
     DebMes("out= ".$out);

?>