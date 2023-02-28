<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
$action_type = gr('action_type');
if ($action_type) {
    $qry .= " AND alarm_server.EVENT='" . $action_type . "'";
    $out['ACTION_TYPE'] = $action_type;
}

$to = gr('to');
if ($to) {
    $out['TO'] = $to;
    $out['TO_URL'] = urlencode($to);
    $qry .= " AND alarm_server.StartTime<='" . $to . "'";
}

$from = gr('from');
if ($from) {
    $out['FROM'] = $from;
    $out['FROM_URL'] = urlencode($from);
    $qry .= " AND alarm_server.StartTime>='" . $from . "'";
}

$search = gr('search');
if ($search != '') {	
	$qry .= " AND (alarm_server.TITLE LIKE '%" . DBSafe($search) . "%'";
    $out['SEARCH'] = htmlspecialchars($search);
    $out['SEARCH_URL'] = urlencode($search);
}

// QUERY READY  Сортировка
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['alarm_server_qry'];
  } else {
   $session->data['alarm_server_qry']=$qry;
  }
  if (!$qry) $qry="1";
  $sortby_alarm_server="ID DESC";
  $out['SORTBY']=$sortby_alarm_server;

// SEARCH RESULTS
 
$res_total = SQLSelectOne("SELECT count(*) as TOTAL FROM alarm_server WHERE $qry");
$page = gr('page', 'int');
if (!$page) $page = 1;
$out['PAGE'] = $page;
$on_page = 20;
$limit = (($page - 1) * $on_page) . ',' . $on_page;
$urlPattern = '?page=(:num)&search=' . urlencode($search) . "&from=" . urlencode($from) . "&to=" . urlencode($to) . "&action_type=" . urlencode($action_type);
require(ROOT . '3rdparty/Paginator/Paginator.php');
$paginator = new JasonGrimes\Paginator($res_total['TOTAL'], $on_page, $page, $urlPattern);
$out['PAGINATOR'] = $paginator;
$res = SQLSelect("SELECT alarm_server.* FROM alarm_server WHERE $qry ORDER BY " . $sortby_alarm_server . " LIMIT $limit");
 


// SEARCH RESULTS  Поиск и выдача результата
//  $res=SQLSelect("SELECT * FROM alarm_server WHERE $qry ORDER BY ".$sortby_alarm_server);

  if ($res[0]['ID']) {
   //paging($res, 100, $out); // search result paging
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
   }
   $out['RESULT']=$res;
  }


$out['EVENT'] = SQLSelect("SELECT DISTINCT(EVENT) FROM alarm_server ORDER BY EVENT");