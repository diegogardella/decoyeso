<?php

namespace Decoyeso\EstadisticaBundle\Entity;
	
use Decoyeso\EstadisticaBundle\Entity\FusionCharts;


class Grafico extends FusionCharts
{

    
    /**
     * @var string $tipo
     *
     */
    private $tipo;
    	
    /**
     * @var string $opciones
     *
     */
    private $opciones = array();
    
    /**
     * @var string $valores
     *
     */
    //private $xml;
    
    
    
    /**
     * Set opciones
     *
     * @param text $opciones
     */
    public function setOpciones($opciones)
    {
    	//$this->setOpcionesDefault($this->tipo);
    
    	foreach ($opciones as $k => $v) {
    		$this->opciones[$k] = $v;
    	}
    }
    

    public function getOpcionesString()
    {
    	$opcionesString = "";
    	foreach ($this->opciones as $k => $v):
    		$opcionesString .= ' '.$k.'="'.$v.'" ';
    	endforeach;
    	return $opcionesString;
    }
    
    /**
     * Set valores
     *
     * @param text $valores
     */
    public function setValores($valores)
    {
    
    	for($i=0; $i<count($valores); $i++) {
    		$this->valores .= '<set label="'.$valores[$i]["label"].'" value="'.$valores[$i]["value"].'"/>';
    
    	}
    
    }
    
    /*
    public function setXML($xml)
    {
    	$this->xml = $xml;
    }
    
    public function getXML($xml)
    {
    	return $this->xml;
    }
    */
    
    /*
     *
    Graficos series simple
    
    file:///var/www/Decoyeso_arriba_test1/src/Decoyeso/EstadisticaBundle/Resources/public/js/FusionCharts/Gallery/Column3D.html
    file:///var/www/Decoyeso_arriba_test1/src/Decoyeso/EstadisticaBundle/Resources/public/js/FusionCharts/Gallery/Pie2D.html
    file:///var/www/Decoyeso_arriba_test1/src/Decoyeso/EstadisticaBundle/Resources/public/js/FusionCharts/Gallery/Line2D.html
    file:///var/www/Decoyeso_arriba_test1/src/Decoyeso/EstadisticaBundle/Resources/public/js/FusionCharts/Gallery/Bar2D.html
    
    Column3D
    Pie2D
    Line2D
    Bar2D
    Graficos de series multiples
    file:///var/www/Decoyeso_arriba_test1/src/Decoyeso/EstadisticaBundle/Resources/public/js/FusionCharts/Gallery/MSColumn2D.html
    file:///var/www/Decoyeso_arriba_test1/src/Decoyeso/EstadisticaBundle/Resources/public/js/FusionCharts/Gallery/MSBar2D.html
    file:///var/www/Decoyeso_arriba_test1/src/Decoyeso/EstadisticaBundle/Resources/public/js/FusionCharts/Gallery/MSLine.html
    
    
    */
    
    


    /**
     * Get opciones
     *
     * @return text 
     */
    public function getOpciones()
    {
        return $this->opciones;
    }
    


    /**
     * Get valores
     *
     * @return text 
     */
    public function getValores()
    {
        return $this->valores;
    }
    
    
    /**
     * Set tipo
     *
     * @param string $tipo
     */
    public function setTipo($tipo)
    {
    	$this->tipo = $tipo;
    }
    
    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
    	return $this->tipo;
    }
    
    
}