$(document).ready(function(){
	
	$("#btnEstadoSecadorPlacas").live('click',function(){
		$("#solapaEstadoPlacas").removeClass("NdisplayNone");
		$("#solapaEstadoMolduras").addClass("NdisplayNone");
	});
	
	$("#btnEstadoSecadorMolduras").live('click',function(){
		$("#solapaEstadoPlacas").addClass("NdisplayNone");
		$("#solapaEstadoMolduras").removeClass("NdisplayNone");
	});
	
});
		
