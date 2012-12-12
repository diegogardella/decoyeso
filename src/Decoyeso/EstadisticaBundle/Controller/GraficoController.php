<?php

namespace Decoyeso\EstadisticaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Decoyeso\EstadisticaBundle\Entity\Grafico;

use Decoyeso\EstadisticaBundle\Form\GraficoType;




use DoctrineExtensions\Query\Mysql\Year;


/**
 * Grafico controller.
 *
 */
class GraficoController extends Controller
{
    /**
     * Lists all Grafico entities.
     *
     */
    public function indexAction()
    {
        
		$entities = "";
        return $this->render('DecoyesoEstadisticaBundle:Grafico:admin_index.html.twig', array(
            'entities' => $entities
        ));
    }
    

    
/*
 
 
 PRODUCCION
 ----------
 cantidad de productos fabricados
 produccion
 
 
 STOCK
 -----
 Cantidad de 
 Cantidad de insumos
 
 Productos e insumos
 cantidad en stock
 Cantidad que salio de stock
 Cantidad que entro
 
 Cantidad solicitada a stock (por pedido)
 Cantidad entregada (por pedido)
 
 
 function fecha($fecha)

{ //www.webenphp.com
$valor = $fecha;if (ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})" , $valor, $regs)&&$regs[1]<=31 &&$regs[2]<=12)
{echo "$regs[1].$regs[2].$regs[3]";}
else {echo "No es correcto el formato de fecha: $valor";}}
 
 */
    
    public function crearDateTime($datetime) {
    	
    	$aux = explode ("-", $datetime->format('d-m-Y'));
    	$d = new \DateTime();
    	$d->setDate($aux[2], $aux[1], $aux[0]);
    	
    	return $d;
    }
    
    public function getDatosRequest() {
    
    	$request = $this->getRequest();
    	$r = $request->query->all();
    
    	/*
    	if (isset($r["fechaDesde"])){
	    	$auxFecha = explode('-', $r["fechaDesde"]);
	    	foreach ($auxFecha as $f) {
	    		
	    		if (!is_int($f)) {
	    			unset($r["fechaDesde"]);
	    		}
	    	}
    	}
    	if (isset($r["fechaDesde"])){
	    	$auxFecha = explode('-', $r["fechaHasta"]);
	    	foreach ($auxFecha as $f) {
	    		if (!is_int($f)) {
	    			unset($r["fechaHasta"]);
	    		}
	    	}
    	}
    	*/
    	if (!isset($r["fechaDesde"])){
    		$today = new \DateTime('today');
    		$r["fechaDesde"] = $today->modify("-30 day");
    	}
    	else {
    		$auxFecha = explode('-', $r["fechaDesde"]);
    		$fecha = new \DateTime();
    		$r["fechaDesde"] = $fecha->setDate($auxFecha[2], $auxFecha[1], $auxFecha[0]);
    	}
    	if (!isset($r["fechaHasta"])){
    		$r["fechaHasta"] = new \DateTime('today');
    	}
    	else {
    		$auxFecha = explode('-', $r["fechaHasta"]);
    		$fecha = new \DateTime();
    		$r["fechaHasta"] = $fecha->setDate($auxFecha[2], $auxFecha[1], $auxFecha[0]);
    	}
    	
    	
    	//Intervalo
    	
    	if (!isset($r["intervalo"])){
    		$r["intervalo"] = 1;
    	}
    	
    	$dif = date_diff( $r["fechaDesde"], $r["fechaHasta"]);
    	
    	if($dif->format('%a') > 60 && $r["intervalo"] == 1 ) {
    		$r["intervalo"] = 7;
    	}
    	
    	if($dif->format('%a') > 120 && $r["intervalo"] == 7 ) {
    		$r["intervalo"] = 30;
    	}
    	
    	if($dif->format('%a') > 520 && $r["intervalo"] == 30 ) {
    		$r["intervalo"] = 360;
    	}
    	
    	
    	//tipo producto
    	if (!isset($r["tipoProducto"]) || ($r["tipoProducto"] != -1 && $r["tipoProducto"] != 0 && $r["tipoProducto"] != 1  )){
    		$r["tipoProducto"] = -1;
    	}
    	
    	
    	if (!isset($r["productos"])){
    		$r["productos"] = array();
    	}
    	
    	if (!isset($r["costoIndividual"])){
    		$r["costoIndividual"] = 0;
    	}
    	
    	if (!isset($r["costoTotal"])){
    		$r["costoTotal"] = 0;
    	}
    	
    	if (!isset($r["insumos"])){
    		$r["insumos"] = 0;
    	}
    	
    	if (!isset($r["ganancia"])){
    		$r["ganancia"] = 0;
    	}
    	
    

    
    	return $r;
    
    
    
    }
    
