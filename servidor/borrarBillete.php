<?php
function borrarBillete(){
	

require 'vendor/autoload.php';
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$coleccion = $cliente
    ->ADAT_Vuelos->vuelo;

$arrEsperado = array(
    "codigo" => "IB706",
    "dni" => "44556677H",
    "codigoVenta" => "GHJ7766GG"
);

function JSONCorrectoAnnadir($recibido)
{

    $aux = false;

    if (isset($recibido["codigo"]) && isset($recibido["dni"]) && isset($recibido["codigoVenta"]))
    {
        $aux = true;
    }

    return $aux;
}

/*
 * Se mostrará siempre la información en formato json para que se pueda leer desde un html (via js)
 * o una aplicación móvil o de escritorio realizada en java o en otro lenguajes
*/

$arrMensaje = array(); // Este array es el codificaremos como JSON tanto si hay resultado como si hay error
/*
 * Lo primero es comprobar que nos han enviado la información via JSON
*/

$parameters = file_get_contents("php://input");

if (isset($parameters))
{

    // Parseamos el string json y lo convertimos a objeto JSON
    $mensajeRecibido = json_decode($parameters, true);

    // Comprobamos que están todos los datos en el json que hemos recibido
    // Funcion declarada en jsonEsperado.php
    if (JSONCorrectoAnnadir($mensajeRecibido))
    {

        $codigo = $mensajeRecibido["codigo"];
        $dni = $mensajeRecibido["dni"];
        $codigoVenta = $mensajeRecibido["codigoVenta"];

        $nuevosdatos = array('$inc' => array(
                    'plazas_disponibles' => +1
        ));
        $result = $coleccion->updateOne(array(
            "codigo" => $codigo
        ) , $nuevosdatos);

        $nuevosdatos = array(
            '$pull' => array(
                'vendidos' => array(
                    'dni' => $dni,
                    'codigoVenta' => $codigoVenta
                )
            )
        );
        $result = $coleccion->updateOne(array(
            "codigo" => $codigo
        ) , $nuevosdatos);

        if (isset($result) && $result)
        { // Si pasa por este if, la query está está bien y se ha insertado correctamente
            $arrMensaje["estado"] = "true";

        }
        else
        { // Se ha producido algún error al ejecutar la query
            $arrMensaje["estado"] = "error";
            $arrMensaje["mensaje"] = "No se ha podido borrar por error en la query";

        }

    }
    else
    { // Nos ha llegado un json no tiene los campos necesarios
        $arrMensaje["estado"] = "error";
        $arrMensaje["mensaje"] = "No se ha podido borrar por que los campos no tiene los datos correspondientes";
        $arrMensaje["recibido"] = $mensajeRecibido;
        $arrMensaje["esperado"] = $arrEsperado;
    }

}
else
{ // No nos han enviado el json correctamente
    $arrMensaje["estado"] = "error";
    $arrMensaje["mensaje"] = "No se ha podido borrar por error en los datos recibidos";

}

$mensajeJSON = json_encode($arrMensaje, JSON_PRETTY_PRINT);

//echo "<pre>";  // Descomentar si se quiere ver resultado "bonito" en navegador. Solo para pruebas
echo $mensajeJSON;
//echo "</pre>"; // Descomentar si se quiere
}
?>
