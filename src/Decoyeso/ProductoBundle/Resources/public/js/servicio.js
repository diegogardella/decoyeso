

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
				var text=$('#servicio_insumos').val()+";"+this.id+"@"+this.cantidad;
				$('#servicio_insumos').val(text);
				
				$('#idSelectInsumo :selected').remove();
				$('#idInsumoCantidad').val('')
				
				if($('#idSelectInsumo option').length<1){
					$('#idSelectInsumo').append(new Option('No existe insumos para agregar', 0));
				}
				

			}
			
			
			
		},
		validar:function(){

			var b=true;
			
			if(this.id==0){
				alert('No hay insumo para agregar')
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
			
			var ids=$('#servicio_insumos').val().split(';');
			$('#servicio_insumos').val('');
			
			for(var j=1;j<ids.length;j++){
				idItem=ids[j].split('@');
				if(idItem[0]!=this.id){
					var text=$('#servicio_insumos').val()+";"+ids[j];
					$('#servicio_insumos').val(text);
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



var productoObject={
		
		id:0,
		nombre:'',
		cantidad:0,
		
		agregar:function(){
			
			this.id=$('#idSelectProducto :selected').val();
			this.nombre=$('#idSelectProducto :selected').html();
			this.cantidad=$('#idProductoCantidad').val();
			
			if(this.validar()){
				
				$('#tableListProductos').append('<tr id="pro_tr_'+this.id+'"><td id="pro_nom_'+this.id+'">'+this.nombre+'</td><td id="pro_cant_'+this.id+'">'+this.cantidad+'</td><td><button class="eliminarProductoDeLista" type="button" value="'+this.id+'">X</button></td></tr>');
				var text=$('#servicio_productos').val()+";"+this.id+"@"+this.cantidad;
				$('#servicio_productos').val(text);
				
				$('#idSelectProducto :selected').remove();
				$('#idProductoCantidad').val('')
				
				if($('#idSelectProducto option').length<1){
					$('#idSelectProducto').append(new Option('No existen productos para agregar', 0));
				}
				

			}
			
			
			
		},
		validar:function(){

			var b=true;
			
			if(this.id==0){
				alert('No existen productos para agregar')
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
			this.nombre=$('#pro_nom_'+id).html();
			this.cantidad=$('#pro_cant_'+id).html();
			
			if($('#idSelectProducto option').length==1 && $('#idSelectProducto option').eq(0).val()==0){
				$('#idSelectProducto option').eq(0).remove();
			}
			
			$('#pro_tr_'+id).remove();
			$('#idSelectProducto').append(new Option(this.nombre, this.id));
			
			var ids=$('#servicio_productos').val().split(';');
			$('#servicio_productos').val('');
			
			for(var j=1;j<ids.length;j++){
				idItem=ids[j].split('@');
				if(idItem[0]!=this.id){
					var text=$('#servicio_productos').val()+";"+ids[j];
					$('#servicio_productos').val(text);
				}
			}

		}
}


$('#idAgregarProducto').click(function(){
	productoObject.agregar();
})

$('.eliminarProductoDeLista').live('click',function(){
	var id=$(this).val();
	productoObject.eliminar(id);
})


