<?php
include_once ("M3UDatos.php");


abstract class BBDD
{
    private $DbServidor = "localhost";
    private $DbBaseDatos = "id17965484_tmpfake";
    private $DbUsuario = "id17965484_tmpfakeuser";
    private $DbPass = "Ua4veqq5_Ua4veqq5";
    //private $DbUsuario = "root";
    //private $DbPass = "";
    private $DbPuerto = "3306";
    protected $DbConexion;

    function __construct($servidor = "", $puerto = "", $bbdd = "", $nombre = "", $pass = "") {
        if ($servidor != "") {$this->DbServidor = $servidor;}
        if ($puerto != "") {$this->DbPuerto = $puerto;}
        if ($bbdd != "") {$this->DbBaseDatos = $bbdd;}
        if ($nombre != "") {$this->DbUsuario = $nombre;}
        if ($pass != "") {$this->DbPass = $pass;}
        $this->conectar();
    }

    function __destruct()
    {
        $this->desconectar();
    }

    private function conectar() {
        $this->DbConexion = new mysqli($this->DbServidor, $this->DbUsuario, $this->DbPass, $this->DbBaseDatos, $this->DbPuerto);
        if ($this->DbConexion->connect_errno) {
            return false;
        }
        $this->DbConexion->autocommit(false);
        // "Conexión BBDD válida\n";
        return true;
    }

    private function desconectar() {
        $this->DbConexion->close();
    }

    private function QueryResult2Array(mysqli_result $dataSet) {
        $arrayResultado = array();
        while ($fila = mysqli_fetch_object($dataSet)) {
            $arrayResultado[] = $fila;
        }

        /* liberar el conjunto de resultados */
        $dataSet->close();
        return $arrayResultado;
    }

    public function DbQuery($query) {
      $this->conectar();
        $resultado = @$this->DbConexion->query($query);
        if ($resultado === false) {
            //echo "Consulta fallida: ".$this->DbConexion->error."\n";
            return false;
        }
        if ($this->DbConexion->commit() === false) {
            // "Error al realizar commit\n";
            return false;
        }
        if (gettype($resultado) === "boolean") {
            // La query ha devuelto un valor booleano (insert, delete, update).
            return $resultado;
        }
        // La query ha devuelto un dataSet (select).
        $resultado = $this->QueryResult2Array($resultado);
        return $resultado;
    }

    public function LimpiarTabla($tabla) {
        // Borramos los elementos de la tabla.
        $deleteQuery = "TRUNCATE TABLE %s;";
        $deleteQuery = sprintf($deleteQuery, $tabla);
        return $this->DbQuery($deleteQuery);
    }


    public function Array2StringIN(array $valor) {
        $retorno = "";
        for ($i = 0; $i < count($valor); $i++) {
            $retorno .= "'".$valor[$i]."'";
            if ($i < count($valor) - 1) {
                $retorno.= ",";
            }
        }
        return $retorno;
    }
}

class ConsultasLista extends BBDD{
    public function __construct($servidor = "", $puerto = "", $bbdd = "", $nombre = "", $pass = ""){
        parent::__construct($servidor, $puerto, $bbdd, $nombre, $pass);
    }

    public function GrabarUrlLista($url)
    {
        $query = "INSERT INTO lista (url) VALUES ('" . $url . "');";
        return $this->DbQuery($query);
    }

    public function ObtenerUrlLista($idLista) {
        //$query = "SELECT DISTINCT url FROM lista WHERE id = %s;";
        $query = "SELECT listam3u.url FROM listam3u INNER JOIN lista ON lista.idm3u = listam3u.id WHERE lista.Id = %s;";
        $url = $this->DbQuery(sprintf($query, $idLista));
        if (count($url) > 0) {
            $result = $url[0];
            foreach ($result as $res) {
                return $res;
            }
        }
        return false;
    }


    public function getListaGruposSeleccionados($idLista) {
        $bdGrupos = $this->ObtenerGruposSeleccionados($idLista);
        $grupos = array();
        foreach ($bdGrupos as $bdGrupo) {
            $grupos[$bdGrupo->titulogrupo] = "'".$bdGrupo->titulogrupo."'";
        }
        return $grupos;
    }


    private function ObtenerGruposSeleccionados($idLista) {
        $query = "SELECT titulogrupo FROM gruposeleccionado WHERE idLista = %s";
        return $this->DbQuery(sprintf($query, $idLista));
    }
    
    public function GuardarGruposSeleccionados (array $grupos, string $idLista, string $idListaM3u) {
        $gruposString = "('%s', %s, %s)";
        $insertValues = "";
        for ($i = 0; $i < count($grupos); $i++){
            $insertValues .= sprintf($gruposString, $grupos[$i], $idLista, $idListaM3u);
            if ($i < count($grupos) - 1) {
                $insertValues .= ", ";
            }
        }
        
        // Actualizamos la lista de grupos
        $query = "UPDATE lista SET idm3u = %s WHERE id = %s;";
        $this->DbQuery(sprintf($query, $idListaM3u, $idLista));
        // Borramos los grupos actuales.
        $query = "DELETE FROM gruposeleccionado WHERE idLista = %s;";
        $this->DbQuery(sprintf($query, $idLista));
        // Grabamos los nuevos grupos.
        $query = "INSERT INTO gruposeleccionado (titulogrupo, idLista, idlistam3u) VALUES ".$insertValues.";";
        return $this->DbQuery($query);
    }

    public function OkUser($user, $pass) {
        $query = "SELECT COUNT(1) AS num FROM users WHERE user = '%s' AND pass = '%s';";
        $query = sprintf($query, $user, $pass);
        $num = $this->DbQuery($query);
        if (count($num) > 0) {
            $result = $num[0];
            foreach ($result as $res) {
                if (intval($res) === 1) {
                    return true;
                }
            }
        }
        return false;
    }
}