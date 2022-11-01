<?php
include_once("M3UDatos.php");
include_once("SocketServidor.php");

class TokenM3U {
    private const EXTINF = "#extinf";
    private const TVGID = "tvg-id";
    private const TVGNAME = "tvg-name";
    private const TVGSHIFT = "tvg-shift";
    private const TVGLOGO = "tvg-logo";
    private const GROUPTITLE = "group-title";
    private const AUDIOTRACK = "audio-track";
    private $tokenId;
    private $tokenName;
    private $tokenGroupTitle;
    private $tokenLogo;
    private $tokenShift;
    private $tokenAudio;
    private $tokenExtInf;
    private $tokenUrl;
    private $tokenTitulo;

    private $tokenCargado = false;

    private function getToken($cadena, $token) {
        $nPosToken = strpos(strtolower($cadena), $token, 0);
        if ($nPosToken >= 0) {
            // Se ha encontrado el token en la cadena.
            $nPosValorInicio = strpos($cadena, "\"", $nPosToken) + 1;
            $nPosValorFin = strpos($cadena, "\"", $nPosValorInicio) ;
            return substr($cadena, $nPosValorInicio, $nPosValorFin - $nPosValorInicio);
        }
    }


    private function cargarTokens($cadena) {
        // Token EXTINF
        $inicioExtInf = strpos($cadena, ":", strpos($cadena, self::EXTINF, 0)) + 1;
        $finExtInf = strpos($cadena, " ", $inicioExtInf);
        $this->tokenExtInf = substr($cadena, $inicioExtInf, $finExtInf - $inicioExtInf);

        // Token Titulo grupo
        $this->tokenGroupTitle = $this->getToken($cadena, self::GROUPTITLE);
        // Token Logo canal
        $this->tokenLogo = $this->getToken($cadena, self::TVGLOGO);
        // Token Nombre canal
        $this->tokenName = $this->getToken($cadena, self::TVGNAME);
        // Token Duración track audio
        $this->tokenAudio = $this->getToken($cadena, self::AUDIOTRACK);
        // Token id guía
        $this->tokenShift = $this->getToken($cadena, self::TVGSHIFT);
        // Token id canal.
        $this->tokenId = $this->getToken($cadena, self::TVGID);
        // Token Titulo.
        $this->tokenTitulo = substr($cadena, strrpos($cadena, ",") + 1, strlen($cadena) - strrpos($cadena, ","));
    }

    private function cargarUrl($cadena) {
        // Token Url canal.
        $this->tokenUrl = $cadena;
    }

    public function cargarLinea($cadena) {
        $posInicioLinea = strpos(strtolower($cadena), self::EXTINF,  0);
        if ($posInicioLinea !== false) {
            $this->cargarTokens($cadena);
        } else {
            $this->cargarUrl($cadena);
            $this->tokenCargado = true; // El token está cargado por completo.
        }
    }

    public function estaCompleto() {
        return $this->tokenCargado;
    }

    public function getCanal() {
        return new Canal($this->tokenId, "", "", $this->tokenName, $this->tokenGroupTitle, $this->tokenTitulo, $this->tokenLogo, $this->tokenShift, $this->tokenAudio, $this->tokenExtInf, $this->tokenUrl);
    }


}



class M3U
{
    public const CABECERAM3U = "#EXTM3U";
    public const MODOFICHERO = "r";
    public const MAXLENGTH = 1024;
    public const MAXEXECUTIONTIME = 1200;

    private function getCanalLinea($arrayLineas, $numLinea)
    {
        // Cada canal son 2 líneas.
        $token = new TokenM3U();
        $token->cargarLinea($arrayLineas[$numLinea]);
        @$token->cargarLinea($arrayLineas[$numLinea + 1]);
        return $token->getCanal();
    }

    public function cargarFicheroGrupos($idLista, $rutaFichero)
    {
        $previusExecutionTime = ini_get('max_execution_time');
        ini_set('max_execution_time', self::MAXEXECUTIONTIME);
        $arrayGrupos = array();

        $ficheroLista = file($rutaFichero);
        $numLineas = count($ficheroLista);


        if (strpos($ficheroLista[0], self::CABECERAM3U, 0) >= 0) {
            // La primera línea indica se trata de una lista.
            for ($i = 1; $i < count($ficheroLista); $i += 2) {
                $canalTmp = $this->getCanalLinea($ficheroLista, $i);
                if (!array_key_exists($canalTmp->TituloGrupo, $arrayGrupos)) {
                    $arrayGrupos[$canalTmp->TituloGrupo] = $canalTmp->TituloGrupo;
                }
            }
        }
        ini_set('max_execution_time', $previusExecutionTime);
        return $arrayGrupos;
    }

    public function cargarFicheroCanalesPorGrupos(string $rutaFichero, int $idLista) {
        // Recorremos todo el fichero buscando los canales de los grupos indicados.
        $previusExecutionTime = ini_get('max_execution_time');
        ini_set('max_execution_time', self::MAXEXECUTIONTIME);
        $consulta = new ConsultasLista();
        $arrayGrupos = $consulta->getListaGruposSeleccionados($idLista);
        $ficheroLista = file($rutaFichero);
        $numLineas = count($ficheroLista);
        $arrayCanales = array();
        if (strpos($ficheroLista[0], self::CABECERAM3U, 0) >= 0) {
            // La primera línea indica se trata de una lista.
            for ($i = 1; $i < count($ficheroLista); $i += 2) {
                $canalTmp = $this->getCanalLinea($ficheroLista, $i);
                if (array_key_exists($canalTmp->TituloGrupo, $arrayGrupos)) {
                    // El grupo del canal actual está incluido en el array de grupos.
                    // Lo añadimos a la lista de canales a generar.
                    $arrayCanales[] = $canalTmp;
                }
            }
        }
        ini_set('max_execution_time', $previusExecutionTime);
        return $arrayCanales;
    }

    public function cargarListas() {
        $query = "SELECT id, descripcion, info, url FROM lista;";
        $consulta = new ConsultasLista();
        $result = $consulta->DbQuery($query);
        return $result;
    }
    
    public function cargarListasM3u() {
        $query = "SELECT id, nombre, url FROM listam3u;";
        $consulta = new ConsultasLista();
        $result = $consulta->DbQuery($query);
        return $result;
    }

}

