<?php
include_once("M3UDatos.php");
include_once("M3U.php");

if (isset($_GET['funcion'])){
    $cargador = new Cargador();
    // Si se recibe como parámetro el nombre de una función, la ejecutamos.
    if (strtoupper($_GET["funcion"]) == "IMPRIMIRCANALES" && isset($_GET["id"])) {
        // La lista de canales ha de llevar indicado el Id del grupo.
        echo $cargador->ImprimirCanales($_GET["id"]);
    } elseif (strtoupper($_GET["funcion"]) == "IMPRIMIRGRUPOS"){
        $url = "";
        $lista = -1;
        if (isset($_GET["url"])) {
            $url = $_GET["url"];
        }
        if (isset($_GET["lista"])) {
            $lista = intval(isset($_GET["lista"]));
        }
        echo $cargador->ImprimirGrupos($lista, $url);
    } elseif (strtoupper($_GET["funcion"]) == "IMPRIMIRLISTASCANALES") {
        $cargador->ImprimirListas();
    } elseif (strtoupper($_GET["funcion"]) == "IMPRIMIRLISTASM3U") {
        $cargador->ImprimirListasM3u();
    }
}


class Cargador{
    private $bloqueGrupo = "<input type='checkbox' id='%s' name='%s' value='%s' onclick='cargaCanales(\"%s\")'/><label for='%s'>%s</label><br/>";
    private $bloqueCanal = "<input type='checkbox' id='%s' name='%s' value='%s'/><label for='%s'>%s</label><br/>";
    private $bloqueListaM3u = "<option name='listasM3U' id='%s' value='%s'>%s</option>";
    
    public function ImprimirGrupos($lista, $url = "") {
        // Antes imprimimos las listas.
        if ($url == "") {
            $url = ListaM3U::getDefaultUrl();
        }
        $tokenizar = new M3U();
        $arrayGrupos = $tokenizar->cargarFicheroGrupos($lista, $url);
        echo "<div id='bloqueGrupos'>\n";
        echo "    <form id='formGrupos' name='formGrupos'>\n";
        foreach ($arrayGrupos as $grupo) {
            $checkGroup = sprintf($this->bloqueGrupo, $grupo,"bloqueGrupos", "", $grupo, $grupo, $grupo);
            echo $checkGroup."\n";
        }
        echo "    </form>\n";
        echo "</div>\n";
        // Grabamos la url de la lista.
        $consulta = new ConsultasLista();
    }

    public function ImprimirListas() {
        $listaM3U = new M3U();
        $arrayListas = $listaM3U->cargarListas();
        $linea = "<input type='radio' id='%s' name='%s' value='%s'/> <label for='%s'>%s</label><br/>\n";
        foreach ($arrayListas as $lista) {
            $printLinea = sprintf($linea, $lista->id, 'radiosListas', $lista->url, $lista->id, $lista->descripcion);
            echo $printLinea;
        }
    }


    public function ImprimirCanales(string $grupo) {
        return;
        // Obtenemos los canales de los grupos indicados.
        $grupo = "'".$grupo."'";
        echo "<div id='bloqueCanales'>\n";
        echo "    <form id='formCanales' name='formCanales'>\n";
        $listaM3U = new M3U();
        $arrayCanales = $listaM3U->cargarFicheroCanalesPorGrupos([$grupo]);
        $numCanales = count($arrayCanales);
        for ($i = 0; $i < $numCanales; $i++) {
            $checkGroup = sprintf($this->bloqueCanal, $arrayCanales[$i]->Id,"bloqueCanales", "", $arrayCanales[$i]->Id, $arrayCanales[$i]->Nombre);
            echo $checkGroup."\n";
        }
        echo "    </form>\n";
        echo "</div>\n";
    }
    
    public function ImprimirListasM3u() {
        $listaM3U = new M3U();
        $arrayListas = $listaM3U->cargarListasM3u();
        echo "<SELECT id='idListaM3U' name='idListaM3U'>";
        foreach ($arrayListas as $lista) {
            $printLinea = sprintf($this->bloqueListaM3u, $lista->id,  $lista->id, $lista->url);
            echo $printLinea;
        }
        echo "</SELECT>";
    }


}

