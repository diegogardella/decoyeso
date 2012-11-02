
var elementoObject={
		
		id:0,
		unidad:0,		
		nombre:'',
		cantidad:0,
		
		agregar:function(){
			
			var datosOption=$('#idSelectElemento option:selected').val().split('@');
			
			this.id=datosOption[0];
			this.unidad=datosOption[1];
			
			this.nombre=$('#idSelectElemento option:selected').html();
			this.cantidad=$('#idElementoCantidad').val();
			
			if(this.validar()){
				
				$('#tableListElementos').append('<tr id="ele_tr_'+this.id+'"><td id="ele_nom_'+this.id+'">'+this.nombre+'</td><td id="ele_cant_'+this.id+'">'+this.cantidad+' '+this.unidad+'</td><td><button class="eliminarElementoDeLista" type="button" value="'+this.id+'">X</button></td></tr>');
				var text=$('#solicitudmovimiento_elementos').val()+";"+this.id+"@"+this.cantidad+"@"+this.unidad+"@"+this.nombre;
				$('#solicitudmovimiento_elementos').val(text);
				
				$('#idSelectElemento option:selected').remove();
				$('#idElementoCantidad').val('')
				
				if($('#idSelectElemento option').length<1){
					$('#idSelectElemento').append(new Option('No existen productos o insumos para agregar','0@@@'));
				}

			}
			
		},
		validar:function(){

			var b=true;
			
			if(this.id==0){
				alert('No existen productos o insumos para agregar')
				b=false;
			}
			
			if($.isNumeric(this.cantidad)==false && b==true){
				alert('El valor de cantidad es incorrecto')
				b=false;
			}
			
			return b;

		},
		eliminar:function(id){
			
			this.id=id;
			this.nombre=$('#ele_nom_'+id).html();
			this.cantidad=$('#ele_cant_'+id).html();
			
			if($('#idSelectElemento option').length==1 && $('#idSelectElemento option').eq(0).val()=='0@@@'){
				$('#idSelectElemento option').eq(0).remove();
			}
			
			$('#ele_tr_'+id).remove();
			
			
			var ids=$('#solicitudmovimiento_elementos').val().split(';');
			$('#solicitudmovimiento_elementos').val('');
			
			for(var j=1;j<ids.length;j++){
				idItem=ids[j].split('@');
				if(idItem[0]!=this.id){
					var text=$('#solicitudmovimiento_elementos').val()+";"+ids[j];
					$('#solicitudmovimiento_elementos').val(text);
				}else{
					$('#idSelectElemento').append(new Option(this.nombre, this.id+"@"+idItem[2]+"@"+idItem[3]));
				}
			}
			
		},
}


$('#idAgregarElemento').click(function(){
	elementoObject.agregar();
})

$('.eliminarElementoDeLista').live('click',function(){
	var id=$(this).val();
	elementoObject.eliminar(id);
})