	/*
	 * ----------------------- 
	 * GRAFICOS DE PRODUCCION
	 * -----------------------
	 */
  
    
    /*
     * Grafico 1: Niveles de Produccion
     */
   
    public function graficoNivelesDeProduccion($opciones = "") {
    	
    	$o["ancho"] = "900";
    	if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    	$o["alto"] = "300";
    	if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    	
    	$request = $this->getDatosRequest();
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	
    	//QUERY PLACAS
    	$query = $em->createQuery('
    			SELECT SUM(pp.cantidadProducida) as cantidadProducida, prod.id as prodId,  proc.fechaFin FROM DecoyesoProduccionBundle:ProcesoProducto pp
    			JOIN pp.proceso proc
    	    	JOIN pp.producto prod
    			WHERE
    			proc.estado > :proc_estado AND
    			prod.tipo = 0 AND
       			proc.fechaFin >= :proc_fechaDesde AND
       			proc.fechaFin <= :proc_fechaHasta 
    			GROUP BY prod.tipo, proc.fechaFin 
    			ORDER BY proc.fechaFin ASC
    			');
       	$query->setParameters(array(
    			'proc_estado' => 1,
    			'proc_fechaDesde' => $request["fechaDesde"],
    			'proc_fechaHasta' => $request["fechaHasta"],
       	));
    	$res = $query->getResult();
		    	
		   		
   		//Cargo array de categorias
   		$fecha = $this->crearDateTime($request["fechaDesde"]);

   		
   		# Create FusionCharts PHP Class object for single series column3d chart
   		if ($request["insumos"]){ 
   			$grafico = new grafico("MSColumn3DLineDY",$o["ancho"],$o["alto"], 10);
   		}
   		else {
   			$grafico = new grafico("MSLine2D",$o["ancho"],$o["alto"], 10);
   			
   		}
   		
   		
   		
   		while ($fecha < $request["fechaHasta"] ) {
   			$grafico->addCategory($fecha->format('d-m-Y'));
   			$fecha->modify('+'.$request["intervalo"].' day');
   		}
   		
   		
    	//Cargo array de datos de la primera serie 1
   		$grafico->addDataset("Placas");
   		
   		$fecha = $this->crearDateTime($request["fechaDesde"]);
   		$valorAmostrar = "cantidadProducida";
   		$campoFechaTope = "fechaFin";	
   		
   		//print_r($res);
   		$i = 0;
   		$valor = 0;
   		$grafico->addChartData(0);
   		$insumosTotales = array();
   		
   		while ($fecha < $request["fechaHasta"] ) {
   			$i++;
   			  			  			
   			foreach ($res as $r) {
   				
   				//print_r($r);
   				//echo "<br>";
   				$fechaTope = new \DateTime();
   				$auxFecha = explode("-",$r[$campoFechaTope]);
   				$fechaTope->setDate($auxFecha[0], $auxFecha[1], $auxFecha[2]);
   				
   				if ($fechaTope->format('d-m-Y') == $fecha->format('d-m-Y') ) {	
   					$valor += $r[$valorAmostrar];
   					
   				
   				}
   				
   			}
   			
   			if ($i % $request["intervalo"] == 0) {
   				$grafico->addChartData($valor);	
   				$valor = 0;
   			}
   			
   		  	$fecha->modify('+1 day');
   		}
   		
   		
   		
   		
   		//QUERY MOLDURAS
   		$query = $em->createQuery('
   				SELECT SUM(pp.cantidadProducida) as cantidadProducida, prod.id as prodId,  proc.fechaFin FROM DecoyesoProduccionBundle:ProcesoProducto pp
   				JOIN pp.proceso proc
   				JOIN pp.producto prod
   				WHERE
   				proc.estado > :proc_estado AND
   				prod.tipo = 1 AND
   				proc.fechaFin >= :proc_fechaDesde AND
   				proc.fechaFin <= :proc_fechaHasta
   				GROUP BY prod.tipo, proc.fechaFin
   				ORDER BY proc.fechaFin ASC
   				');
   		$query->setParameters(array(
   				'proc_estado' => 1,
   				'proc_fechaDesde' => $request["fechaDesde"],
   				'proc_fechaHasta' => $request["fechaHasta"],
   		));
   		$res = $query->getResult();
   		
   		
   		//Cargo array de datos de la primera serie 1
   		$grafico->addDataset("Molduras");
   		
   		$fecha = $this->crearDateTime($request["fechaDesde"]);
   		$valorAmostrar = "cantidadProducida";
   		$campoFechaTope = "fechaFin";
   		
   		//print_r($res);
   		$i = 0;
   		$valor = 0;
   		$grafico->addChartData(0);
   		$insumosTotales = array();
   		
   		while ($fecha < $request["fechaHasta"] ) {
   			$i++;
   			
   			foreach ($res as $r) {
   				$fechaTope = new \DateTime();
   				$auxFecha = explode("-",$r[$campoFechaTope]);
   				$fechaTope->setDate($auxFecha[0], $auxFecha[1], $auxFecha[2]);
   		
   				if ($fechaTope->format('d-m-Y') == $fecha->format('d-m-Y') ) {
   					$valor += $r[$valorAmostrar];
   					/*
   					$producto = $em->getRepository('ProductoBundle:Producto')->find($r["prodId"]);
   					
   					$in = $this->calcularUsoDeInsumo($producto, $valor);
   					//print_r($in);
   					
   					foreach ($in as $k => $i) {
   						
   						if (!isset($insumosTotales[$k][$fecha->format('d-m-Y')]))
   							$insumosTotales[$k][$fecha->format('d-m-Y')] = $i;
   						else
   						$insumosTotales[$k][$fecha->format('d-m-Y')] += $i;
   						
   						
   					}
   					
   					*/
   				}
   		
   			}
   		
   			if ($i % $request["intervalo"] == 0) {
   				$grafico->addChartData($valor);
   				$valor = 0;
   			}
   		
   			$fecha->modify('+1 day');
   		}
   		
   		
   		
   		
   		/*
   		if (count($insumosTotales)>0) {
   			
   			print_r($insumosTotales);
   			
   			foreach ($insumosTotales as $k => $v) {
   				$insumo = $em->getRepository('ProductoBundle:Insumo')->find($k);
   				$grafico->addDataset($insumo->getNombre(),"parentYAxis=S");
   				$fecha = $this->crearDateTime($request["fechaDesde"]);
   				foreach ($v as $k1 =>$v1) {
   					while ($fecha < $request["fechaHasta"] ) {
   					
   						
   						if ($fecha->format('d-m-Y') == $k1 ) {
   							echo $k1."<br>";
   							$grafico->addChartData($v1);
   						}
   						else {
   							$grafico->addChartData(0);
   						}
   					
   					$fecha->modify('+1 day');
   					}
   				}
   				
   			}
   		}
   		*/
   			
   			
   
   	
   		
   		
   		
    	# Create FusionCharts PHP Class object for single series column3d chart
    	//$grafico = new grafico("MSLine",$o["ancho"],$o["alto"]);
    	# Set Relative Path of swf file.
      	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    			'caption' => 'Nivel de Producción',
    			'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'labelDisplay' => 'ROTATE',
    			'slantLabels' =>'1',
    			'formatNumberScale' => '0',
    			'decimalSeparator' =>',',
    			'thousandSeparator' => '.',
    			
    			); 
   		
   		
   		$strParam = "";
   		foreach ($parametros as $k => $v) {
   			$strParam .= $k."=".$v.";";
   		}
   			
    	//$strParam="caption=Niveles de Producción;formatNumberScale=1;rotateValues=1;decimals=0";
    	# Set chart attributes
    	$grafico->setChartParams($strParam);
    	## call FusionCharts PHP Class Function to add data from the array
    	//$grafico->addChartDataFromArray($arrData, $arrCatNames);

    	
    	return $grafico;
    	
    }
    
    
    public function calcularUsoDeInsumo($p, $cant) {
    	$em = $this->getDoctrine()->getEntityManager();
    	$insumos = $em->getRepository('ProductoBundle:ProductoInsumo')->findByProducto($p->getId());
    	
    	$arr = array();
    	
    	if ($insumos) {
    		foreach ($insumos as $i) {
    			$arr[$i->getInsumo()->getId()] = $i->getCantidad() * $cant;
    		}
    	}
    	
    	//print_r($arr);
    	//exit();
    	return $arr;
    }
    
    
    /*
     if ($request["insumos"]){
    $fecha = $this->crearDateTime($request["fechaDesde"]);
    print_r($insumosTotales);
    foreach ($insumosTotales as $k => $i ) {
    
    
    $insumo = $em->getRepository('ProductoBundle:Insumo')->find($k);
    $grafico->addDataset($k->getNombre." (Kg)","parentYAxis=S");
    
    foreach ($insumosTotales as $i ) {
    while ($fecha->format('d-m-Y') ==  ) {
    $grafico->addChartData(10);
    $fecha->modify('+1 day');
    }
    
    }
    
    }
    
    */
    
    
    
    public function graficoProductosFabricados($opciones = "") {
    
    	$o["ancho"] = "450";
    	if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    	$o["alto"] = "300";
    	if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    	$o["tipoProducto"] = 0;
    	if (isset($opciones["tipoProducto"])) $o["tipoProducto"] = $opciones["tipoProducto"];
    	
    	
    
    	$request = $this->getDatosRequest();
    
    	$sqlProductos = "";
    	if (count($request["productos"]) > 0) {
    		$sqlProductos = "";
    		foreach ($request["productos"] as $p ) {
    			$sqlProductos .= " prod.id = $p OR ";
    		}
    		$sqlProductos .= " prod.id = 0 AND";
    
    	}
    
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT SUM(pp.cantidadProducida) AS cantidadProducida, prod.nombre AS prodNombre, prod.id as prodId, proc.fechaFin
    			FROM DecoyesoProduccionBundle:ProcesoProducto pp
    			JOIN pp.proceso proc
    			JOIN pp.producto prod
    			WHERE
    			proc.estado = :proc_estado AND
    			'.$sqlProductos.'
    			prod.tipo = :prod_tipoProducto AND
    			proc.fechaFin >= :proc_fechaDesde AND
    			proc.fechaFin <= :proc_fechaHasta
    			GROUP BY prod.id
    			ORDER BY proc.fechaFin ASC
    			');
    
    	$params = array(
    			'proc_estado' => 2,
    			'prod_tipoProducto' => $o["tipoProducto"],
    			'proc_fechaDesde' => $request["fechaDesde"],
    			'proc_fechaHasta' => $request["fechaHasta"],
    	);
    
    	$query->setParameters($params);
    
    	$res = $query->getResult();
    	
    	# Create FusionCharts PHP Class object for single series column3d chart

    	
    	if ($request["costoIndividual"] || $request["costoTotal"]) {
    		//MSCombi2D
    		$grafico = new grafico("MSColumn3DLineDY",$o["ancho"],$o["alto"],11);
    		$grafico->addDataset("Placas");
    		for ($i=0; $i<count($res); $i++) {
    			$grafico->addCategory($res[$i]["prodNombre"]);
    			$grafico->addChartData($res[$i]["cantidadProducida"]);
    		}
    		
    		if ($request["costoIndividual"]) {
    			$grafico->addDataset("Costo Individual ($)","parentYAxis=S");
    			$sum = 0;
    			for ($i=0; $i<count($res); $i++) {
    				$producto = $em->getRepository('ProductoBundle:Producto')->find($res[$i]["prodId"]);
    				$sum = $res[$i]["cantidadProducida"] * $producto->getCosto();
    				$grafico->addChartData($sum);
    			}
    		}
    		
    		if ($request["costoTotal"]) {
    			$grafico->addDataset("Costo Total ($)","parentYAxis=S");
    			$sum = 0;
    			for ($i=0; $i<count($res); $i++) {
    				$producto = $em->getRepository('ProductoBundle:Producto')->find($res[$i]["prodId"]);
    				$sum += $res[$i]["cantidadProducida"] * $producto->getCosto();
    				$grafico->addChartData($sum);
    			}
    		}
    	}
    		
    	else  {
    		$grafico = new grafico("Column2D",$o["ancho"],$o["alto"],11);
	    	for ($i=0; $i<count($res); $i++) {
	    		$grafico->addChartData($res[$i]["cantidadProducida"], "label=".$res[$i]["prodNombre"]);
	    	}
    	}
    	
    	
    
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    	'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
    			'formatNumberScale' => '0',
    			'decimalSeparator' =>',',
    			'thousandSeparator' => '.',
    	'useRoundEdges' => "1",
    	);
    	
    	if ($o["tipoProducto"] == 0) {
    		$parametros['caption'] = 'PLACAS PRODUCIDAS';
    	}
    	else {
    		$parametros['caption'] = 'MOLDURAS PRODUCIDAS';
    	}
    
    	$strParam = "";
    	foreach ($parametros as $k => $v) {
    	$strParam .= $k."=".$v.";";
    }
    
    
    # Set chart attributes
    $grafico->setChartParams($strParam);
   
    return $grafico;
    
    
    }
    
  

