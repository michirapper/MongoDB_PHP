<?php
function verBilletes(){


require 'vendor/autoload.php'; // include Composer goodies
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$colección = $cliente
    ->ADAT_Vuelos->vuelo;

if (isset($_GET["fecha"]) && isset($_GET["origen"]) && isset($_GET["destino"]))
{
    $fechaParam = $_GET["fecha"];
    $origenParam = $_GET["origen"];
    $destinoParam = $_GET["destino"];

    $resultado = $colección->find(['fecha' => $fechaParam, 'origen' => $origenParam, 'destino' => $destinoParam]);

}
elseif (isset($_GET["origen"]) && isset($_GET["destino"]))
{
    $origenParam = $_GET["origen"];
    $destinoParam = $_GET["destino"];
    $resultado = $colección->find(['origen' => $origenParam, 'destino' => $destinoParam]);
}
elseif (isset($_GET["fecha"]) && isset($_GET["origen"]))
{
    $fechaParam = $_GET["fecha"];
    $origenParam = $_GET["origen"];
    $resultado = $colección->find(['fecha' => $fechaParam, 'origen' => $origenParam]);
}
elseif (isset($_GET["fecha"]) && isset($_GET["destino"]))
{
    $fechaParam = $_GET["fecha"];
    $destinoParam = $_GET["destino"];
    $resultado = $colección->find(['fecha' => $fechaParam, 'origen' => $destinoParam]);
}
elseif (isset($_GET["fecha"]))
{
    $fechaParam = $_GET["fecha"];
    $resultado = $colección->find(['fecha' => $fechaParam]);
}
elseif (isset($_GET["origen"]))
{
    $origenParam = $_GET["origen"];
    $resultado = $colección->find(['origen' => $origenParam]);
}
elseif (isset($_GET["destino"]))
{
    $destinoParam = $_GET["destino"];
    $resultado = $colección->find(['destino' => $destinoParam]);
}
else
{
    $resultado = $colección->find();
}
//$resultado = $colección->find( [  'origen' => "MADRID"] );
$contador = 0;

$sizeCollection = $colección->count();
$arrayVuelos = array();

if (isset($resultado) && $resultado)
{
    if ($sizeCollection > 0)
    {
        foreach ($resultado as $entry)
        {
            //echo $entry['_id'], ': ', $entry['codigo'], "<br/>";
           
           
            $arrayVuelo = array();
            $arrayVuelo["codigo"] = $entry["codigo"];
            $arrayVuelo["origen"] = $entry["origen"];
            $arrayVuelo["destino"] = $entry["destino"];
            $arrayVuelo["fecha"] = $entry["fecha"];
            $arrayVuelo["hora"] = $entry["hora"];
            $arrayVuelo["plazas_totales"] = $entry["plazas_totales"];
            $arrayVuelo["plazas_disponibles"] = $entry["plazas_disponibles"];
            $arrayVuelo["precio"] = $entry["precio"];
            
           // $arrayVuelos[] = $entry;
            $arrayVuelos[] = $arrayVuelo;
           
            $contador++;
        }
        $arrayMensaje["estado"] = true;
        $arrayMensaje["encontrados"] = $contador;
        $arrayMensaje["vuelos"] = $arrayVuelos;
        $mensajeJSON = json_encode($arrayMensaje, JSON_PRETTY_PRINT);
    }
    else
    {
        $arrayMensaje["estado"] = true;
        $arrayMensaje["encontrados"] = 0;
    }
}
else
{
    $arrayMensaje["estado"] = "error";
    $arrayMensaje["mensaje"] = "Se ha producido un error al conectar con la BD";
}
if (isset($_GET["debug"]) && $_GET["debug"] == 1)
{
    echo "<pre>";
    echo $mensajeJSON;
    echo "</pre>";
}
else
{
    echo $mensajeJSON;
}
}
?>
