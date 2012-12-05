

var insumoObject={
		
		id:0,
		costo:0,
		unidad:0,				
		nombre:'',
		cantidad:0,
		
		agregar:function(){
			
			var datosOption=$('#idSelectInsumo option:selected').val().split('@');
			
			this.id=datosOption[0];
			this.costo=datosOption[1];
			this.unidad=datosOption[2];
			
			
			this.nombre=$('#idSelectInsumo :selected').html();
			this.cantidad=$('#idInsumoCantidad').val();
			
			if(this.validar()){
				
				$('#tableListInsumos').append('<tr id="ins_tr_'+this.id+'"><td id="ins_nom_'+this.id+'">'+this.nombre+'</td><td align="center"  id="ins_cant_'+this.id+'">'+this.cantidad+' '+this.unidad+'</td><td align="center"  id="ins_cost_'+this.id+'">'+this.costo+'</td><td align="center"  ><button class="eliminarInsumoDeLista" type="button" value="'+this.id+'">X</button></td></tr>');
				var text=$('#producto_insumos').val()+";"+this.id+"@"+this.cantidad+"@"+this.costo+"@"+this.unidad+"@"+this.nombre;
				$('#producto_insumos').val(text);
				
				$('#idSelectInsumo :selected').remove();
				$('#idInsumoCantidad').val('')
				
				if($('#idSelectInsumo option').length<1){
					$('#idSelectInsumo').append(new Option('No hay insumos para agregar', '0@@@'));
				}
			}
			
			this.mostrarCosto();
			
			var costoSugerido=parseInt($('#idCostoSugerido').html())
			$('#idCostoSugerido').html(costoSugerido+(this.cantidad*this.costo))
			
			
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
			
			if($('#idSelectInsumo option').length==1 && $('#idSelectInsumo option').eq(0).val()=='0@@@'){
				$('#idSelectInsumo option').eq(0).remove();
			}
			
			$('#ins_tr_'+id).remove();
			
			
			var ids=$('#producto_insumos').val().split(';');
			$('#producto_insumos').val('');
			
			for(var j=1;j<ids.length;j++){
				idItem=ids[j].split('@');
				if(idItem[0]!=this.id){
					var text=$('#producto_insumos').val()+";"+ids[j];
					$('#producto_insumos').val(text);
				}else{
					$('#idSelectInsumo').append(new Option(this.nombre, this.id+"@"+idItem[2]+"@"+idItem[3]));
					var costoSugerido=parseFloat($('#idCostoSugerido').html())
					$('#idCostoSugerido').html(costoSugerido-(parseFloat(idItem[1])*parseFloat(idItem[2])));
				}
			}
			
			this.mostrarCosto();
			
			

		},
		mostrarCosto:function(){
			var datosOption=$('#idSelectInsumo option:selected').val().split('@');
			$('#idCostoInsumoAgergar').html(datosOption[1]);
		}
}

$('#idAgregarInsumo').click(function(){
	insumoObject.agregar();
})

$('.eliminarInsumoDeLista').live('click',function(){
	var id=$(this).val();
	insumoObject.eliminar(id);
})

$('#idSelectInsumo').change(function(){
	insumoObject.mostrarCosto();
});

insumoObject.mostrarCosto();




var productoObject={
		
		id:0,
		costo:0,
		unidad:0,		
		nombre:'',
		cantidad:0,
		
		agregar:function(){
			
			var datosOption=$('#idSelectProducto option:selected').val().split('@');
			
			this.id=datosOption[0];
			this.costo=datosOption[1];
			this.unidad=datosOption[2];
			
			this.nombre=$('#idSelectProducto option:selected').html();
			this.cantidad=$('#idProductoCantidad').val();
			
			if(this.validar()){
				
				$('#tableListProductos').append('<tr id="pro_tr_'+this.id+'"><td id="pro_nom_'+this.id+'">'+this.nombre+'</td><td align="center"  id="pro_cant_'+this.id+'">'+this.cantidad+' '+this.unidad+'</td><td align="center"  id="pro_cost_'+this.id+'">'+this.costo+'</td><td align="center" ><button class="eliminarProductoDeLista" type="button" value="'+this.id+'">X</button></td></tr>');
				var text=$('#producto_productos').val()+";"+this.id+"@"+this.cantidad+"@"+this.costo+"@"+this.unidad+"@"+this.nombre;
				$('#producto_productos').val(text);
				
				$('#idSelectProducto option:selected').remove();
				$('#idProductoCantidad').val('')
				
				if($('#idSelectProducto option').length<1){
					$('#idSelectProducto').append(new Option('No existen productos para agregar','0@@@'));
				}

			}
			
			this.mostrarCosto();

			var costoSugerido=parseFloat($('#idCostoSugerido').html())
			$('#idCostoSugerido').html(costoSugerido+(this.cantidad*this.costo))
			
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
			
			if($('#idSelectProducto option').length==1 && $('#idSelectProducto option').eq(0).val()=='0@@@'){
				$('#idSelectProducto option').eq(0).remove();
			}
			
			$('#pro_tr_'+id).remove();
			
			
			var ids=$('#producto_productos').val().split(';');
			$('#producto_productos').val('');
			
			for(var j=1;j<ids.length;j++){
				idItem=ids[j].split('@');
				if(idItem[0]!=this.id){
					var text=$('#producto_productos').val()+";"+ids[j];
					$('#producto_productos').val(text);
				}else{
					$('#idSelectProducto').append(new Option(this.nombre, this.id+"@"+idItem[2]+"@"+idItem[3]));
					var costoSugerido=parseFloat($('#idCostoSugerido').html())
					$('#idCostoSugerido').html(costoSugerido-(parseFloat(idItem[1])*parseFloat(idItem[2])));
				}
			}
			
			this.mostrarCosto();
			

		},
		mostrarCosto:function(){
			var datosOption=$('#idSelectProducto option:selected').val().split('@');
			$('#idCostoProductoAgergar').html(datosOption[1]);
		}
}


$('#idAgregarProducto').click(function(){
	productoObject.agregar();
})

$('.eliminarProductoDeLista').live('click',function(){
	var id=$(this).val();
	productoObject.eliminar(id);
})

$('#idSelectProducto').change(function(){
	productoObject.mostrarCosto();
});

productoObject.mostrarCosto();