    public function mostrarGraficosModuloAction($modulo)
    {

	   	switch ($modulo) {
    		case "proceso":
    			$graficos[1] = $this->graficoNivelesDeProduccion();
    			$graficos[2] = $this->graficoProductosFabricados(array("tipoProducto"=> 0));
    			$graficos[3] = $this->graficoProductosFabricados(array("tipoProducto"=> 1));
    			break;
    		case "pedido":
    			$graficos[1] =  $this->graficoProductosPedidos();
    			$graficos[2] =  $this->graficoProductoMasPedidos(array("producto"=> 0));
    			$graficos[3] =  $this->graficoProductoMasPedidos(array("producto"=> 1));
    			//$graficos[4] =  $this->graficoNivelesPedidos();
    			
    			break;
    		case "stock":
    			$graficos[1] =  $this->graficoCantidadDePlacas();
    			$graficos[2] =  $this->graficoCantidadDeMolduras();
    			$graficos[3] =  $this->graficoCantidadDeInsumos();
    			break;
    		default:
    			return $this->redirect($this->generateUrl('grafico'));
    	}
    	
    	
    	
    	$request = $this->getDatosRequest();
  
    	return $this->render('DecoyesoEstadisticaBundle:Grafico:graficos_'.$modulo.'.html.twig', array(
    			'graficos'      => $graficos,
    			'modulo'	=> $modulo,
    			'request' => $request,
    			
    			
    	));

    }
    
    
    
