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
    		$r["intervalo"] = -1;
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
    	
    	
    
    	return $r;
    
    
    
    }
    
	/*
	 * ----------------------- 
	 * GRAFICOS DE PRODUCCION
	 * -----------------------
	 */
    /*
     $i = 1;
    while ($fecha < $request["fechaHasta"] ) {
    $i++;
    
    $arrCatNames[] = $fecha->format('d-m-Y');
    $fecha->modify('+'.$request["intervalo"].' day');
    
    $arrData[0][$i] = 0;
    $arrData[1][$i] = 0;
    
    foreach ($res as $r){
    if ($r[0]->getProceso()->getFechaFin()->format('d-m-Y') == $fecha->format('d-m-Y') ) {
    if ($r[0]->getProducto()->getTipo() == 0) {
    $arrData[0][$i] += $r[1];
    }
    if ($r[0]->getProducto()->getTipo() == 1) {
    $arrData[1][$i] += $r[1];
    }
    }
    
    }
    }
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
    	$query = $em->createQuery('
    			SELECT   SUM(pp.cantidadProducida) as cantidadProducida, prod.tipo, proc.fechaFin FROM DecoyesoProduccionBundle:ProcesoProducto pp
    			JOIN pp.proceso proc
    	    	JOIN pp.producto prod
    			WHERE
    			proc.estado = :proc_estado AND
    		
       			proc.fechaFin >= :proc_fechaDesde AND
       			proc.fechaFin <= :proc_fechaHasta 
    			GROUP BY prod.tipo, proc.fechaFin 
    			ORDER BY proc.fechaFin ASC
    			');
       	$query->setParameters(array(
    			'proc_estado' => 2,
    			'proc_fechaDesde' => $request["fechaDesde"],
    			'proc_fechaHasta' => $request["fechaHasta"],
       	));
       
    	$res = $query->getResult();
		    	
		   		
   		//Cargo array de categorias
   		$fecha = $this->crearDateTime($request["fechaDesde"]);
   		$arrCatNames = array();
   		$i=0;
   		while ($fecha < $request["fechaHasta"] ) {
   			$arrCatNames[$i] = $fecha->format('d-m-Y');
   			$fecha->modify('+1 day');
   			$i++;
   		}
   		
   		
   		//Cargo array de datos de la primera serie 1
   		$fecha = $this->crearDateTime($request["fechaDesde"]);
   		$serie = 0;
   		$nombreSerie = "Placas";
   		$arrData[$serie][0] = $nombreSerie;
   		$arrData[$serie][1] = "";
   		$valorAmostrar = "cantidadProducida";
   		$campoFechaTope = "fechaFin";	
   		$arrData[$serie][2] = 0;
   		
   		
   		$i = 2;
   		while ($fecha < $request["fechaHasta"] ) {
   			
   			if (!isset($arrData[$serie][$i])) $arrData[$serie][$i] = 0;
   			  			
   			foreach ($res as $r) {
   				//print_r($r);
   				//echo "<br>";
   				$fechaTope = new \DateTime();
   				$auxFecha = explode("-",$r[$campoFechaTope]);
   				$fechaTope->setDate($auxFecha[0], $auxFecha[1], $auxFecha[2]);
   				if ($fechaTope->format('d-m-Y') == $fecha->format('d-m-Y') ) {		
   					if ($r["tipo"] == 0) {
	   					$arrData[$serie][$i] = $r[$valorAmostrar];
	   					break;
	   				}
   				}
   			}	
   			$i++;
   			$fecha->modify('+1 day');
   		}
		
   		
   		/*
   		//Acomodo intervalo
   	
   		$p = count($arrCatNames);
   		for ($i=0; $i<$p; $i++) {
   			if (($i % floatval($request["intervalo"])) != 0 ) {
  				unset($arrCatNames[$i]);
   			}  
  		}
  		
		for ($i=0; $i<count($arrData); $i++) {
			$p = count($arrData[$i]);
			
			//exit();
			$sum = 0;
			for ($o=3; $o<$p; $o++) {
				if (($o % floatval($request["intervalo"])) != 0 ) {
					$sum += $arrData[$i][$o];
					unset($arrData[$i][$o]);
				}
				else {
					$arrData[$i][$o] += $sum;
					$sum = 0;
				}
			}
			
  		}	
  		*/
  		//echo count ($arrData);
  		//print_r($arrCatNames);
  		//echo "<br>";
   		//print_r($arrData);
   		
   		
   		
