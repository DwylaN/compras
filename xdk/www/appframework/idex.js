$(window).on('load', function(){
	console.log("on")
    var dir = "http://localhost/example/api"
    $('#client').on('click', function(){clients()}) 
    $('#products').on('click', function(){products()}) 
    $('#sales').on('click', function(){sales()}) 

    function clients (e){
    	$.ajax({
            type: "GET",
            contentType: "application/json",
            dataType: "json",
            url: dir+"/clientes",
            beforeSend: function() {
                $('#clientes').html('<div id="afui_mask" class="ui-loader" style="z-index: 20000; display: block;"><span class="ui-icon ui-icon-loading spin"></span><h1>Loading Content</h1></div>')
            },
            success: function(message) {
                $('#clientes').empty()
                // var message = JSON.parse(message)
                for (i = 0; i < message.length; i++) {
                    $('<p />').text('Nombre: ' + message[i].nombre).appendTo('#clientes')
                    $('<p />').text('Telefono: ' + message[i].telefono).appendTo('#clientes')
                    $('<p />').text('<br><br>')
                    // $('<p />').text('Reportado el: ' + data.reports[a].date+'Usuario: '+ getCookie("useramtr")).appendTo('#mreports')                    
                }  
            },
            error: function(message) {
                return intel.xdk.notification.alert("Server error");
            }
        })
    } 
    function products (e){
    	$.ajax({
            type: "GET",
            contentType: "application/json",
            dataType: "json",
            url: dir+"/productos",
            beforeSend: function() {
                $('#productos').html('<div id="afui_mask" class="ui-loader" style="z-index: 20000; display: block;"><span class="ui-icon ui-icon-loading spin"></span><h1>Loading Content</h1></div>')
            },
            success: function(message) {
                $('#productos').empty()
                // var data = JSON.parse(a)
                for (i = 0; i < message.length; i++) {
                	console.log(message[i].nombre)
                    $('<p />').text('Nombre: ' + message[i].nombre).appendTo('#productos')
                    $('<p />').text('Marca: ' + message[i].marca).appendTo('#productos')
                    $('<p />').text('<br><br>')
                    // $('<p />').text('Reportado el: ' + data.reports[a].date+'Usuario: '+ getCookie("useramtr")).appendTo('#mreports')                    
                }  
            },
            error: function(message) {
                return intel.xdk.notification.alert("Server error");
            }
        })
    } 

    function sales (e){
    	$.ajax({
            type: "GET",
            contentType: "application/json",
            dataType: "json",
            url: dir+"/ventas",
            beforeSend: function() {
                $('#ventas').html('<div id="afui_mask" class="ui-loader" style="z-index: 20000; display: block;"><span class="ui-icon ui-icon-loading spin"></span><h1>Loading Content</h1></div>')
            },
            success: function(message) {
                $('#ventas').empty()
                // var data = JSON.parse(a)
                for (i = 0; i < message.length; i++) {
                    $('<p />').text('Id Cliente: ' + message[i].id_cliente).appendTo('#ventas')
                    $('<p />').text('Id Producto: ' + message[i].id_producto).appendTo('#ventas')
                    $('<p />').text('Fecha: ' + message[i].fecha).appendTo('#ventas')
                    $('<br>').text('<br><br><br><br>')
                    // $('<p />').text('Reportado el: ' + data.reports[a].date+'Usuario: '+ getCookie("useramtr")).appendTo('#mreports')                    
                }  
            },
            error: function(message) {
                return intel.xdk.notification.alert("Server error");
            }
        })
    }      

});