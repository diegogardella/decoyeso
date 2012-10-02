	
	
	$(window).ready(function(){
	
		 function evaluarCamposForm(){
	
				if($('#cliente_tipo').val()==2){
					$('#div_cliente_dni').css('display','none');
					$('#div_cliente_nombre label').html('Nombre');
					$('#div_cliente_cuitOcuil label').html('CUIT');
				}else{
					$('#div_cliente_dni').css('display','');
					$('#div_cliente_nombre label').html('Apellido, Nombre');
					$('#div_cliente_cuitOcuil label').html('CUIL');
				}
		  }
		
		
		
			evaluarCamposForm();
		
			$('#cliente_tipo').change(function(){
					
					evaluarCamposForm();
			});
			
			$('#pedidosClienteContenido').css('display','none');
			$('#clienteDatosBtn').css('display','none');
			$('#clientePedidosCrearBtn').css('display','none');
			
			$('#clientePedidosBtn').click(function(){
				
				$('#datosClienteContenido').css('display','none');
				$('#clientePedidosBtn').css('display','none');
				$('#clienteListarBtn').css('display','none');
				$('#clienteEliminarBtn').css('display','none');
				$('#msjInfo').css('display','none');
				
				$('#pedidosClienteContenido').css('display','');
				$('#clienteDatosBtn').css('display','');
				$('#clientePedidosCrearBtn').css('display','');
				
				$('#tituloContenido').html('Pedidos del Cliente');
				
			});
			
			$('#clienteDatosBtn').click(function(){
				

				$('#pedidosClienteContenido').css('display','none');
				$('#clienteDatosBtn').css('display','none');
				$('#clientePedidosCrearBtn').css('display','none');
				
				$('#datosClienteContenido').css('display','');
				$('#clientePedidosBtn').css('display','');
				$('#clienteListarBtn').css('display','');
				$('#clienteEliminarBtn').css('display','');
				$('#msjInfo').css('display','');
				
				$('#tituloContenido').html('Datos del Cliente');
				
			});
			
			
			

		});
		
	function bloquearUbicacionTodos(){
		
		$('#cliente_provincia').attr('disabled','disabled');
		$('#cliente_departamento').attr('disabled','disabled');
		$('#cliente_localidad').attr('disabled','disabled');
		$('#btnGuardarCambios').attr('disabled','disabled')
		
	}
	
	function habilitarUbicacionTodos(){
		$('#cliente_provincia').removeAttr('disabled');
		$('#cliente_departamento').removeAttr('disabled');
		$('#cliente_localidad').removeAttr('disabled');
		$('#btnGuardarCambios').removeAttr('disabled')
	}
	
	function cargarLocalidades(departamento){
		
		$('#cliente_localidad option').remove();
		$('<option>').val('').text('Cargando Localidades').appendTo('#cliente_localidad');
		$.post(urlLocalidad ,{depa:departamento},function(datos){ 
			$('#cliente_localidad option').remove();
			$.each(datos,function(index,objeto){
				
				$('<option>').val(objeto.id).text(objeto.nombre).appendTo('#cliente_localidad');

			})
				
			habilitarUbicacionTodos();
			
		},"json");
	}
	
	
	bloquearUbicacionTodos();
	
	
	
	$(window).load(function(){	
		
		habilitarUbicacionTodos();
		
		$('#cliente_provincia').change(function(){
			
			bloquearUbicacionTodos();
			
			$('#cliente_departamento option').remove();
				$('<option>').val('').text('Cargando Departamentos').appendTo('#cliente_departamento');
			
			$('#cliente_localidad option').remove();
				$('<option>').val('').text('Cargando Localidades').appendTo('#cliente_localidad');
			
			$.post(urlDepartamento ,{prov:$(this).val()},function(datos){ 
				
				$('#cliente_departamento option').remove();
				
				$.each(datos,function(index,objeto){
					
					$('<option>').val(objeto.id).text(objeto.nombre).appendTo('#cliente_departamento');

				})
				
				
				cargarLocalidades($('#cliente_departamento option:selected').val())
			   console.log($('#cliente_departamento option:selected'));
				
				
			},"json");
			
		
			
		});
		
		
		$('#cliente_departamento').change(function(){
			
			bloquearUbicacionTodos();
			
				$('#cliente_localidad option').remove();
				$('<option>').val('').text('Cargando Localidades').appendTo('#cliente_localidad');
				cargarLocalidades($(this).val());
			
		});
		
		
		
		
	});
	
	
	