    public function mostrarGraficoIndividualAction($grafico) {
    	
    	
    	
    	switch ($grafico) {
    		//Graficos de procesos
    		case 10:
    			$grafico = array('grafico' => $this->graficoNivelesDeProduccion(array("ancho" => "900","alto" => "550")));
    			break;
    		case 11:
    			$grafico = array('grafico' => $this->graficoProductosFabricados(array("ancho" => "900","alto" => "450", "tipoProducto" => 0)));
    			break;
    		case 12:
    			$grafico = array('grafico' => $this->graficoProductosFabricados(array("ancho" => "900","alto" => "450", "tipoProducto" => 1)));
    			break;
    		//Graficos de pedidos
    		case 30:
    			$grafico = array('grafico' => $this->graficoProductosPedidos(array("ancho" => "900","alto" => "450")));
    			break;
    		default:
    			return $this->redirect($this->generateUrl('grafico'));
    	}
    	
	
    	$request = $this->getDatosRequest();
    	$productos = $this->getProductos();
    	
    	return $this->render('DecoyesoEstadisticaBundle:Grafico:admin_show_grafico_individual.html.twig', array(
    			'grafico'      => $grafico,
    			'request' => $request,
    			'productos' => $productos,
    	
    	));
    }
    
    
    
    
    
    
    
    
    
