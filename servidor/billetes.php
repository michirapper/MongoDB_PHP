<?php

header('Access-Control-Allow-Origin: http://localhost'); 

header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE'); 

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		//echo "Solicitado GET";
		require 'leerBilletes.php';
		verBilletes();
		break;
	case 'POST':
		//echo "Solicitado POST";
		require 'insertarBilletes.php';
		escribirBillete();
		break;
	case 'PUT':
		//echo "Solicitado PUT";
		require 'modificarBillete.php';
		actualizarBillete();
		break;
	case 'DELETE':
		//echo "Solicitado DELETE";
		require 'borrarBillete.php';
		borrarBillete();
		break;
	default:
		echo "No disponible";
		break;
}

// $parameters = file_get_contents("php://input");
// if (isset($parameters)) {
// 	echo "</br>";
// 	echo "MENSAJE RECIBIDO";
// 	echo "</br>";

// 	var_dump($parameters);

// 	$mensajeRecibido = json_decode($parameters, true);

// 	echo "</br>";
// 	echo "MENSAJE RECIBIDO";
// 	echo "</br>";

// 	var_dump($mensajeRecibido);


// }
?>