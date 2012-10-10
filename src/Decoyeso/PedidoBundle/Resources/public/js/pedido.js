
	
	$(window).ready(function(){

		
			
			function mostrarPedido(){
				$('#pedidoListarBtn').css('display','');
				$('#pedidoDatosBtn').css('display','none');
				$('#pedidoEliminarBtn').css('display','');
				
				$('#tituloContenido').html('Datos del Pedido');
				
				$('#msjInfo').css('display','');
				$('#datosPedidoContenido').css('display','');
					
			}
			
			function ocultarPedido(){
				$('#pedidoListarBtn').css('display','none');
				$('#pedidoDatosBtn').css('display','');
				$('#pedidoEliminarBtn').css('display','none');
				
				$('#msjInfo').css('display','none');
				$('#datosPedidoContenido').css('display','none');
			}
			
			function mostrarRelevamiento(){
				$('#pedidoRelevamientosCrearBtn').css('display','');
				$('#pedidoRelevamientosBtn').css('display','none');
				
				$('#tituloContenido').html('Relevamientos del Pedido');

				$('#relevamientosPedidoContenido').css('display','');
			}
			
			function ocultarRelevamiento(){
				$('#pedidoRelevamientosBtn').css('display','');
				$('#pedidoRelevamientosCrearBtn').css('display','none');
				
				$('#relevamientosPedidoContenido').css('display','none');
			}
			
			function mostrarPresupuesto(){
				$('#pedidoPresupuestosCrearBtn').css('display','');
				$('#pedidoPresupuestosBtn').css('display','none');
				$('#tituloContenido').html('Presupuestos del Pedido');
				
				$('#presupuestosPedidoContenido').css('display','');
			}
			
			function ocultarPresupuesto(){
				$('#pedidoPresupuestosBtn').css('display','');
				$('#pedidoPresupuestosCrearBtn').css('display','none');
				
				$('#presupuestosPedidoContenido').css('display','none');
			}
			
			mostrarPedido();
			ocultarRelevamiento();
			ocultarPresupuesto();
			
			$('#pedidoDatosBtn').click(function(){
				
				mostrarPedido();
				ocultarRelevamiento();
				ocultarPresupuesto();
				
			});
			
			$('#pedidoRelevamientosBtn').click(function(){
				
				ocultarPedido();
				mostrarRelevamiento();
				ocultarPresupuesto();
				
			});
			
			$('#pedidoPresupuestosBtn').click(function(){
				
				ocultarPedido();
				ocultarRelevamiento();
				mostrarPresupuesto();
				
			});
			


		});
	
	
	
function bloquearUbicacionTodos(){
		
		$('#pedido_provincia').attr('disabled','disabled');
		$('#pedido_departamento').attr('disabled','disabled');
		$('#pedido_localidad').attr('disabled','disabled');
		$('#btnGuardarCambios').attr('disabled','disabled')
		
	}
	
	function habilitarUbicacionTodos(){
		$('#pedido_provincia').removeAttr('disabled');
		$('#pedido_departamento').removeAttr('disabled');
		$('#pedido_localidad').removeAttr('disabled');
		$('#btnGuardarCambios').removeAttr('disabled')
	}
	
	function cargarLocalidades(departamento){
		
		$('#pedido_localidad option').remove();
		$('<option>').val('').text('Cargando Localidades').appendTo('#pedido_localidad');
		$.post(urlLocalidad ,{depa:departamento},function(datos){ 
			$('#pedido_localidad option').remove();
			$.each(datos,function(index,objeto){
				
				$('<option>').val(objeto.id).text(objeto.nombre).appendTo('#pedido_localidad');

			})
				
			habilitarUbicacionTodos();
			
		},"json");
	}
	
	
	bloquearUbicacionTodos();
	
	
	
	$(window).load(function(){	
		
		habilitarUbicacionTodos();
		
		$('#pedido_provincia').change(function(){
			
			bloquearUbicacionTodos();
			
			$('#pedido_departamento option').remove();
				$('<option>').val('').text('Cargando Departamentos').appendTo('#pedido_departamento');
			
			$('#pedido_localidad option').remove();
				$('<option>').val('').text('Cargando Localidades').appendTo('#pedido_localidad');
			
			$.post(urlDepartamento ,{prov:$(this).val()},function(datos){ 
				
				$('#pedido_departamento option').remove();
				
				$.each(datos,function(index,objeto){
					
					$('<option>').val(objeto.id).text(objeto.nombre).appendTo('#pedido_departamento');

				})
				
				
				cargarLocalidades($('#pedido_departamento option:selected').val())
			   console.log($('#pedido_departamento option:selected'));
				
				
			},"json");
			
		
			
		});
		
		
		$('#pedido_departamento').change(function(){
			
			bloquearUbicacionTodos();
			
				$('#pedido_localidad option').remove();
				$('<option>').val('').text('Cargando Localidades').appendTo('#pedido_localidad');
				cargarLocalidades($(this).val());
			
		});
		
		
		
		
	});
		
	