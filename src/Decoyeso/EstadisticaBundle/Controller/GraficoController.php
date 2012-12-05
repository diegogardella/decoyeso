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
   
    public function graficoNivelesDeProduccion() {
    	
    	$request = $this->getDatosRequest();
    	
    	$em = $this->getDoctrine()->getEntityManager();
    	$query = $em->createQuery('
    			SELECT pp, SUM(pp.cantidadProducida), prod.tipo FROM DecoyesoProduccionBundle:ProcesoProducto pp
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

    	$fecha = $request["fechaDesde"];
    	$arrCatNames = array();
    	$arrData[0][0] = "Placas";
		$arrData[0][1] = ""; // Dataset Parameters
		$arrData[1][0] = "Molduras";
		$arrData[1][1] = ""; // Dataset Parameter
    	
		$i = 1;
   		while ($fecha < $request["fechaHasta"] ) {
   			$i++;
   			$arrCatNames[] = $fecha->modify('+1 day')->format('d-m-Y');	
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
    			'labelStep' => 7,
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
    
    public function graficoProductosFabricados() {
    	    	
    	

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
    	$strParam="caption=Cantidades;";
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
    	$strParam="caption=Cantidades;";
    	# Set chart attributes
    	$grafico->setChartParams($strParam);
    	## call FusionCharts PHP Class Function to add data from the array
    	$grafico->addChartDataFromArray($arrData);
    
    
    	return $grafico;
    
    }
    
    /*
     * Grafico 1: Cantidad de Productos
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
    	$strParam="caption=Cantidades;";
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
    			$graficos[1] =  $this->graficoNivelesDeProduccion();
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
    	
    	return $this->render('DecoyesoEstadisticaBundle:Grafico:admin_show_graficos_modulo.html.twig', array(
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
