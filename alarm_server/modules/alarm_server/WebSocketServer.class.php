<?php
/**
 * Класс WebSocket сервера
 */
class WebSocketServer {

    /**
     * Функция вызывается, когда получено сообщение от клиента
     */
    public $handler;

    /**
     * IP адрес сервера
     */
    private $ip;
    /**
     * Порт сервера
     */
    private $port;
    /**
     * Сокет для принятия новых соединений, прослушивает указанный порт
     */
    public $connection;
    /**
     * Для хранения всех подключений, принятых слушающим сокетом
     */
    private $connects;

    /**
     * Ограничение по времени работы сервера
     */
    private $timeLimit = 0;
    /**
     * Время начала работы сервера
     */
    private $startTime;
    /**
     * Выводить сообщения в консоль?
     */
    private $verbose = true;
    /**
     * Записывать сообщения в log-файл?
     */
    private $logging = false;
    /**
     * Имя log-файла
     */
    //private $logFile = './modules/Alarm_server/ws-log.txt';
    /**
     * Ресурс log-файла
     */
    private $resource;


    public function __construct($ip = 'ПОСТАВИТЬ СВОЙ', $port = 15002) {
        $this->ip = $ip;
        $this->port = $port;				
		//echo 'Класс WebSocket сервера создан'. PHP_EOL;
		include_once(DIR_MODULES . 'alarm_server/alarm_server.class.php');
		$Alarm_server_module = new Alarm_server();
		// $Alarm_server_module->name, PHP_EOL;
		return true;
    }

    public function __destruct() {
        if (is_resource($this->connection)) {
            $this->stopServer();
        }
        if ($this->logging) {
            //fclose($this->resource);
        }
    }

    /**
     * Дополнительные настройки для отладки
     */
    public function settings($timeLimit = 0, $verbose = false, $logging = false, $logFile = 'ws-log.txt') {

        if ($this->logging) {
            //$this->resource = fopen($this->logFile, 'a');
        }
    }

    /**
     * Выводит сообщение в консоль и/или записывает в лог-файл
     */
    private function debug($message) {
        $message = '[' . date('r') . '] ' . $message . PHP_EOL;
        echo $message;
        //fwrite($this->resource, $message);
		}

    /**
     * Запускает сервер в работу
     */
    public function startServer() {

        $this->debug('Try start server...');

        $this->connection = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (false === $this->connection) {
            $this->debug('Error socket_create(): ' . socket_strerror(socket_last_error()));
            return;
        }

        $bind = socket_bind($this->connection, $this->ip, $this->port); // привязываем к ip и порту
        if (false === $bind) {
            $this->debug('Error socket_bind(): ' . socket_strerror(socket_last_error()));
            return;
        }

        // разрешаем использовать один порт для нескольких соединений
        $option = socket_set_option($this->connection, SOL_SOCKET, SO_REUSEADDR, 1);
        if (false === $option) {
            $this->debug('Error socket_set_option(): ' . socket_strerror(socket_last_error()));
            return;
        }

        $listen = socket_listen($this->connection); // слушаем сокет
        if (false === $listen) {
            $this->debug('Error socket_listen(): ' . socket_strerror(socket_last_error()));
            return;
        }

        $this->debug('Server is running...');

        $this->connects = array($this->connection);
        $this->startTime = time();
    }

    /**
     * Останавливает работу сервера
     */
    public function stopServer() {
        // закрываем слушающий сокет
        socket_close($this->connection);
        if (!empty($this->connects)) { // отправляем все клиентам сообщение о разрыве соединения
            foreach ($this->connects as $connect) {
                if (is_resource($connect)) {
                    socket_write($connect, self::encode('  Closed on server demand', 'close'));
                    socket_shutdown($connect);
                    socket_close($connect);
                }
            }
        }
    }
/**
* Функция вызывается из cycle_alarm_server, в ней обрабатываются подключения.
*/
    public function proc() {    
		
		$this->debug('Waiting for connections...');
// сообщаем что процесс жив...
		$cycleName='cycle_alarm_server Run';
		setGlobal($cycleName, time(), 1);
		// создаем копию массива, так что массив $this->connects не будет изменен функцией socket_select()
		$read = $this->connects;
		$write = $except = null;
		/*
		 * Сокет $this->connection только прослушивает порт на предмет новых соединений. Как только поступило
		 * новое соединение, мы создаем новый ресурс сокета с помощью socket_accept() и помещаем его в массив
		 * $this->connects для дальнейшего чтения из него.
		 */

		if ( ! socket_select($read, $write, $except, 1)) { // ожидаем сокеты, доступные для чтения (таймаут 1 секунда)
			return;
		}

		// если слушающий сокет есть в массиве чтения, значит было новое соединение
		if (in_array($this->connection, $read)) {
			// принимаем новое соединение и производим рукопожатие  && $this->handshake($connect)
			if (($connect = socket_accept($this->connection))) {
				$this->debug('New connection accepted');
				$this->connects[] = $connect; // добавляем его в список необходимых для обработки
			}
			// удаляем слушающий сокет из массива для чтения
			unset($read[ array_search($this->connection, $read) ]);
		}

		foreach ($read as $connect) { // обрабатываем все соединения, в которых есть данные для чтения
			$data = socket_read($connect, 100000);
			$decoded = self::decode($data);
			$this->debug($data);
			
			// если клиент не прислал данных или хочет разорвать соединение
			if (false === $decoded) { // || 'close' === $decoded['type']
				$this->debug('Connection closing');
				//socket_write($connect, self::encode('  Closed on client demand', 'close'));
				//socket_shutdown($connect);
				socket_close($connect);
				unset($this->connects[ array_search($connect, $this->connects) ]);
				$this->debug('Closed successfully');
				continue;
			}
			// получено сообщение от клиента, вызываем пользовательскую
			// функцию, чтобы обработать полученные данные

			Alarm_server::response($decoded);

		}

		// если истекло ограничение по времени, останавливаем сервер
		if ($this->timeLimit && time() - $this->startTime > $this->timeLimit) {
			$this->debug('Time limit. Stopping server.');
			$this->StopServer();
			return;
		}

    }

    /**
     * Для декодирования сообщений, полученных от клиента
     */
    private static function decode($data) {
        if ( ! strlen($data)) {
            //echo "return false;". PHP_EOL;
			return false;
        }
		$str=strpos($data, "{");
		$data=substr($data, $str);
		$decodedData = json_decode($data, true);
		//echo strlen($data). PHP_EOL;
		//print_r($decodedData);
        return $decodedData;
    } 

}