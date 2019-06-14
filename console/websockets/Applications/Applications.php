<?php
use Workerman\Worker;
use Workerman\Lib\Timer;
use yii\helpers\Json;
use console\websockets\Applications\controllers\AuthController;
use console\websockets\Applications\controllers\EngineerController;
use console\websockets\Applications\controllers\BrigadierController;
use console\websockets\Applications\controllers\NodController;
use console\websockets\Applications\controllers\SupportController;
use console\websockets\Applications\controllers\ListenerController;
use console\websockets\Applications\controllers\ViewerController;

require(__DIR__ . '/../../../vendor/autoload.php');
require(__DIR__ . '/../../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../../common/config/bootstrap.php');

$config = require(__DIR__ . '/../../config/main.php');
$db = require(__DIR__ . '/../../config/db.php');
$config = array_merge($config, $db);
new yii\console\Application($config);

class Applications
{
	public $socket;
	public $connections;
	public $users;
	public $controllers = [];

	function __construct(){
		$this->socket = Yii::$app->params["websockets"]["applications"];

        $this->controllers = [
			'auth' => AuthController::Instance(),
			'listener' => ListenerController::Instance(),
			'viewer' => ViewerController::Instance(),
			'engineer' => EngineerController::Instance(),
			'brigadier' => BrigadierController::Instance(),
			'nod' => NodController::Instance(),
			'support' => SupportController::Instance(),
		];

		$this->run();
	}

	private function run(){
		// Create a Websocket server
		$ws_worker = new Worker($this->socket);

		// 4 processes
		// $ws_worker->count = 4;

		// Emitted when new connection come
		$ws_worker->onConnect = function($connection){
			$this->connections[$connection->id] = $connection;
		};

		// Emitted when connection closed
		$ws_worker->onClose = function($connection){
			unset($this->connections[$connection->id]);
		};

		// Emitted when data received
		$ws_worker->onMessage = function($connection, $data){
			$data = Json::decode($data);
			$response = $this->route($connection, $data);

			$response = Json::encode($response);
			$connection->send($response);
		};

		$ws_worker->onWorkerStart = function($ws_worker){
		    $time_interval = 0.1; 
		    $timer_id = Timer::add($time_interval, function(){
	    		$this->controllers["listener"]->run($this->connections, $this->users);
	        });
		};

		// Run worker
		Worker::runAll();
	}

	private function route($connection, $data){
		$response = '';

		if(isset($data["controller"]) && isset($data["action"]) && isset($data["post"])){
			$controller = $data["controller"];
			$action = $data["action"];
			$post = $data["post"];

			if(($controller == "auth") && ($action == "login")){
				$user = $this->controllers[$controller]->$action($post);

				if($user){
					$this->users[$connection->id] = $user;
				}
				else{
					$connection->close();
				}
			}
			else{
				$response = $this->controllers[$controller]->$action($post, $this->users[$connection->id]);
			}

		}

		return $response;
	}
}

new Applications();