<?php

// TODO: COMPROBAR QUE LO QUE FALLE AL GUARDAR EL FORMULARIO NO SEA EL POST O GET EQUIVOCADO (DEBERÍA SER POST)
// http://local.webhotel.com/api/rooms
// TODO: SEGUIR COMENTANDO Y PROBANDO

// Clase API rest. Todas los request a "/api" se redirigen aquí
class ApiController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
 
    //Formato de respuesta por defecto:
    private $format = 'json';
    
 
    /** 
     * ACCION: Lista todas las habitaciones disponibles para el rango de fechas dado.
     *  Ejemplo de request [GET]:
     *    * http://hostname/api/rooms?checkin=2017-11-01&checkout=2017-11-03   
     *
     *  - Para ejecutar el cálculo de precios totales es necesario indicar [fecha de entrada] y
     *    [fecha de salida].
     *  - Si no se especifica un rango de fechas muestra todas las habitaciones con su
     *    precio por defecto.
     *  - Se pueden añadir extras y calculará también el precio final:
     *      * http://local.webhotel.com/api/rooms?checkin=2017-11-01&checkout=2017-11-03&extras[]=aa&extras[]=pc
     *    (aa = aire acondicionado)
     *    (pc = pensión completa)
     *    Ver la tabla tbl_roomextras para ver todas las formas cortas de los extras.
     *  - Se puede añadir el número de personas (el resultado se multiplicará por él):
     *      * http://local.webhotel.com/api/rooms?checkin=2017-11-01&checkout=2017-11-03&persons=2
     */ 
    public function actionList() {        
    	
        // Obtenemos el modelo requerido
	    switch($_GET['model'])
	    {
	        case 'rooms':     
                // Todas las habitaciones           
	            $models = Room::model()->findAll();     
        

	            break;
	        default:
	            // Error si el modelo no está implementado
	            $this->_sendResponse(501, sprintf(
	                'Error: Mode <b>list</b> is not implemented for model <b>%s</b>',
	                $_GET['model']) );
	            Yii::app()->end();
	    }
	    // Si no hay algún resultado, envía la respuesta:
	    if(empty($models)) {
	        $this->_sendResponse(200, 
	                sprintf('A) No items where found for model <b>%s</b>', $_GET['model']) );
	    } else {            

	        // Preparando la respuesta:
	        $response = array();
            // Obteniendo el número de personas
            $persons = isset($_GET["persons"]) ? $_GET["persons"] : 1;                
            // Obteniendo la fecha de entrada
            $checkin = isset($_GET["checkin"]) ? $_GET["checkin"] : NULL;
            // Obteniendo la fecha de salida
            $checkout = isset($_GET["checkout"]) ? $_GET["checkout"] : NULL;
            // Obteniendo extra_key de los posibles extras
            $extras = NULL;
            if(isset($_GET["extras"])) {
                $extras = is_array($_GET["extras"]) ? array_unique($_GET["extras"]) : array($_GET["extras"]);
            }            
            // Para cada haitación...
	        foreach($models as $model) {

                // Solo añadimos a la respuesta las habitaciones disponibles.
                // La disponibilidad de resuelve en _checkAvailability(...)
                if($this->_checkAvailability($model->id, $checkin, $checkout)) {

                    // _calculatePrice nos devuelve la respuesta del calculo de precio
                    // total, así como de los detalles de los días que llevan incremento de precio.
                    $response[] = $this->_calculatePrice($model->id, $persons, $checkin, $checkout, $extras);                                            
                }                           
            }

            
            // Se comprueba que haya algo con lo que responder...
            if(empty($response)) {
                $this->_sendResponse(200, 
                    sprintf('B) No items where found for model <b>%s</b>', $_GET['model']) );
            }

	        $this->_sendResponse(200, CJSON::encode($response));
	    }
    }

    /** 
     * ACCION: Muestra una habitacion para el rango de fechas dado y si está disponible o no.
     *  Ejemplo de request [GET]:
     *    * http://hostname/api/rooms/1?checkin=2017-11-01&checkout=2017-11-03
     *
     *  - Si no se especifica un rango de fechas muestra la habitación con su
     *    precio por defecto.
     *  - Se pueden añadir extras y calculará también el precio final:
     *      * http://local.webhotel.com/api/rooms/1?checkin=2017-11-01&checkout=2017-11-03&extras[]=aa&extras[]=pc
     *    (aa = aire acondicionado)
     *    (pc = pensión completa)
     *    Ver la tabla tbl_roomextras para ver todas las formas cortas de los extras.
     *  - Se puede añadir el número de personas (el resultado se multiplicará por él):
     *      * http://local.webhotel.com/api/rooms/1?checkin=2017-11-01&checkout=2017-11-03&persons=2
     */ 
    public function actionView()
    {
        // Comprobamos que se esté enviando la petición por GET
        if(!isset($_GET['id']))
            $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing' );
        

        // Id de la habitación:
        $roomId = $_GET['id'];
        
        switch($_GET['model'])
        {            
            case 'rooms':
                // Preparando la respuesta:
                // Obteniendo el número de personas
                $persons = isset($_GET["persons"]) ? $_GET["persons"] : 1;
                // Obteniendo la fecha de entrada            
                $checkin = isset($_GET["checkin"]) ? $_GET["checkin"] : NULL;
                // Obteniendo la fecha de salida
                $checkout = isset($_GET["checkout"]) ? $_GET["checkout"] : NULL;
                
                // Calculando el precio de los extras:
                $extras = NULL;
                if(isset($_GET["extras"])) {
                    $extras = is_array($_GET["extras"]) ? array_unique($_GET["extras"]) : array($_GET["extras"]);
                }

                // _calculatePrice(...) nos devuelve la respuesta del calculo de precio
                // total, así como de los detalles de los días que llevan incremento de precio.
                $response = $this->_calculatePrice($roomId, $persons, $checkin, $checkout, $extras);

                // Comprobando la disponibilidad
                $availability = $this->_checkAvailability($roomId, $checkin, $checkout);
                                
                break;

            default:
                // Error si el modelo no está implementado
                $this->_sendResponse(501, sprintf(
                    'Mode <b>view</b> is not implemented for model <b>%s</b>',
                    $_GET['model']) );
                Yii::app()->end();
        }
        // Did we find the requested model? If not, raise an error
        if(is_null($response))
            $this->_sendResponse(404, 'No Item found with id '.$_GET['id']);
        else {            
            $response["availability"] = $availability ? "Available" : "Occupied";
            $this->_sendResponse(200, CJSON::encode($response));
        }
    }

    // Devuelve el número de días entre 2 fechas
    private function getDatesDiffDaysNum($date1, $date2){
        $dt1 = new DateTime($date1);
        $dt2 = new DateTime($date2);
        return $dt1->diff($dt2)->format("%a");
    }

    /** 
     * ACCION: Crea una reserva si el rango de fechas elegido está disponible para la habitación.
     *  Ejemplo de request [POST]:
     *    * http://hostname/api/rooms/
     *      {
     *          "room_id":1,
     *          "client_name":"Daniel Amador Soria",
     *          "comments":"Esto es un comentario",
     *          "checkin":"2017-11-01",
     *          "checkout":"2017-11-05",
     *          "extras":"aa,pc"
     *      }
     *
     *  - El código de reserva se genera automáticamente.
     *  - El precio se calcula automáticamente      
     *  - Se puede añadir el número de personas (el resultado se multiplicará por él):
     *      * http://local.webhotel.com/api/rooms/1?checkin=2017-11-01&checkout=2017-11-03&persons=2
     */ 
    public function actionCreate() {
        // Check if id was submitted via GET
        if(!isset($_POST['room_id']))
            $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing' );

        $roomId = $_POST['room_id'];
        $persons = isset($_POST["persons"]) ? $_POST["persons"] : 1;                
        $checkin = isset($_POST["checkin"]) ? $_POST["checkin"] : NULL;
        $checkout = isset($_POST["checkout"]) ? $_POST["checkout"] : NULL;        

        if(!$this->_checkAvailability($roomId, $checkin, $checkout)){
            $this->_sendResponse(409, 'Error: No availability in this dates' );
        }

        switch($_GET['model']) {
            // Get an instance of the respective model
            case 'rooms':
                $model = new Reservations();                    
                break;
            default:
                $this->_sendResponse(501, 
                    sprintf('Mode <b>create</b> is not implemented for model <b>%s</b>',
                    $_GET['model']) );
                    Yii::app()->end();
        }
        //$t = array("message"=>$_POST[]);
        
        // Try to assign POST values to attributes
         foreach($_POST as $var=>$value) {
            // Does the model have this attribute? If not raise an error
            if($model->hasAttribute($var))
                $model->$var = $value;
            else
                $this->_sendResponse(500, 
                    sprintf('Parameter <b>%s</b> is not allowed for model <b>%s</b>', $var,
                    $_GET['model']) );
        }
        // Añadiendo el resto de campos obligatorios
        // Generando codigo de reserva falso.
        $model->reservation_key = md5($model->client_name);
        // $resp[] = array("room_id"=>$roomId);
        // $resp[] = array("persons"=>$persons);
        // $resp[] = array("checkin"=>$checkin);
        // $resp[] = array("checkout"=>$checkout);

        // Comprobando los extras
        //if(isset($_POST["extras"])){
            $extras = isset($_POST["extras"]) ? $_POST["extras"] : "";
            //$resp[] = array("result"=>$extras);
        //}
        
        // test
        //$this->_sendResponse(200, CJSON::encode($extras));


        $model->extras = $extras;
        // Calculando el precio total:
        $res = $this->_calculatePrice($roomId, $persons, $checkin, $checkout, $extras);     
        $model->total = $res["total"];

        // Se almacena en BBDD y si no hay error se notifica a través de la respuesta:
        if($model->save()){
            $response = array("status"=>"Saved");
            $response["room"] = $model;
            $this->_sendResponse(200, CJSON::encode($response));
        } else {
            // Errors occurred
            $msg = "<h1>Error</h1>";
            $msg .= sprintf("Couldn't create model <b>%s</b>", $_GET['model']);
            $msg .= "<ul>";
            foreach($model->errors as $attribute=>$attr_errors) {
                $msg .= "<li>Attribute: $attribute</li>";
                $msg .= "<ul>";
                foreach($attr_errors as $attr_error)
                    $msg .= "<li>$attr_error</li>";
                $msg .= "</ul>";
            }
            $msg .= "</ul>";
            $this->_sendResponse(500, $msg );
        }
    }

    public function actionUpdate() {
        // Todo: implementation
    }
    
    public function actionDelete() {
        // Todo: implementation
    }

    /* Comprobar disponibilidad de reserva en las fechas dadas para la habitación
     * especificada.
     */
    private function _checkAvailability($roomId, $checkin, $checkout) {
        // Los 3 argumentos son necesarios:

        if(!is_null($roomId) && !is_null($checkin) && !is_null($checkout)) {

            // Obtenemos la habitación
            $roomMod = Room::model()->findByPk($roomId);
            // Formateamos las fechas (estilo por defecto de MySQL)
            $checkin = date('Y-m-d', strtotime($checkin));   
            $checkout = date('Y-m-d', strtotime($checkout));
                        
            // Comprobamos que el rangos de fechas (checkin y checkout) no 
            // coincida con ningún otro rango existente en las reservas.                  
            $criteria = new CDbCriteria;
            $criteria->addCondition('room_id = '.$roomId);
            $criteria->addCondition("('".$checkin."' < checkout) AND ('".$checkout ."' > checkIn)");            
            $reservations = Reservations::model()->findAll($criteria);                          
            
            // devuelve True o false dependiendo si existe alguna reserva para estas fechas o no.
            return empty($reservations);                                         
        }
        return true;
    }

    /* Calcula el precio total y el desglose de incrementos por día y el precio total de los extras 
     * 
     */
    private function _calculatePrice($roomId, $persons=1, $checkIn=NULL, $checkOut=NULL, $extras=NULL ) {
        
        $total = 0.0;                        
        
        // Obtenemos la habitación (por su identificador) y creamos el objeto de la respuesta
        $roomMod = Room::model()->findByPk($roomId);  
        $response = array("room"=>$roomMod->attributes,"extras"=>NULL);
        $response["test"] = "test";
        if(!is_null($checkIn) && !is_null($checkOut)) { 
            // Calculando extras
            if(!is_null($extras)) {
                // Convertimos los extras para que siempre sean un array y que no existan repetidos.
                $parsed_extras = is_array($extras) ? array_unique($extras) : array($extras);                
                $criteria = new CDbCriteria;
                $criteria->select = 'id, SUM(price) AS totalExtras';                    
                foreach ($parsed_extras as $extra) {
                    //$criteria->addCondition('extra_key LIKE "'.$extra.'"');                            
                    $criteria->compare('extra_key', $extra, true, 'OR');
                }                    
                //for()
                $roomExtras = Roomextras::model()->find($criteria);
                $response["extras"] = $roomExtras->totalExtras;            
            }

            $checkin = date('Y-m-d', strtotime($checkIn));   
            $checkout = date('Y-m-d', strtotime('-1 day', strtotime($checkOut)));
            // Obtenemos los precios particulares por temporad para esta habitación                    
            $criteria = new CDbCriteria;
            $criteria->addCondition('room_id = '.$roomId);

            $nights = $this->getDatesDiffDaysNum($checkin, $checkout) + 1;

            // fechas comprendidas entre la entrada y la salida (el día de salida no se cuenta)                        
            $criteria->addCondition("date_price BETWEEN '".$checkin."' AND '".$checkout."'"); 
            
            $seasonPrices = Seasonprices::model()->findAll($criteria);                                                           
            $response["increments_per_day"] = array();                               
            if($seasonPrices){
                                        
                $response["increments_per_day"] = array();                    
                foreach ($seasonPrices as $sprice) {
                    $day = date('d-m-Y', strtotime($sprice->date_price));
                    $increment = $sprice->increment;
                    $dayDate = date('Y-m-d', strtotime($day));
                    $response["increments_per_day"][] = array($day=>$increment);                             
                    $total += floatval($roomMod->price) + floatval($increment);                            
                }
            } else {
                $total += floatval($roomMod->price) * $nights;                            
            }
            $response["nights"] = $nights;                  
            $response["total"] = ($total + (floatval($response["extras"])*floatval($nights))) * $persons;
            $response["persons"] = $persons;
        }
        return $response;
    }

    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html; charset=utf-8') {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);        
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);

     
        // pages with body are easy
        if($body != '')
        {
            // send the body
            $response = "{\"response\":\"".$body."\"}";
            echo ($body);
        }
        // we need to create the body if none is passed
        else
        {
            // create some body messages
            $message = '';
     
            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch($status)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }
     
            // servers don't always have a signature turned on 
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
     
            // this should be templated in a real-world solution
            $body = '
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
            </head>
            <body>
                <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
                <p>' . $message . '</p>
                <hr />
                <address>' . $signature . '</address>
            </body>
            </html>';
     
            echo $body;
        }
        Yii::app()->end();
    }

    private function _getStatusCodeMessage($status) {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}