/*
   		 		$fechaTope = new \DateTime();
   				$auxFecha = explode("-",$r["fechaFin"]);
   				$fechaTope->setDate($auxFecha[0], $auxFecha[1], $auxFecha[2]);
   				$diaSiguiente = $this->crearDateTime($fechaTope);
   				$diaSiguiente->modify('+1 day');
   				$diasConIntervalo = $this->crearDateTime($fechaTope);
   				$intervalo = $request["intervalo"] -1;
   				$diasConIntervalo->modify('-'.$intervalo.' day');
   				*/
   				//if ($fecha < $diaSiguiente && $fecha > $diasConIntervalo ) {
   		 
	
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("MSLine",$o["ancho"],$o["alto"]);
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
    	$grafico->addChartDataFromArray($arrData, $arrCatNames);

    	
    	return $grafico;
    	
    }
    
    
    
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
    	'caption' => 'PLACAS PRODUCIDAS',
    	'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
    			'formatNumberScale' => '0',
    			'decimalSeparator' =>',',
    			'thousandSeparator' => '.',
    	'useRoundEdges' => "1",
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
    	
    	for ($i=0; $i<count($res); $i++) {
    		$arrData[$i][0] = $res[$i]->getNombre();
    		$arrData[$i][1] = $res[$i]->getCantidadEnStock();
  		
    	}
    	
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("Bar2D",$o["ancho"],$o["alto"]);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    			'caption' => 'STOCK DE PLACAS',
    			//'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
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
    	
    
    	for ($i=0; $i<count($res); $i++) {
	   		$arrData[$i][0] = $res[$i]->getNombre();
    		$arrData[$i][1] = $res[$i]->getCantidadEnStock();
    	}
    	
 
    
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("Bar2D",$o["ancho"],$o["alto"]);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    			'caption' => 'STOCK DE MOLDURAS',
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
    /*
    	$query->setParameters(array(
    			
    	));
    */
    	$res = $query->getResult();
    
    	for ($i=0; $i<count($res); $i++) {
    		$arrData[$i][0] = $res[$i]->getNombre()." (".$res[$i]->getUnidad().")";
    		$arrData[$i][1] = $res[$i]->getCantidadEnStock();
    	}
    
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("Column3D",$o["ancho"],$o["alto"]);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
        $parametros = array(
    			'caption' => 'STOCK DE INSUMOS',
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
    
    public function graficoProductosMasPedidos($opciones = "") {
    
    	$o["ancho"] = "900";
    	if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    	$o["alto"] = "300";
    	if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    
    
    	$request = $this->getDatosRequest();
    
    	$em = $this->getDoctrine()->getEntityManager();
    
    	
    	
    	$query = $em->createQuery('
    			SELECT SUM(pe.cantidad) AS cantidadPedida, el.nombre AS prodNombre, pres.fechaCreado
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
    	$arrData[0][0] = "";
   		$arrData[0][1] = "";
    		for ($i=0; $i<count($res); $i++) {
    			$arrData[$i][0] = $res[$i]["prodNombre"];
    			$arrData[$i][1] = $res[$i]["cantidadPedida"];
    		}

    
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("Column3D",$o["ancho"],$o["alto"]);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    	'caption' => 'CANTIDADES PEDIDAS',
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
     * Grafico 2: Cantidad de Presupuestos aprobados por pedidos
    */
    
    public function graficoPedidosPresupuestosAprobados($opciones = "") {
    
    	$o["ancho"] = "900";
    	if (isset($opciones["ancho"])) $o["ancho"] = $opciones["ancho"];
    	$o["alto"] = "300";
    	if (isset($opciones["alto"])) $o["alto"] = $opciones["alto"];
    
    
    	$request = $this->getDatosRequest();
    
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	
    	//Pedidos con presupuestos aprobados
    	$query = $em->createQuery('
    			SELECT COUNT(ped.id) AS cantidadPedidos
    			FROM PedidoBundle:Pedido ped
    			JOIN ped.presupuestos pres
    			WHERE
    			pres.estado = :pres_estado AND
    			ped.fechaCreado >= :ped_fechaDesde AND
    			ped.fechaCreado <= :ped_fechaHasta

    	
    			');
    	$query->setParameters(array(
    			'pres_estado' => 1,
    			'ped_fechaDesde' => $request["fechaDesde"],
    			'ped_fechaHasta' => $request["fechaHasta"],
    	));
    	$res = $query->getResult();
    	
    	$arrData[0][0] = "Pedidos con presupuestos aprobados";
    	$arrData[0][1] = $res[0]["cantidadPedidos"];
    	
    	$cantidadPedidosAprobados = $res[0]["cantidadPedidos"];
    	
  		//Total de pedidos
    	$query = $em->createQuery('
    			SELECT COUNT(ped) AS cantidadPedidos
    			FROM PedidoBundle:Pedido ped
    			WHERE
    			ped.fechaCreado >= :ped_fechaDesde AND
    			ped.fechaCreado <= :ped_fechaHasta
    			
    			');
    	$query->setParameters(array(
    			
    			'ped_fechaDesde' => $request["fechaDesde"],
    			'ped_fechaHasta' => $request["fechaHasta"],
    	));
    	$res = $query->getResult();
		
    	$arrData[1][0] = "Pedidos sin presupuestos aprobados";
    	$arrData[1][1] = $res[0]["cantidadPedidos"] - $cantidadPedidosAprobados ;
    	
    
    	

    	
    	
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$grafico = new grafico("Pie2D",$o["ancho"],$o["alto"]);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
	    	'caption' => 'CANTIDADES PEDIDAS',
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
    
    public function getProductos() {
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT prod FROM ProductoBundle:Producto prod
    			ORDER BY prod.nombre ASC
    			');
    	$res = $query->getResult();
    	
    	return $res;
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
    			$graficos[1] =  $this->graficoProductosMasPedidos();
    			$graficos[2] =  $this->graficoPedidosPresupuestosAprobados();
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
    			$grafico = array('grafico' => $this->graficoNivelesDeProduccion());
    			break;
    		case 11:
    			$grafico = array('grafico' => $this->graficoProductosFabricados(array("ancho" => "900","alto" => "450", "tipoProducto" => 0)));
    			break;
    		case 12:
    			$grafico = array('grafico' => $this->graficoProductosFabricados(array("ancho" => "900","alto" => "450", "tipoProducto" => 1)));
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
    

}
