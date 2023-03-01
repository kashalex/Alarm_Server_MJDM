# Alarm_Server_MJDM
Тревожный сервер на базе MJDM для IP камер.

Настройки пока реализованы через костыли.

В WebSocketServer.class.php
Внести изменения:
 - public function __construct($ip = 'ПОСТАВИТЬ СВОЙ', $port = ПОСТАВИТЬ СВОЙ)
По умолчанию в настройках камер стоит порт 15002.

Для отправки в Телеграмм в строке 285 alarm_server_class.php поставить свой ID/

Для получения серийника камеры раскомментировать строку 271 в - alarm_server_class.php
На камере инициировать любое событие.
В файл лога должно вывалиться примерно такое:
{
	"Address": "0x7101A8C0",
	"Channel": 0,
	"Descrip": "",
	"Event": "BlindDetect",
	"SerialID": "c91f58182171b000",
	"StartTime": "2023-01-25 21:47:01",
	"Status": "Stop",
	"Type": "Alarm"
}



 
