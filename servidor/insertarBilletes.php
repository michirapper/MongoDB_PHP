<?php
//require 'vendor/autoload.php'; // incluir lo bueno de Composer
//$cliente = new MongoDB\Client("mongodb://localhost:27017");
 //$coleccion = $cliente->ADAT_Vuelos->vuelo;

// $resultado = $colección->insertOne( [ 'name' => 'Hinterland', 'brewery' => 'BrewDog', 'vendidos'=> array('asiento1' => "hola")]);
// echo "Inserted with Object ID '{$resultado->getInsertedId()}'";

// $nuevosdatos = array('$set' => array('vendidos'=> array('asiento' => "h")));
// $colección->updateOne(array("nombre" => "Pedro"), $nuevosdatos);

//$nuevosdatos = array('$push' => array('vendidos'=> array('asiento' => "hddf", 'persona' => 3)));
//$updateResult = $coleccion->updateOne(array("nombre" => "Pedro"), $nuevosdatos);

//var_dump($colección->findOne(array("vendidos.asiento" => "f"), array("vendidos.$"=>1)));

//$resultado = $coleccion->findOne(array('$and'=>array(array("codigo"=>"IB706"), array("vendidos.asiento" =>array('$eq' =>1)))));
//

//$resultado = $coleccion->find(array('vendidos.asiento'=> 'f'));
//var_dump($resultado);

//$cursor = $collection->find($sweetQuery);
// foreach ($resultado as $doc) {
//     var_dump($doc);
// }

 // foreach ($resultado as $entry) {
 //     echo (string)$entry['asiento'], ': ', (string)$entry['persona'], "\n";
 //     var_dump($entry);
 // }

// printf("Matched %d document(s)\n", $updateResult->getMatchedCount());

//var_dump($updateResult);

// printf("Modified %d document(s)\n", $updateResult->writeResult());
?> 


<?php
require 'vendor/autoload.php'; 
$cliente = new MongoDB\Client("mongodb://localhost:27017");
 $coleccion = $cliente->ADAT_Vuelos->vuelo;
header('Access-Control-Allow-Origin: *'); 

	$arrEsperado = array(
        "codigo" => "IB706",
        "dni" => "44556677H",
        "apellido" => "Rodriguez",
        "nombre" => "María",
        "dniPagador" => "44556677H",
        "tarjeta" => "038 0025 5553 5553"
    );

function JSONCorrectoAnnadir($recibido){

    $aux = false;

            if(isset($recibido["codigo"]) 
                && isset($recibido["dni"])
                && isset($recibido["apellido"]) 
            	&& isset($recibido["nombre"])
            	&& isset($recibido["dniPagador"])
            	&& isset($recibido["tarjeta"])){
                    $aux = true;
                }

    return $aux;
}


$arrMensaje = array();  


$parameters = file_get_contents("php://input");


if(isset($parameters)){

    $mensajeRecibido = json_decode($parameters, true);

	if(JSONCorrectoAnnadir($mensajeRecibido)){

		$arrayAsientos = array();
		$arrayAsientosCogidos = array();
		$arrayDefinitivoAsignar = array();

		$codigo = $mensajeRecibido["codigo"];
		$dni = $mensajeRecibido["dni"];
		$apellido = $mensajeRecibido["apellido"];
		$nombre = $mensajeRecibido["nombre"];
		$dniPagador = $mensajeRecibido["dniPagador"];
		$tarjeta = $mensajeRecibido["tarjeta"];

		//CODIGO VENTA:		
		$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
		$codigoVenta = strtoupper(substr(str_shuffle($permitted_chars), 0, 10));
		//
		
		$resultado = $coleccion->find(array('codigo' => $codigo));

		foreach ($resultado as $entry) {

     		$origen = $entry['origen'];
     		$destino = $entry['destino'];
     		$fecha = $entry['fecha'];
     		$hora = $entry['hora'];
     		$plazas_totales = $entry['plazas_totales'];
     		$arrayVendidos = ((array)$entry["vendidos"]);
		 }

		  foreach ($arrayVendidos as $key) {

		 	$asientoCogido = $key["asiento"];
		 	array_push($arrayAsientosCogidos, $asientoCogido);
		 }
		 
		 for ($i=1; $i < $plazas_totales+1 ; $i++) { 
		 	array_push($arrayAsientos, $i);
		 }

		//Diferencia entre los arrays
		 $arrayAsientosLibres = array_diff($arrayAsientos, $arrayAsientosCogidos);

		 //Reordena nos arrays
		 $arrayAsientosLibres = array_reverse(array_reverse($arrayAsientosLibres));


		 $asientoAsignado = $arrayAsientosLibres[0];

		$nuevosdatos = array('$push' => array('vendidos'=> array('asiento' => $asientoAsignado ,'dni' => $dni, 'apellido' => $apellido, 'nombre'=>$nombre, 'dniPagador' => $dniPagador, 'tarjeta' => $tarjeta, 'codigoVenta'=>$codigoVenta)));
		$result = $coleccion->updateOne(array("codigo" => $codigo), $nuevosdatos);
		
		if (isset ( $result ) && $result) { // Si pasa por este if, la query está está bien y se ha insertado correctamente
			
			$arrMensaje["estado"] = "true";
			$arrMensaje["codigo"] = $codigo;
			$arrMensaje["origen"] = $origen;
			$arrMensaje["destino"] = $destino;
			$arrMensaje["fecha"] = $fecha;
			$arrMensaje["hora"] = $hora;

			$arrMensaje["asiento"] = $asientoAsignado;
			$arrMensaje["dni"] = $dni;
			$arrMensaje["apellido"] = $apellido;
			$arrMensaje["nombre"] = $nombre;
			$arrMensaje["dniPagador"] = $dniPagador;
			$arrMensaje["tarjeta"] = $tarjeta;
			$arrMensaje["codigoVenta"] = $codigoVenta;
			$arrMensaje["costeBillete"] = 0;
			
		}else{ // Se ha producido algún error al ejecutar la query
			
			$arrMensaje["estado"] = "error";
			$arrMensaje["mensaje"] = "No se ha podido realizar la compra por error en la query";
			
		}

		
	}else{ // Nos ha llegado un json no tiene los campos necesarios
		
		$arrMensaje["estado"] = "error";
		$arrMensaje["mensaje"] = "No se ha podido realizar la compra por que los campos no tiene los datos correspondientes";
		$arrMensaje["recibido"] = $mensajeRecibido;
		$arrMensaje["esperado"] = $arrEsperado;
	}

}else{	// No nos han enviado el json correctamente
	
	$arrMensaje["estado"] = "error";
	$arrMensaje["mensaje"] = "No se ha podido realizar la compra por error en los datos recibidos";
	
}

$mensajeJSON = json_encode($arrMensaje,JSON_PRETTY_PRINT);

//echo "<pre>";  // Descomentar si se quiere ver resultado "bonito" en navegador. Solo para pruebas
echo $mensajeJSON;
//echo "</pre>"; // Descomentar si se quiere

?>