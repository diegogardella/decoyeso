$(document).ready(function(){
	
		
	
	function contarFilas () {
		var cantidad = 0;
		$(".inputDesignaciones").each(function() {
			var i = $(this).attr("id").split("_");
			var index = i[1];
			if (parseFloat(index) > parseFloat(cantidad))
			cantidad = parseFloat(index);
		});
		//alert(cantidad);
		return (parseFloat(cantidad) +1) ;
		
	}
	
	// Agrega un fila
	/*
	$(".NbtnAgregarFila").live('click', function(){
		
		var numFila = contarFilas();
		
		$('#tablaItems').append('');
		
			
		
	});
	*/
	
	//Eliminar Filas
	$(".linkEliminarFila").live('click',function(){

		var i = $(this).attr("id").split("_");
		var index = i[1];
		
		if($(".NItemsTr").length < 2) return;
		$("#NItemsTr"+index).remove();
		
	});
		
	
	//Enviar Form
	$("#procesoForm").submit(function(e) {
		
		$("#numFilas").val(contarFilas());
		
		
		return validarForm();
		
	
	});
	
	
	function validarForm() {
		
		var res = true;
		
		//Me fijo si ha seleccionado algun producto
		var seleccionoProducto = 0;
		$(".inputDesignaciones" ).each(function() {
			if ($(this).val() != 0) seleccionoProducto = $(this).val();
		});
		if (seleccionoProducto == 0) {
			alert ("Por favor seleccione algun producto");
			return false;
			
		}
		//Me fijo si ingreso cantidad cuando selecciona un producto
		$(".inputDesignaciones" ).each(function() {
			
			if ($(this).val() != 0) {
				var i = $(this).attr("id").split("_");
				var index = i[1];
				
				if (!$.isNumeric($("#cantidad_"+index).val()) || $("#cantidad_"+index).val() < 1 ){
					alert ("Por favor Ingrese la cantidad para el producto");
					res =   false;
					return false;
				}	
			}
			
			
		});
		
		return res;

		
		
	}

	/*
	$( ".inputDesignaciones" ).autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: urlAjaxItemsPresupuesto,
					//dataType: "jsonp",
					data: {term: request.term},
					success: function( data ) {
						response(jQuery.map(data, function(n){
							
							return {
								label: n.designacion,
								designacion: n.designacion,
								unidad: n.unidad,
								precio: n.precio,
								id: n.id
							}
						}));
				}
			});
			},
			select: function( event, ui ) {
				var i = $(this).attr("name").split("_");
				var index = i[1];
				
				$("#unidad_"+index).val(ui.item.unidad);
				$("#precioUnitario_"+index).val(ui.item.precio);
				
			},
	});
	*/

	
	
	
	
	
	
});
		


/*
arr = jQuery.map(data, function(n, i){
    //console.log (n.designacion + i);
	return {
		label: n.designacion,
		value: n.designacion
	}
  });
*/

/*
response( $.map( data.itemnames, function( item ) {
	console.log(item);
	
	return {
		label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
		value: item.name
	}
	
}));
*/
/**/
	