<?php
define('TTF_DIR', dirname(__FILE__).'/../media/fonts/' );
define('BACKGROUND_IMG_POLLH', dirname(__FILE__).'/../media/polls/bg_enquisa_hor.jpg');
define('BACKGROUND_IMG_POLLV', dirname(__FILE__).'/../media/polls/bg_enquisa_ver.jpg');

include(SITE_LIBS_PATH.'jpgraph/jpgraph.php');
include(SITE_LIBS_PATH.'jpgraph/jpgraph_bar.php');

class PollGraph {
    
    // Poll values
    protected $title  = '';    
    protected $labels = array();
    protected $values = array();
    protected $total  = 0;  // total votes    
    
    // Graph settings
    protected $max_length_title = 24;
    protected $width   = 160;  // Default values
    protected $height  = 240;
    protected $margins = array();    
    protected $colours_theme = array();
        
    /**
     * Constructor
    */
    function __construct( $options=NULL ) {
        // TODO: initial config can be load from ini file, xml, ...
        
        // Set colours
        $this->colours_theme = array( 'horizontal' => 
                                        array('#003366', '#336699', '#6699CC',
                                              '#000000', '#666666', '#CCCCCC',
                                              '#663300', '#996633', '#CC9966', ),                                       
                                    );
        
        // Set margins
        $this->margins = array( 'horizontal'  => array( 50, 20, 38, 4), // L, R, T, B
                                'vertical'    => array( 10, 10, 70, 80), 
                                'pie'         => array( 0, 0, 0, 0), );
        
        if( is_array($options) ) {
            $this->setOptions( $options );
        }
    }
    
    /**
     * Contructor for PHP4 versions
     * @deprecated
     * @see PollGraph::__construct()
     */
    function PollGraph($options=NULL) {
        $this->__construct( $options );
    }
    
    
    /* Begin setters and getters section ************************************* */
    
    function setOptions( $params=array() ) {
        foreach($params as $k => $v) {
            if( property_exists($this, $k) ) {
                $this->{$k} = $v;
            }
        }
    }
    
    function getTitle() {
        return( $this->title );
    }
    
    function setTitle($title) {
        $this->title = $title;
    }
    
    function getValues() {
        return( $this->values );
    }
    
    function setValues( $values ) {
        $this->total = array_sum( $this->values );
        $this->values  = $values;        
    }    
    
    function getLabels() {
        return( $this->labels );
    }
    
    function setLabels( $labels ) {
        $this->labels = $labels;
    }    
    
    // FIXME: recovery data 
    function getGraphTypes() {
        return( array('horizontal', 'vertical', 'pie') );
    }
    
    function getMargins($type = 'horizontal') {
        $type  = strtolower( $type );
        $types = $this->getGraphTypes();
        
        if( !in_array($type, $types) ) {
            throw new Exception('You must specify a valid graph type');
        }        
        
        return( $this->margins[ $type ] );
    }
    
    /* End setters and getters section *************************************** */
    
    
    /**
     * util method
     */
    function converValuesToPercent() {
        $data = $this->getValues();
        $total = array_sum( $data );
        
        for($i=0; $i<count($data); $i++) {
            if( $total > 0) {
                $data[$i] = ($data[$i]*100)/$total;
            } else {
                $data[$i] = 0;
            }            
        }
        
        return( $data );
    }
    
    /**
     *
     */
    function chunkString($string, $length, $token=' ') {
        $currentLine = 0;
        $lines = array();
        
        $words = explode($token, $string);
        
        while( count($words) > 0 ) {
            $word = array_shift($words);
            if( strlen($lines[ $currentLine ].$token.$word) < $length ) {
                $lines[ $currentLine ] .= (strlen($lines[ $currentLine ])>0)? $token: ''; // control de principio de línea
                $lines[ $currentLine ] .= $word;
            } else {
                $currentLine++;
                $lines[ $currentLine ] = $word;
            }
        }
        
        return( $lines );
    }
    
