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
   
    public function graficoNivelesDeProduccion() {
    	
    	$request = $this->getDatosRequest();
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT   SUM(pp.cantidadProducida) as cantidadProducida, prod.tipo, proc.fechaFin FROM DecoyesoProduccionBundle:ProcesoProducto pp
    			JOIN pp.proceso proc
    	    	JOIN pp.producto prod
    			WHERE
    			proc.estado = :proc_estado AND
    			prod.tipo = :prod.tipo 
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
			
  		}	/**/
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
    	$grafico = new grafico("MSLine","900","300");
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
    
    public function graficoPlacasFabricados() {
    	
    	$request = $this->getDatosRequest();
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT SUM(pp.cantidadProducida) AS cantidadProducida, prod.nombre AS prodNombre , proc.fechaFin 
    			FROM DecoyesoProduccionBundle:ProcesoProducto pp
    			JOIN pp.proceso proc
    			JOIN pp.producto prod
    			WHERE
    			proc.estado = :proc_estado AND
    			prod.tipo = 0 AND
    			proc.fechaFin >= :proc_fechaDesde AND
    			proc.fechaFin <= :proc_fechaHasta
    			GROUP BY prod.id
    			ORDER BY proc.fechaFin ASC
    			');
    	$query->setParameters(array(
    			'proc_estado' => 2,
    			'proc_fechaDesde' => $request["fechaDesde"],
    			'proc_fechaHasta' => $request["fechaHasta"],
    	));
    	
    	$res = $query->getResult();
    	
    	
    	for ($i=0; $i<count($res); $i++) {
    		$arrData[$i][0] = $res[$i]["prodNombre"];
    		$arrData[$i][1] = $res[$i]["cantidadProducida"];
    	}
    	
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$ancho = 450;
    	$grafico = new grafico("Column3D",$ancho,"300");
    	$grafico->setAncho($ancho);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    			'caption' => 'PLACAS PRODUCIDAS',
    			'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
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
    
    public function graficoMoldurasFabricados() {
    
    	$request = $this->getDatosRequest();
    
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT SUM(pp.cantidadProducida) AS cantidadProducida, prod.nombre AS prodNombre , proc.fechaFin
    			FROM DecoyesoProduccionBundle:ProcesoProducto pp
    			JOIN pp.proceso proc
    			JOIN pp.producto prod
    			WHERE
    			proc.estado = :proc_estado AND
    			prod.tipo = 1 AND
    			proc.fechaFin >= :proc_fechaDesde AND
    			proc.fechaFin <= :proc_fechaHasta
    			GROUP BY prod.id
    			ORDER BY proc.fechaFin ASC
    			');
    	$query->setParameters(array(
    			'proc_estado' => 2,
    			'proc_fechaDesde' => $request["fechaDesde"],
    			'proc_fechaHasta' => $request["fechaHasta"],
    	));
    
    	$res = $query->getResult();
    
    
    	for ($i=0; $i<count($res); $i++) {
    		$arrData[$i][0] = $res[$i]["prodNombre"];
    		$arrData[$i][1] = $res[$i]["cantidadProducida"];
    	}
    
    	# Create FusionCharts PHP Class object for single series column3d chart
    	$ancho = 450;
    	$grafico = new grafico("Column3D",$ancho,"300");
    	$grafico->setAncho($ancho);
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    	'caption' => 'MOLDURAS PRODUCIDAS',
    	'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
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
    * GRAFICOS DE STOCK
    * -----------------------
    */
    
    
    /*
     * Grafico 1: Cantidad de Placas
    */
    
    
    public function graficoCantidadDePlacas() {
    
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
    	$grafico = new grafico("Column3D","900","300");
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    			'caption' => 'STOCK DE PLACAS',
    			//'subcaption' => "".$request["fechaDesde"]->format("d-m-Y")."  a  ".$request["fechaHasta"]->format("d-m-Y") ,
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
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
    
    public function graficoCantidadDeMolduras() {
    
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
    	$grafico = new grafico("Column3D","900","300");
    	# Set Relative Path of swf file.
    	$grafico->setSWFPath($this->container->getParameter("grafico.swf.dir"));
    	# Define chart attributes
    	$parametros = array(
    			'caption' => 'STOCK DE MOLDURAS',
    			'yAxisName' => "Cantidad",
    			'showLabels' => 1,
    			'slantLabels' =>'1',
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
    		$arrData[$i][0] = $res[$i]->getNombre();
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
     * Grafico 1: Cantidad de Pedidos
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
    		$arrData[$i][0] = $res[$i]->getNombre();
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
    

    public function mostrarGraficosModuloAction($modulo)
    {

	   	switch ($modulo) {
    		case "proceso":
    			$graficos[1] = $this->graficoNivelesDeProduccion();
    			$graficos[2] = $this->graficoPlacasFabricados();
    			$graficos[3] = $this->graficoMoldurasFabricados();
    			break;
    		case "pedido":
    			//$graficos[1] =  $this->graficoCantidadDeProductos();
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
    		case 10:
    			$grafico = array('grafico' => $this->graficoNivelesDeProduccion());
    			break;
    		default:
    			return $this->redirect($this->generateUrl('grafico'));
    	}
    	
	
    	$request = $this->getDatosRequest();
    	
    	return $this->render('DecoyesoEstadisticaBundle:Grafico:admin_show_grafico_individual.html.twig', array(
    			'grafico'      => $grafico,
    			'request' => $request,
    	
    	));
    }
    

}
