$(document).ready(function(){

	//Eliminar Filas
	$("#formMovimientoStock").submit(function(e) {
		var res = true;
		if ($("#movimientostock_cantidad").val() != 0) {
				if (!$.isNumeric($("#movimientostock_cantidad").val()) || $("#movimientostock_cantidad").val() < 1 ){
					alert ("Debe ingresar la cantidad a ingresar del producto.");
					res =   false;
					return false;
				}	
			}
		return res;
		});
		
		

	
})
