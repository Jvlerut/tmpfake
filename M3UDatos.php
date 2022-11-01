<?php
include_once("M3UDatos.php");
include_once("BBDD.php");
abstract class ListaM3UBase {
    protected $lista = array();

    protected $consulta;

    public function getLista() {return $this->lista;}
    public function setLista($value) {$this->lista = $value;}

    protected function Ordenar() {
        ksort($this->lista, SORT_STRING );
    }

    // Añadimos un elemento, si no existe previamente, a la lista.
    protected function AddElemento($key, ListaM3UBase $Elemento) {
        if (!key_exists($key, $this->lista)) {
            $this->lista[$key] = $Elemento;
        }
        return $this->lista[$key];
    }

   // public abstract function Add(ListaM3UBase $Elemento);

    protected function existeElemento($keyElemento) {
        if (array_key_exists($keyElemento, $this->lista)) {
            return $this->lista[$keyElemento];
        }
        return false;
    }

    protected function __construct()
    {
        //$consulta = new ConsultasLista();
    }


}

class ListaM3U extends ListaM3UBase{

    private $nombre;
    private $url = "http://hz01.oknirvana.club:8880/get.php?username=AALM3620&password=5283363929&type=m3u_plus&output=mpegts";

    //private $url = "http://ott4k.me:80/get.php?username=50603751008547&password=23845830885103&type=m3u_plus&output=ts";
    //private static $defaultUrl = "http://ott4k.me:80/get.php?username=50603751008547&password=23845830885103&type=m3u_plus&output=ts";
   // private static $defaultUrl = "c:/tv_channels.m3u";
    private static $defaultUrl = "http://hz01.oknirvana.club:8880/get.php?username=AALM3620&password=5283363929&type=m3u_plus&output=mpegts";

    //private $url = "c:/tv_channels_50603751008547_plus.m3u";

    public static function getDefaultUrl() {
        return self::$defaultUrl;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function setUrl($value) {
        $this->url = $value;
    }
    public function getUrl() {return $this->url;}
    public function setNombre($value) {
        $this->nombre = $value;
    }
    public function getNombre() {
        return $this->nombre;
    }
}

class Grupo extends ListaM3UBase
{
    public $nombre;
    public $logo;
    public $titulo;

    function __construct($nombre, $logo, $titulo)
    {
        $this->nombre = $nombre;
        $this->logo = $logo;
        $this->titulo = $titulo;
        parent::__construct();
    }

    private function Grabar()
    {
        // Grabar lista de canales
        foreach ($this->lista as $elemento) {
            $elemento->Grabar();
        }
    }
}

class Canal extends ListaM3UBase{
    public $Id;
    public $Info;
    public $Descripcion;
    public $Nombre;
    public $TituloGrupo;
    public $Titulo;
    public $Logo;
    public $Shift;
    public $Audio;
    public $ExtInf;
    public $Url;
    function __construct($Id, $Info, $Descripcion,  $Nombre, $TituloGrupo, $Titulo, $Logo, $Shift, $Audio, $ExtInf, $Url) {
        $this->Id = $Id;
        $this->Info = $Info;
        $this->Descripcion = $Descripcion;
        $this->Nombre = $Nombre;
        $this->TituloGrupo = $TituloGrupo;
        $this->Titulo = $Titulo;
        $this->Logo = $Logo;
        $this->Shift = $Shift;
        $this->Audio = $Audio;
        $this->ExtInf = $ExtInf;
        $this->Url = $Url;
        parent::__construct();
    }
    private function Grabar() {
        $query = "INSERT INTO canal (";
        $query .= "descripcion, info, extinf, grouptitle, titulo, tvgid, tvgname, tvglogo, tvgshift, audiotrack, url";
        $query .= ") \n";
        $query .= "VALUES (";
        $query .= "'".$this->DbConexion->real_escape_string($this->Descripcion)."', ";
        $query .= "'".$this->DbConexion->real_escape_string($this->Info)."', ";
        $query .= "".$this->ExtInf.", ";
        $query .= "'".$this->DbConexion->real_escape_string($this->TituloGrupo)."', ";
        $query .= "'".$this->DbConexion->real_escape_string($this->Titulo)."', ";
        $query .= "'".$this->DbConexion->real_escape_string($this->Id)."', ";
        $query .= "'".$this->DbConexion->real_escape_string($this->Nombre)."', ";
        $query .= "'".$this->DbConexion->real_escape_string($this->Logo)."', ";
        $query .= "'".$this->DbConexion->real_escape_string($this->Shift)."', ";
        $query .= "'".$this->DbConexion->real_escape_string($this->Audio)."', ";
        $query .= "'".$this->DbConexion->real_escape_string($this->Url)."'";
        $query .= "); \n";
        return $this->consulta->DbQuery($query);
    }


}