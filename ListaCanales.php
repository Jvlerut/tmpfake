<?php
session_start();
include_once("Cargador.php");
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="Carga.css"/>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jslista.js"></script>
</head>

<body>
    <div id="divGrupos">
        <div id = "divPorcentaje">
        </div>
    </div>
    <div id="divCanales">
        <div id = "divPorcentaje">
        </div>
    </div>
    <div id="divBotones">
        <div id="divListas">

        </div>
        <input type="button" value="Cargar lista" onclick="cargarGrupos('');"/>
        <input type="button" value="Grabar grupos" onclick="guardarGruposSeleccionados();"/>
        <p>Listas M3U diponibles:
            <div id="divlistasm3u">

            </div>
        </p>
    </div>
    <div id="divGruposSeleccionados">

    </div>
</body>

</html>

