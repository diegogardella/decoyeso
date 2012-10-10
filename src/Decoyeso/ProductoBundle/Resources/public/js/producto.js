

var insumoObject={
		
		id:0,
		nombre:'',
		cantidad:0,
		
		agregar:function(){
			
			this.id=$('#idSelectInsumo :selected').val();
			this.nombre=$('#idSelectInsumo :selected').html();
			this.cantidad=$('#idInsumoCantidad').val();
			
			if(this.validar()){
				
				$('#tableListInsumos').append('<tr id="ins_tr_'+this.id+'"><td id="ins_nom_'+this.id+'">'+this.nombre+'</td><td id="ins_cant_'+this.id+'">'+this.cantidad+'</td><td><button class="eliminarInsumoDeLista" type="button" value="'+this.id+'">X</button></td></tr>');
				var text=$('#producto_insumos').val()+";"+this.id+"@"+this.cantidad;
				$('#producto_insumos').val(text);
				
				$('#idSelectInsumo :selected').remove();
				$('#idInsumoCantidad').val('')
				
				if($('#idSelectInsumo option').length<1){
					$('#idSelectInsumo').append(new Option('No hay insumos para agregar', 0));
				}
				

			}
			
			console.log($('#producto_insumos').val())
			
		},
		validar:function(){

			var b=true;
			
			if(this.id==0){
				alert('No existen insumos para agregar')
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
			this.nombre=$('#ins_nom_'+id).html();
			this.cantidad=$('#ins_cant_'+id).html();
			
			if($('#idSelectInsumo option').length==1 && $('#idSelectInsumo option').eq(0).val()==0){
				$('#idSelectInsumo option').eq(0).remove();
			}
			
			$('#ins_tr_'+id).remove();
			$('#idSelectInsumo').append(new Option(this.nombre, this.id));
			
			var ids=$('#producto_insumos').val().split(';');
			$('#producto_insumos').val('');
			
			for(var j=1;j<ids.length;j++){
				idItem=ids[j].split('@');
				if(idItem[0]!=this.id){
					var text=$('#producto_insumos').val()+";"+ids[j];
					$('#producto_insumos').val(text);
				}
			}

		}
}


$('#idAgregarInsumo').click(function(){
	insumoObject.agregar();
})

$('.eliminarInsumoDeLista').live('click',function(){
	var id=$(this).val();
	insumoObject.eliminar(id);
})


