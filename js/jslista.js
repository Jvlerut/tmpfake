var idTimeOut = 0;
function cargarGrupos() {
    // Se cargan los grupos de la lista seleccionada.
    lista = $("#idListaM3U option:selected").text();
    myurl = $("#idListaM3U option:selected").text();
    //alert("Lista: " + lista + ",   URL: " + myurl);
    $.ajax({
        url: 'Cargador.php',
        async: true,
        type: 'GET',
        datatype: 'json',
        data: {funcion:'ImprimirGrupos', lista: lista, url:myurl}
    })
        .done(function(html) {
            $('#divGrupos').html(html);
        });
}

function cargaCanales(idGrupo) {
    $.ajax({
        url: 'Cargador.php',
        async: true,
        type: 'GET',
        datatype: 'json',
        data: {funcion:'ImprimirCanales', id:idGrupo}
    })
        .done(function(html) {
            $('#divCanales').html(html);
        });
}

function cargaListaCanales() {
    $.ajax({
        url: 'Cargador.php',
        async: true,
        type: 'GET',
        datatype: 'json',
        data: {funcion:'IMPRIMIRLISTASCANALES'}
    })
        .done(function(html) {
            $('#divListas').html(html);
        });
}

function cargaListaM3u() {
        $.ajax({
        url: 'Cargador.php',
        async: true,
        type: 'GET',
        datatype: 'json',
        data: {funcion:'ImprimirListasM3u'}
    })
        .done(function(html) {
            $('#divlistasm3u').html(html);
        });
    
}

function socketPorcentaje(bloque) {
    $.ajax({
        url: 'SocketCliente.php',
        async: true,
        type: 'GET',
        datatype: 'json',
        data: {funcion:'leer'}
    })
    .done(function(percent) {
        if (percent >= 0) {
            $(bloque).html('<p>' + percent + '</p>');
        }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        var out = '';
        for (var p in jqXHR) {
            out += p + ': ' + jqXHR[p] + '\n';
        }
        alert("Error SockectCliente.LeerPorcentajeSocket: " + textStatus);
    });
}

function leerSocket(bloque) {
    socketPorcentaje(bloque);
    idTimeOut = setTimeout(leerSocket, 100, bloque);
}cargarGrupos

function progressBarInicio(bloque, conProgreso = true) {
    $(bloque).show();
    if (conProgreso == true) {
        leerSocket(bloque);
    }
}

function progressBarFinal(bloque, conProgreso = true) {
    if(conProgreso == true) {
        clearTimeout(idTimeOut); // Se cancela el bucle para mostrar el progreso.
    }
    $(bloque).hide();
}

function guardarGruposSeleccionados() {
    var grupos = "";
    $("input[name=bloqueGrupos]").each(function (index) {
        if($(this).is(':checked')){
            grupos = grupos + $(this).attr("id") + "\n";
        }
    });
    // Se graban los grupos de la lista seleccionada.
    lista = $('input:radio[name=radiosListas]:checked').attr('id');
    var idListaM3u = $("#idListaM3U :selected").val();
    $.ajax({
        url: 'GenerarLista.php',
        async: true,
        type: 'GET',
        datatype: 'json',
        data: {funcion:'grabarGruposSeleccionados', grupos:grupos, lista:lista, idListaM3u:idListaM3u}
    })
        .done(function(resultado){
            cargarGruposLista(lista);
        });
}

function cargarGruposLista(idLista) {
    $.ajax({
        url: 'GenerarLista.php',
        async: true,
        type: 'GET',
        datatype: 'json',
        data: {funcion:'cargarGruposSeleccionados', lista:idLista}
    })
        .done(function(html){
            $("#divGruposSeleccionados").html(html);
        });
}


$(document).ready(function() {
    cargaListaCanales();
    cargaListaM3u();
   // cargarGrupos("");
})

