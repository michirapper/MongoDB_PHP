<?php
require 'vendor/autoload.php'; // incluir lo bueno de Composer
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$colección = $cliente->ADAT_Vuelos->vuelo;

//$resultado = $colección->insertOne( [ 'name' => 'Hinterland', 'brewery' => 'BrewDog' ] );
//echo "Inserted with Object ID '{$resultado->getInsertedId()}'";

?>

<?php
// require 'vendor/autoload.php'; // include Composer goodies
// $cliente = new MongoDB\Client("mongodb://localhost:27017");
// $colección = $cliente->ADAT_Vuelos->vuelo;
// $resultado = $colección->find( [ 'name' => 'Hinterland', 'brewery' => 'BrewDog' ] );
// foreach ($resultado as $entry) {
//     echo $entry['_id'], ': ', $entry['name'], "\n";
// }

?>
<?php
require 'vendor/autoload.php'; // include Composer goodies
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$colección = $cliente->ADAT_Vuelos->vuelo;

$resultado = $colección->find();
$contador = 0;

//var_dump($colección->count());
//echo $colección->count();
$sizeCollection = $colección->count();

if (isset($resultado) && $resultado)
{
    if ($sizeCollection > 0)
    {
        foreach ($resultado as $entry)
        {
            //echo $entry['_id'], ': ', $entry['codigo'], "<br/>";
            $arrayVuelos[] = $entry;
            $contador++;
        }
        $arrayMensaje["estado"] = "ok";
        $arrayMensaje["numeroVuelos"] = $contador;
        $arrayMensaje["vuelos"] = $arrayVuelos;
        $mensajeJSON = json_encode($arrayMensaje, JSON_PRETTY_PRINT);
    }
    else
    {
        $arrayMensaje["estado"] = "ok";
        $arrayMensaje["numeroVuelos"] = 0;
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
?>