    /*
     * -----------------------
    * GRAFICOS DE STOCK
    * -----------------------
    */
    
    
    /*
     * Grafico 1: Cantidad de Placas
    */
    
    
    public function graficoCantidadDePlacas($opciones = "") {
    
    	$o["ancho"] = "450";
    	if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    	$o["alto"] = "500";
    	if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    
    	$request = $this->getDatosRequest();
    
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT prod FROM ProductoBundle:Producto prod
    			WHERE prod.tipo = :prod_tipo
    			ORDER BY prod.nombre ASC
    			');
    
    	$query->setParameters(array(
    			'prod_tipo' => 0,
    	));
    
    	$res = $query->getResult();
    
    	$cantidadTotal = 0;
    	$costoTotal = 0;
    	for ($i=0; $i<count($res); $i++) {
    		$arrData[$i][0] = $res[$i]->getNombre();
    		$arrData[$i][1] = $res[$i]->getCantidadEnStock();
    		$cantidadTotal += $res[$i]->getCantidadEnStock();
    		$costoTotal += $res[$i]->getCantidadEnStock() * $res[$i]->getCosto();
    	}
    
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("Bar2D",$o["ancho"],$o["alto"]);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    	'caption' => 'STOCK DE PLACAS',
    	'subcaption' => "Cantidad Total: ".$cantidadTotal." - Costo Total: $".number_format($costoTotal, 2, ',', '.'),
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
    			'formatNumberScale' => '0',
    			'decimalSeparator' =>',',
    			'thousandSeparator' => '.',
    			);
    
