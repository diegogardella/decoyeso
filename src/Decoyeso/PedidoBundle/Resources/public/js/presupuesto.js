$(document).ready(function(){
	
	//Mostrar/Ocultar Columnas
	$("#decoyeso_pedidobundle_presupuestotype_mostrarColumnas input").live('click onLoad',function(index) {
		if($(this).is(':checked')) {  
			$(".NpresupuestoCol"+$(this).val()).removeClass("NdisplayNone");
        }
		else {
			$(".NpresupuestoCol"+$(this).val()).addClass("NdisplayNone");
		}
	});
	$("#decoyeso_pedidobundle_presupuestotype_mostrarColumnas input").trigger('onLoad');
	
	
	//Pintar casilleros
	$(".checkCasillero").live('click onLoad',function() {
		if($(this).is(':checked')) {  
			$("#NpresupuestoTr"+$(this).val()).addClass("NfondoGris");
        }
		else {
			
			$("#NpresupuestoTr"+$(this).val()).removeClass("NfondoGris");
		}
	});
	$(".checkCasillero").trigger('onLoad');
	
	function contarFilas () {
		var cantidad = 0;
		$(".checkCasillero").each(function() {
			if (parseFloat($(this).val()) > parseFloat(cantidad))
			cantidad = parseFloat($(this).val());
		});
		return (parseFloat(cantidad) +1) ;
	}
	
	// Agrega un fila
	$(".NbtnAgregarFila").live('click', function(){
		
		var numFila = contarFilas();
		
		$('#tablaItemsPresupuesto').append('<tr class="NpresupuestoTr" id="NpresupuestoTr'+numFila+'"><td class=""><input type="checkbox"  name="check_'+numFila+'" value ="'+numFila+'" class="checkCasillero"></td><td class="NpresupuestoCol0"><input type="text"  name="designacion_'+numFila+'" class="inputLargo "></td><td class="NpresupuestoCol1"><input type="text" name="unidad_'+numFila+'" class="inputCorto"></td><td class="NpresupuestoCol2"><input type="text" name="cantidad_'+numFila+'" class="inputCorto inputCantidad"></td><td class="NpresupuestoCol3"><input type="text" name="precioUnitario_'+numFila+'" class="inputCorto precioUnitario"></td><td class="NpresupuestoCol4"><input type="text" name="precioVtaSinIva_'+numFila+'" class="inputCorto "></td><td class="NpresupuestoCol5"><input type="text" name="precioVtaConIva_'+numFila+'" class="inputCorto "></td><td class="NpresupuestoCol6"><input type="text" name="precioTotal_'+numFila+'" class="inputCorto inputPrecioTotal"></td><td class=""><a href="javascript:void(0);" class="linkEliminarFila" id="linkEliminarFila_'+numFila+'" ></a></td></tr>');
		
		//oculto las columnas no seleccionadas
		$("#decoyeso_pedidobundle_presupuestotype_mostrarColumnas input").trigger('onLoad');
		
	});
	
	$(".linkEliminarFila").live('click',function(){
		
		var i = $(this).attr("id").split("_");
		var index = i[1];
		
		if($(".NpresupuestoTr").length < 2) return;
		$("#NpresupuestoTr"+index).remove();
		
	});
		
	
	
	$("#presupuestoForm").submit(function(){
		$("#numFilas").val(contarFilas());
	});

	
		
	//Operacion CANTIDAD * PRECIO UNIT. 
	$(".inputCantidad, .precioUnitario").live('change onLoad', function(index, el) {
		
		var inputs = new Array();
		inputs['inputCantidad'] = new Array(); 
		inputs['precioUnitario'] = new Array();
		
		$(".inputCantidad").each(function(index) {
			if ($(this).val() != "" && $.isNumeric($(this).val()))
				inputs["inputCantidad"][index] = parseFloat($(this).val());
		});
		$(".precioUnitario").each(function(index) {
			if ($(this).val() != "" && $.isNumeric($(this).val()))
				inputs["precioUnitario"][index] = parseFloat($(this).val());
		});
		
		$(".inputPrecioTotal").each(function(index) {
			if ($.isNumeric(inputs["inputCantidad"][index]) && $.isNumeric(inputs["precioUnitario"][index])) {
				$(this).val(inputs["inputCantidad"][index] * inputs["precioUnitario"][index])
				$(this).change();	
			}
		});
		
	});
	
	//Sumas de casillas PRECIO TOTAL
	$(".inputPrecioTotal").live('change',function(){
		
		var subTotal = 0;
		$(".inputPrecioTotal").each(function() {
			if ($(this).val() != "") {
				if ($.isNumeric($(this).val())) {
					subTotal = subTotal + parseFloat($(this).val());
				}
				else { 
					alert ("El valor "+$(this).val()+" no se puede sumar. Por favor reviselo.");
				}
			}
		});
		$("#decoyeso_pedidobundle_presupuestotype_subTotal, #decoyeso_pedidobundle_presupuestotype_total").val(subTotal);
		
	});
	
	//Calculo de casilla saldo
	$("#decoyeso_pedidobundle_presupuestotype_total, #decoyeso_pedidobundle_presupuestotype_precioEntrega").live('change',function(){
		
		if ( 
			$("#decoyeso_pedidobundle_presupuestotype_total").val() != "" &&
			$.isNumeric($("#decoyeso_pedidobundle_presupuestotype_total").val()) &&
			
			$("#decoyeso_pedidobundle_presupuestotype_precioEntrega").val() != "" &&
			$.isNumeric($("#decoyeso_pedidobundle_presupuestotype_precioEntrega").val()) 
		) 
		
		
		{
			$("#decoyeso_pedidobundle_presupuestotype_precioSaldo").val(
					parseFloat($("#decoyeso_pedidobundle_presupuestotype_total").val()) - 
					parseFloat($("#decoyeso_pedidobundle_presupuestotype_precioEntrega").val())
					);
		}
		else {
			$("#decoyeso_pedidobundle_presupuestotype_precioSaldo").val("");
		}
			
	});
	
	

	

	
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
	