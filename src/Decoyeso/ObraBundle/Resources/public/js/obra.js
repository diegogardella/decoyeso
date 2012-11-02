
$(window).ready(function(){

	$('#obraDatosBtn').click(function(){
			$('#datosObraContenido').css('display','');
			$('#solicitudMovimientoContenido').css('display','none');
	});
	
	$('#solicitudMovimientosBtn').click(function(){
			$('#datosObraContenido').css('display','none');
			$('#solicitudMovimientoContenido').css('display','');
	});
			
});
	
		
	