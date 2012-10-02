

$(window).load(function(){

	$('.list_delete_btn').click(function(){
		 
		var id=this.id.split("_");
		
		 if(confirm($('#'+id[0]+'_idMsjEliminar').html())){
			 $('#'+id[0]+'_idFormEliminar').submit();
		  }
		
	});

});






