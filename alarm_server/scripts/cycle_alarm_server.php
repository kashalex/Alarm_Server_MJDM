<?php
chdir(dirname(__FILE__) . '/../');
include_once("./config.php");
include_once("./lib/loader.php");
include_once("./lib/threads.php");
require_once("./modules/alarm_server/WebSocketServer.class.php");
set_time_limit(0);
// connecting to database
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once("./load_settings.php");
include_once(DIR_MODULES . "control_modules/control_modules.class.php");
$ctl = new control_modules();
include_once(DIR_MODULES . 'alarm_server/alarm_server.class.php');
$alarm_server_module = new alarm_server();
$alarm_server_module->getConfig();
$tmp = SQLSelectOne("SELECT ID FROM alarm_server_camera LIMIT 1");
if (!$tmp['ID'])
   exit; // no devices added -- no need to run this cycle
echo date("H:i:s") . " running " . basename(__FILE__) . PHP_EOL;
$latest_check=0;
$checkEvery=5; // poll every 5 seconds
$server = new WebSocketServer('192.168.1.126', 15002);
if (is_resource($server->connection)) {
            $server->stopServer();
        }
$server->settings();
$server->startServer();
//echo "старт Сервер...".PHP_EOL;;	
while (true)
{
   setGlobal((str_replace('.php', '', basename(__FILE__))) . 'Run', time(), 1);
   if ((time()-$latest_check)>$checkEvery) {
    $latest_check=time();
    echo date('Y-m-d H:i:s').' Polling devices...';
    $server->proc();
	$alarm_server_module->processCycle();
   }
   if (file_exists('./reboot') || IsSet($_GET['onetime']))
   {
      $db->Disconnect();
      exit;
   }
   sleep(1);
}
DebMes("Unexpected close of cycle: " . basename(__FILE__));
