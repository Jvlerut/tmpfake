<?php
class SocketServidor {
    private  $ip;
    private  $puerto = 32587;
    private  $socket;
    private const MAXCONEXIONES = 5;

    public function crearSocket() {
        //$this->ip = gethostbyname('localhost');
        $this->ip = gethostbyname($_SERVER['SERVER_NAME']);
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_bind($this->socket, $this->ip, $this->puerto);
        socket_listen($this->socket);
        socket_set_nonblock($this->socket);
    }

    public function cerrarSocket($socket = null) {
        if ($socket === null) {
        socket_close($this->socket);
        } else {
        socket_close($socket);
        }
    }

    public function conexionCliente($value) {
        if (($socketCliente = socket_accept($this->socket)) !== false) {
            socket_write($socketCliente, $value, strlen($value));
            $this->cerrarSocket($socketCliente);
        }
    }

}