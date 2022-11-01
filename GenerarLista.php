<?php
include_once ("BBDD.php");
include_once("M3UDatos.php");
include_once("M3U.php");
if (isset($_GET["funcion"])) {
    if (($_GET["funcion"] == "grabarGruposSeleccionados") && (isset($_GET["grupos"])) &&  (isset($_GET["lista"])) && (isset($_GET["idListaM3u"]))) {
        GenerarLista::GrabarGruposSeleccionados($_GET["grupos"], $_GET["lista"], $_GET["idListaM3u"]);
    } 
    if ($_GET["funcion"] == "generarLista") {
        GenerarLista::GenerarListaM3U();
    }
    if (($_GET["funcion"] == "descargar") && isset($_GET["fichero"])) {
        GenerarLista::descargarFichero($_GET["fichero"]);
    }
    if ($_GET["funcion"] == "cargarGruposSeleccionados" && isset($_GET["lista"])) {
        GenerarLista::CargarGruposSeleccionados($_GET["lista"]);
    }
}
$grupos = array();
if (isset($_GET["grupos"])) {
    $grupos = $_GET["grupos"];
}


class GenerarLista
{
    public static function GrabarGruposSeleccionados($grupos, $idLista, $idListaM3u) {
        $arrayGrupos = explode("\n", $grupos);
        unset($arrayGrupos[count($arrayGrupos) - 1]);
        $arrayGrupos = array_values($arrayGrupos);
        $bbdd = new ConsultasLista();
        $bbdd->GuardarGruposSeleccionados($arrayGrupos, $idLista, $idListaM3u);
        return $grupos;
    }


    public static function GenerarListaM3U($fileName, $idLista) {
        $consulta = new ConsultasLista();
        $urlLista = $consulta->ObtenerUrlLista($idLista);
        
        $lineaM3U_1 = '#EXTINF:%s tvg-id="%s" tvg-name="%s" tvg-logo="%s" group-title="%s",%s';
        $lineaM3U_2 = '%s';
        $listaM3U = new M3U();
        $arrayCanales = $listaM3U->cargarFicheroCanalesPorGrupos($urlLista, $idLista);

        echo M3U::CABECERAM3U."\n";
        foreach ($arrayCanales as $canal) {
            $linea1 = sprintf($lineaM3U_1, $canal->ExtInf, $canal->Id, $canal->Nombre, $canal->Logo, $canal->TituloGrupo, $canal->Titulo);
            $linea2 = sprintf($lineaM3U_2, $canal->Url);
            if (trim($linea1) != "" && trim($linea2) != "") {
                echo $linea1 . $linea2;
            }
        }
    }

    public static function CargarGruposSeleccionados($idLista) {
        $consulta = new ConsultasLista();
        $gruposSel = $consulta->getListaGruposSeleccionados($idLista);
        foreach ($gruposSel as $grupo) {
            echo "<p>".$grupo."</p>\n";
        }
    }

}