    			$strParam = "";
    			foreach ($parametros as $k => $v) {
    	$strParam .= $k."=".$v.";";
    }
    
    
    # Set chart attributes
    $grafico->setChartParams($strParam);
    	## call FusionCharts PHP Class Function to add data from the array
    	$grafico->addChartDataFromArray($arrData);
    
    
    	return $grafico;
    
    }
    
    public function graficoCantidadDeMolduras($opciones = "") {
    
    $o["ancho"] = "450";
    if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    $o["alto"] = "500";
    if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    
    $request = $this->getDatosRequest();
    
    $em = $this->getDoctrine()->getEntityManager();
    $query = $em->createQuery('
    SELECT prod FROM ProductoBundle:Producto prod
    WHERE prod.tipo = :prod_tipo
    ORDER BY prod.nombre ASC
    ');
    
    $query->setParameters(array(
    		'prod_tipo' => 1,
    	));
    
    	$res = $query->getResult();
    
    	$cantidadTotal = 0;
    	$costoTotal = 0;
    	for ($i=0; $i<count($res); $i++) {
    	$arrData[$i][0] = $res[$i]->getNombre();
    	$arrData[$i][1] = $res[$i]->getCantidadEnStock();
    	$cantidadTotal += $res[$i]->getCantidadEnStock();
    	$costoTotal += $res[$i]->getCantidadEnStock() * $res[$i]->getCosto();
    	}
    
    
    
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("Bar2D",$o["ancho"],$o["alto"]);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    			'caption' => 'STOCK DE MOLDURAS',
    			'subcaption' => "Cantidad Total: ".$cantidadTotal." - Costo Total: $".number_format($costoTotal, 2, ',', '.'),
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
    			'formatNumberScale' => '0',
    			'decimalSeparator' =>',',
    			'thousandSeparator' => '.',
    			);
    			$strParam = "";
    	foreach ($parametros as $k => $v) {
    	$strParam .= $k."=".$v.";";
    	}
    	# Set chart attributes
    	$grafico->setChartParams($strParam);
    	## call FusionCharts PHP Class Function to add data from the array
    	$grafico->addChartDataFromArray($arrData);
    
    
    	return $grafico;
    
    }
    
    /*
    * Grafico 2: Cantidad de Insumos
    */
    
    
    public function graficoCantidadDeInsumos($opciones = "") {
    
    $o["ancho"] = "900";
    if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    $o["alto"] = "300";
    if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    
    
    $request = $this->getDatosRequest();
    
    $em = $this->getDoctrine()->getEntityManager();
    $query = $em->createQuery('
    		SELECT ins FROM ProductoBundle:Insumo ins
    
    ORDER BY ins.nombre ASC
    ');
    
    $res = $query->getResult();
    
    $datosTotalesInsumos = array();
    for ($i=0; $i<count($res); $i++) {
	    $arrData[$i][0] = $res[$i]->getNombre()." (".$res[$i]->getUnidad().")";
	    $arrData[$i][1] = $res[$i]->getCantidadEnStock();
	    $datosTotalesInsumos[$res[$i]->getId()]["costoTotal"] = $res[$i]->getCantidadEnStock() * $res[$i]->getCosto();
	    $datosTotalesInsumos[$res[$i]->getId()]["nombre"] = $res[$i]->getNombre();
    	}
    	
    	$subcaption = "";
    	foreach ($datosTotalesInsumos as $d) {
    		$subcaption .= $d["nombre"].": $".$d["costoTotal"]."  ";
    	}
    
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("Column3D",$o["ancho"],$o["alto"]);
    			# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    			# Define chart attributes
    			$parametros = array(
    			'caption' => 'STOCK DE INSUMOS',
    			'subcaption' => $subcaption,
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
    			'formatNumberScale' => '0',
    			'decimalSeparator' =>',',
    	'thousandSeparator' => '.',
    	);
    	$strParam = "";
    	foreach ($parametros as $k => $v) {
    	$strParam .= $k."=".$v.";";
    }
    	# Set chart attributes
    	$grafico->setChartParams($strParam);
    	## call FusionCharts PHP Class Function to add data from the array
    $grafico->addChartDataFromArray($arrData);
    
    
    return $grafico;
    
    
    }
    
    
    
    
    
    
    
    
    /*
     * -----------------------
    * GRAFICOS DE PEDIDOS
    * -----------------------
    */
    
    
    /*
     * Grafico 1: Cantidad de Productos Pedidos
    */
    
    public function graficoProductosPedidos($opciones = "") {
    
    	$o["ancho"] = "900";
    	if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    	$o["alto"] = "300";
    	if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    
    
    	$request = $this->getDatosRequest();
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    
    
    	$query = $em->createQuery('
    			SELECT SUM(pe.cantidad) AS cantidadPedida, el.id AS prodId, el.nombre AS prodNombre, pres.fechaCreado
    			FROM PedidoBundle:PresupuestoElemento pe
    			JOIN pe.elemento el
    			JOIN pe.presupuesto pres
    			WHERE
    			pres.estado = :pres_estado AND
    			pres.fechaCreado >= :pres_fechaDesde AND
    			pres.fechaCreado <= :pres_fechaHasta
    			GROUP BY el.id
    			ORDER BY pres.fechaCreado ASC
    			');
    	$query->setParameters(array(
    			'pres_estado' => 1,
    			'pres_fechaDesde' => $request["fechaDesde"],
    			'pres_fechaHasta' => $request["fechaHasta"],
    	));
    	$res = $query->getResult();
    
    	if ($request["ganancia"]) {
    		$grafico = new grafico("MSColumn3DLineDY",$o["ancho"],$o["alto"],30);
    		$grafico->addDataset("Productos");
    		for ($i=0; $i<count($res); $i++) {
    			$grafico->addCategory($res[$i]["prodNombre"]);
    			$grafico->addChartData($res[$i]["cantidadPedida"]);
    		}
    
    		$grafico->addDataset("Ganancia estimada","parentYAxis=S");
    		$sum = 0;
    		for ($i=0; $i<count($res); $i++) {
    			$producto = $em->getRepository('ProductoBundle:Producto')->find($res[$i]["prodId"]);
    			$sum += $res[$i]["cantidadPedida"] * $producto->getPrecio();
    			$grafico->addChartData($sum);
    		}
    	}
    
    	else {
    
    		# Create FusionCharts PHP Class object for single series column3d chart
    		$grafico = new grafico("Column3D",$o["ancho"],$o["alto"], 30);
    	$grafico->addDataset("Productos");
    	for ($i=0; $i<count($res); $i++) {
    		//$grafico->addCategory($res[$i]["prodNombre"]);
    		$grafico->addChartData($res[$i]["cantidadPedida"], "label=".$res[$i]["prodNombre"]);
    	}
    	}
    
    
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    	'caption' => 'PRODUCTOS PEDIDOS',
    	'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
    	'yAxisName' => "Cantidad",
    	'showLabels' => 1,
    	'slantLabels' =>'1',
    	'formatNumberScale' => '0',
    	'decimalSeparator' =>',',
    	'thousandSeparator' => '.',
    	);
    	$strParam = "";
    	foreach ($parametros as $k => $v) {
    	$strParam .= $k."=".$v.";";
    }
    
    	# Set chart attributes
    	$grafico->setChartParams($strParam);
    
    
    
    	return $grafico;
    
    
    	}
    	
    	
    	/*
    	 * Grafico 1: Cantidad de Productos Pedidos
    	*/
    	
    	public function graficoNivelesPedidos($opciones = "") {
    	
    		$o["ancho"] = "900";
    		if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    		$o["alto"] = "300";
    		if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    	
    	
    		$request = $this->getDatosRequest();
    	
    		$em = $this->getDoctrine()->getEntityManager();
    	
    	
    	
    		$query = $em->createQuery('
    				SELECT SUM(pe.cantidad) AS cantidadPedida, el.id AS prodId,  pres.fechaCreado
    				FROM PedidoBundle:PresupuestoElemento pe
    				JOIN pe.elemento el
    				JOIN pe.presupuesto pres
    				WHERE
    				pres.estado = :pres_estado AND
    				pres.fechaCreado >= :pres_fechaDesde AND
    				pres.fechaCreado <= :pres_fechaHasta
    				
    				ORDER BY pres.fechaCreado ASC
    				');
    		$query->setParameters(array(
    				'pres_estado' => 1,
    				'pres_fechaDesde' => $request["fechaDesde"],
    				'pres_fechaHasta' => $request["fechaHasta"],
    		));
    		$res = $query->getResult();
    		
    		
    		$grafico = new grafico("MSLine2D",$o["ancho"],$o["alto"], 34);
    		
    		
    		
    		$fecha = $this->crearDateTime($request["fechaDesde"]);
    		
    		while ($fecha < $request["fechaHasta"] ) {
    			$grafico->addCategory($fecha->format('d-m-Y'));
    			$fecha->modify('+'.$request["intervalo"].' day');
    		}
    		
    		
    		//Cargo array de datos de la primera serie 1
    		$grafico->addDataset("Placas");
    		
    		$fecha = $this->crearDateTime($request["fechaDesde"]);
    		$valorAmostrar = "cantidadPedida";
    		$campoFechaTope = "fechaCreado";
    		
    		//print_r($res);
    		$i = 0;
    		$valor = 0;
    		$grafico->addChartData(0);
    		$insumosTotales = array();
    		
    		while ($fecha < $request["fechaHasta"] ) {
    			$i++;
    		
    			foreach ($res as $r) {
    		
    			
    				$fechaTope = new \DateTime();
    				$auxFecha = explode("-",$r[$campoFechaTope]);
    				$fechaTope->setDate($auxFecha[0], $auxFecha[1], $auxFecha[2]);
    		
    				if ($fechaTope->format('d-m-Y') == $fecha->format('d-m-Y') ) {
    					//echo $r[0]->getId();
    					$valor += $r[$valorAmostrar];
    				}
    			}
    		
	    		if ($i % $request["intervalo"] == 0) {
		    		$grafico->addChartData($valor);
		    		$valor = 0;
	    		}
    		
    			$fecha->modify('+1 day');
    		}
    		
    		
    		
    		
    	
    	
    		
    	
    	
    		
    		# Define chart attributes
    		$parametros = array(
    		'caption' => 'PRODUCTOS PEDIDOS',
    		'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
    		'yAxisName' => "Cantidad",
    		'showLabels' => 1,
    		'slantLabels' =>'1',
    		'formatNumberScale' => '0',
    		'decimalSeparator' =>',',
    		'thousandSeparator' => '.',
    		);
    		$strParam = "";
    		foreach ($parametros as $k => $v) {
    			$strParam .= $k."=".$v.";";
    		}
    	
    		# Set chart attributes
    		$grafico->setChartParams($strParam);
    	
    	
    	
    		return $grafico;
    	
    	
    	}
    
    
    
    
    	/*
    	* Grafico 2: Placas/Molduras mas pedidas
    	*/
    
    	public function graficoProductoMasPedidos($opciones = "") {
    
    	$o["ancho"] = "450";
    	if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    	$o["alto"] = "500";
    	if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    	$o["producto"] = 0;
    	if (isset($opciones["producto"])) $o["producto"] = $opciones["producto"];
    
    
    	$request = $this->getDatosRequest();
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    
    
    
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    	SELECT  prod.nombre as prodNombre, prod.id AS prodId FROM ProductoBundle:Producto prod
    	WHERE prod.tipo = :prod_tipo
    	ORDER BY prod.nombre ASC
    	');
    
    	$query->setParameters(array(
    			'prod_tipo' => $o["producto"],
    			));
    
    			$res = $query->getResult();
    
    
    			if ($o["producto"] == 0) {
    			$grafico = new grafico("Bar2D",$o["ancho"],$o["alto"], 31);
    			$grafico->addDataset("Placas");
    				for ($i=0; $i<count($res); $i++) {
    				//$grafico->addCategory($res[$i]["prodNombre"]);
    
    				$query1 = $em->createQuery('
    				SELECT SUM(pe.cantidad) AS cantidadPedida
    				FROM PedidoBundle:PresupuestoElemento pe
    				JOIN pe.elemento el
    				JOIN pe.presupuesto pres
    				WHERE
    				pres.estado = :pres_estado AND
    				el.id = :el_id AND
    				pres.fechaCreado >= :pres_fechaDesde AND
    				pres.fechaCreado <= :pres_fechaHasta
    				');
    				$query1->setParameters(array(
    				'pres_estado' => 1,
    				'el_id' => $res[$i]["prodId"],
    				'pres_fechaDesde' => $request["fechaDesde"],
    				'pres_fechaHasta' => $request["fechaHasta"],
    
    				));
    				$res1 = $query1->getResult();
    
    						$cantidad = 0;
    						if (isset($res1[0]["cantidadPedida"])) $cantidad = $res1[0]["cantidadPedida"];
    
    						$grafico->addChartData($cantidad, "label=".$res[$i]["prodNombre"]);
    				}
    
    
    				}
    				else {
    				$grafico = new grafico("Bar2D",$o["ancho"],$o["alto"], 33);
    				$grafico->addDataset("Molduras");
    				for ($i=0; $i<count($res); $i++) {
    				$query1 = $em->createQuery('
    				SELECT SUM(pe.cantidad) AS cantidadPedida
    				FROM PedidoBundle:PresupuestoElemento pe
    				JOIN pe.elemento el
    				JOIN pe.presupuesto pres
    				WHERE
    				pres.estado = :pres_estado AND
    				el.id = :el_id AND
    				pres.fechaCreado >= :pres_fechaDesde AND
    				pres.fechaCreado <= :pres_fechaHasta
    				');
    				$query1->setParameters(array(
    				'pres_estado' => 1,
    				'el_id' => $res[$i]["prodId"],
    				'pres_fechaDesde' => $request["fechaDesde"],
    				'pres_fechaHasta' => $request["fechaHasta"],
    
    				));
    				$res1 = $query1->getResult();
    
    				$cantidad = 0;
    				if (isset($res1[0]["cantidadPedida"])) $cantidad = $res1[0]["cantidadPedida"];
    
    				$grafico->addChartData($cantidad, "label=".$res[$i]["prodNombre"]);
    	}
    
    
    	}
    
    
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    
    	'yAxisName' => "Cantidad",
    	'showLabels' => 1,
    	'slantLabels' =>'1',
    	'formatNumberScale' => '0',
    	'decimalSeparator' =>',',
    				'thousandSeparator' => '.',
    				);
    
    				if ($o["producto"] == 0) {
    				$parametros['caption'] = 'PLACAS MAS PEDIDAS';
    				}
    				else {
    				$parametros['caption'] = 'MOLDURAS MAS PEDIDAS';
    				}
    
    
    				$strParam = "";
    				foreach ($parametros as $k => $v) {
    				$strParam .= $k."=".$v.";";
    				}
    
    # Set chart attributes
    $grafico->setChartParams($strParam);
    
    
    
    return $grafico;
    
    
    }
    
    public function getProductos() {
    $em = $this->getDoctrine()->getEntityManager();
    $query = $em->createQuery('
    SELECT prod FROM ProductoBundle:Producto prod
    ORDER BY prod.nombre ASC
    ');
    $res = $query->getResult();
    
    return $res;
    }
    

}
