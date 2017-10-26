<?php

// Controlador de Reservas (muestra el formulario de reservas)
class ReservationsController extends Controller
{
	// Acción por defecto:
	public function actionIndex()
	{		
		// Si no sabemos de que habitación se trata no 
		// dejamos que se llegue al formulario de reserva:
		if(!isset($_GET["room_id"])){			
			$this->redirect(Yii::app()->user->returnUrl);			
		}

		// Recopilando datos para mostrar en la vista ($arguments):
		$roomId = $_GET["room_id"];
		$arguments = array("room_id"=>$roomId);
		$arguments["selected_extras_cs"] = "";
		$selected_extras = array();
		if(isset($_GET["room_extras"])){
			$selected_extras = is_array($_GET["room_extras"]) ? $_GET["room_extras"] : array($_GET["room_extras"]);
			$arguments["selected_extras"] = $selected_extras;
			$sep = "";
			foreach ($selected_extras as $extKey) {
				$arguments["selected_extras_cs"] .= $sep.$extKey; 
				$sep = ",";
			}
		}
		
		$roomName = Room::model()->findByPk($roomId)->name;
		$arguments["room_name"] = $roomName;

		$roomExtras = Roomextras::model()->findAll();
		$arguments["room_extras"] = $roomExtras;

		if(isset($_GET["checkin"])) {
			$arguments["checkin"] = $_GET["checkin"];
		}

		if(isset($_GET["checkout"])) {
			$arguments["checkout"] = $_GET["checkout"];
		}

		if(isset($_GET["total"])) {
			$arguments["total"] = $_GET["total"];
		}
		// renderizando la vista y pasando el array de argumentos:
		$this->render('index', $arguments);
	}
}