    // FIXME: código muy similar a horizontal pero con muchas peculiaridades OJO
    function verticalRenderlet() {
        // Settings Graph 
        $graph = new Graph($this->width, $this->height, 'auto');
        $graph->SetScale( 'textlin' );
        $graph->SetColor( '#E4DDC9' );
        $graph->SetFrame( FALSE );
        $graph->SetMarginColor( '#E4DDC9' );        
        $graph->SetBackgroundImage(BACKGROUND_IMG_POLLV, BGIMG_FILLFRAME);
        
        // Get margins for Horizontal bars
        list($left, $right, $top, $bottom) = $this->getMargins('vertical');
        $graph->SetMargin($left, $right, $top, $bottom);            
        
        // Setup labels
        function labels2utf8(&$item, $key) {
            $item = utf8_decode($item);
        }
        $labels = $this->getLabels();        
        array_walk($labels, 'labels2utf8');
        
        // Label X-axis
        $graph->xaxis->SetTickLabels( $labels );
        $graph->xaxis->SetLabelMargin(0);
        $graph->xaxis->SetFont(FF_ARIAL, FS_BOLD);
        $graph->xaxis->HideTicks(TRUE, TRUE);
        $graph->xaxis->SetLabelAngle( 45 );
        
        // Label align for Y-axis
        $graph->yaxis->SetLabelAlign('center', 'bottom');
        $graph->yaxis->HideTicks(TRUE, TRUE);
        $graph->yaxis->HideLabels();
        $graph->yaxis->HideZeroLabel();
        $graph->yaxis->HideLine();
        
        // Titles
        $title = utf8_decode($this->getTitle());
        $lines_title = $this->chunkString($title, $this->max_length_title);
        $suffix = (count($lines_title) > 3)? "...": "";
        // Para horizontal bar como mucho dos líneas con 18 caracteres cada una
        $graph->title->Set( $lines_title[0]."\n".$lines_title[1]."\n".$lines_title[1].$suffix );
        $graph->title->SetFont(FF_ARIAL, FS_BOLD);
        
        // Get values
        $data = $this->converValuesToPercent();
        
        // Create a bar pot
        $bplot = new BarPlot( $data );
        $bplot->value->Show();
        $bplot->value->SetFont(FF_ARIAL, FS_BOLD);
        
        // Formating data
        function cbFmtPercentage($aVal) {    
            return sprintf("%.0f%%", $aVal); // Convert to string
        }           
        $bplot->value->SetFormatCallback("cbFmtPercentage");
        //$bplot->value->SetAngle( 45 );
        
        // Get colours scheme for this graph
        $colours_theme = array_slice( $this->colours_theme['horizontal'], 0, count($this->values) );        
        $bplot->SetFillColor( $colours_theme ); 
        
        $graph->Add($bplot);
        
        // FIXME: separate this method to other, see also abstract factory pattern
        $graph->Stroke();        
    }
    
    function horizontalRenderlet() {                            
        // Settings Graph 
        $graph = new Graph($this->width, $this->height, 'auto');
        $graph->SetScale( 'textlin' );
        $graph->SetColor( '#FFFFFF' );
        $graph->SetFrame( FALSE );
        $graph->SetMarginColor( '#FFFFFF' );        
        $graph->SetBackgroundImage(BACKGROUND_IMG_POLLH, BGIMG_FILLFRAME);
        
        // Get margins for Horizontal bars
        list($left, $right, $top, $bottom) = $this->getMargins('horizontal');        
        $graph->Set90AndMargin($left, $right, $top, $bottom);
        
        // Setup labels
        function labels2utf8(&$item, $key) {
            $item = utf8_decode($item);
        }
        $labels = $this->getLabels();        
        array_walk($labels, 'labels2utf8');
        
        // Label X-axis
        $graph->xaxis->SetTickLabels( $labels );
        $graph->xaxis->SetLabelMargin(0);
        $graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 8); // FF_FONT0
        $graph->xaxis->SetLabelAlign('right', 'center', 'right');
        $graph->xaxis->HideTicks(TRUE, TRUE); 
        
        // Label align for Y-axis
        $graph->yaxis->SetLabelAlign('center', 'bottom');
        $graph->yaxis->HideTicks(TRUE, TRUE);
        $graph->yaxis->HideLabels();
        $graph->yaxis->HideZeroLabel();
        $graph->yaxis->HideLine();
        
        // Titles
        $title = utf8_decode($this->getTitle());
        $lines_title = $this->chunkString($title, $this->max_length_title);
        $suffix = (count($lines_title) > 2)? "...": "";
        // Para horizontal bar como mucho dos líneas con 18 caracteres cada una
        $graph->title->Set( $lines_title[0]."\n".$lines_title[1].$suffix );
        $graph->title->SetFont(FF_ARIAL, FS_BOLD, 10);
        
        // Get values
        $data = $this->converValuesToPercent();
        
        // Create a bar pot
        $bplot = new BarPlot( $data );
        $bplot->value->Show();
        $bplot->value->SetFont(FF_FONT0, FS_NORMAL);
        
        // Formating data
        function cbFmtPercentage($aVal) {    
            return sprintf("%.0f%%", $aVal); // Convert to string
        }           
        $bplot->value->SetFormatCallback("cbFmtPercentage");        
        
        // Get colours scheme for this graph
        $colours_theme = array_slice( $this->colours_theme['horizontal'], 0, count($this->values) );        
        $bplot->SetFillColor( $colours_theme ); 
        
        $graph->Add($bplot);
        
        // FIXME: separate this method to other, see also abstract factory pattern
        $graph->Stroke();
    }
    
    function render( $type='horizontal' ) {
        $type = strtolower( $type );
        $methodName = $type.'Renderlet';
        
        if( method_exists($this, $methodName) ) {
            call_user_method($methodName, $this);
        }
    }
}



/* $lbl = array("Muy malo","Malo","Reguíar",'BñÑen','Muy bien');
$values = array(10, 32, 340, 13, 95);

$params = array('title' => "¿Qué te parece que google controle la ubicación de tu móvil?",
                'max_length_title' => 100, //30,
                'width' =>  480, //200,
                'height' => 360, //240,
                'labels' => $lbl,
                'values' => $values );

$graph = new PollGraph();

$graph->setOptions( $params );
$graph->render('vertical'); */