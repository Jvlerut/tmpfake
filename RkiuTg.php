<?php
include_once("GenerarLista.php");
include_once("BBDD.php");
if (isset($_GET["user"]) && isset($_GET["pass"]) && isset($_GET["lista"])) {
    $fechaHora = getdate();
    $fichero = $fechaHora["year"].$fechaHora["mon"].$fechaHora["mday"].$fechaHora["hours"].$fechaHora["minutes"].$fechaHora["seconds"].".m3u";
    $user = $_GET["user"];
    $pass = $_GET["pass"];
    $idLista = $_GET["lista"];
    $bbdd = new ConsultasLista();
    if ($bbdd->OkUser($user, $pass) === true) {
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fichero");
        header("Content-Type: text/plain");
        header("Content-Transfer-Encoding: Use7Bit");
        // Obtenemos la lista de canales de los grupos seleccionados.
        GenerarLista::GenerarListaM3U($fichero, $idLista);
        // Generamos fichero temporal a partir de los canales buscados.
        //if (file_exists($fichero)) {
        //    readfile($fichero);
        //    if (isset($fichero)) {
        //        unlink($fichero);
        //    }
        //}
    }
}
exit;