# Alarm_Server_MJDM
Тревожный сервер на базе MJDM для IP камер.

IP камеры имеют возможность слать при возникновении тревожных событий на указанный адрес и порт информацию о событии.
Данный модуль эту информацию принимает и сохраняет в базе.

Для нормальной работы надо знать серийный номер камеры - SerialID.

Пока для получения серийного номера используя костыль.
- раскомментировать строку 271 в - alarm_server_class.php
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

Или посмотреть в настройках камеры, вкладка информация о камере.


 
