<?php
/*
* @version 0.1 (wizard)
*/
 
   DebMes("Add");
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['alarm_server_camera_qry'];
  } else {
   $session->data['alarm_server_camera_qry']=$qry;
  }
  if (!$qry) $qry="1";
  $sortby_alarm_server_camera="ID DESC";
  $out['SORTBY']=$sortby_alarm_server_camera;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM alarm_server_camera WHERE $qry ORDER BY ".$sortby_alarm_server_camera);
  if ($res[0]['ID']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
   }
   $out['RESULT']=$res;
  }
