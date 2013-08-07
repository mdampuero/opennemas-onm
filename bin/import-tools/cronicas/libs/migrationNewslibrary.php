<?php
/**
 * Migrate paths, urls & images sources in newslibrary frontpages
 *
 * @author sandra
 */

class migrationNewslibrary {

    public $categoriesData = array();

    public $helper = null;

    public function __construct ($configNewDB=array())
    {

        Application::initInternalConstants();

        self::initDatabaseConnections($configNewDB);
        $this->helper = new CronicasHelper('logLibrary.txt');

    }

    public static function initDatabaseConnections($configNewDB=array())
    {

        echo "Initialicing Onm database connection...".PHP_EOL;
        define('BD_HOST', $configNewDB['host']);
        define('BD_USER', $configNewDB['user']);
        define('BD_PASS', $configNewDB['password']);
        define('BD_TYPE', $configNewDB['type']);
        define('BD_DATABASE', $configNewDB['database']);

        $GLOBALS['application'] = new Application();

        Application::initDatabase();
    }


    /**
     * Extract category data of old db and insert these in new DB.
     * Drop example categories in new DB.
     *
     * @param array $categories categories ids of used contents
     *
     * @return bool
     */

    public function importCategories()
    {

      $this->categoriesData = ContentCategoryManager::get_instance()->categories;
      $this->categoryNames = array('0'=>'home', '1'=>'cronicas', '2' => 'galicia',
                    '3' => 'asturias', '4' => 'canarias', '5' => 'castillaleon',
                    '6' => 'cantabria', '7' => 'madrid', '8' => 'baleares',
                    '9' => 'andalucia', '15'=>'paisvasco'  );
      return;

    }

    public function migrateAllDirs($iniDate ='20120331',$endDate ='20120101') {

        while($iniDate >= $endDate) {

            foreach($this->categoryNames as $catName) {
                $path = OLD_LIBRARY."/{$iniDate}/{$catName}.html";
                $html =file_get_contents($path);
                if(!empty($html)) {
                    $htmlOut = $this->migrateSources( $html );
                    $htmlOut = $this->migrateSourcesImages( $htmlOut );
                    $htmlOut = $this->migrateUrls( $htmlOut );
                    $htmlOut = $this->migrateOtherUrls($htmlOut);


                    $date =  new DateTime($iniDate);
                    $directoryDate = $date->format("/Y/m/d/");
                    $basePath = MEDIA_PATH.'/library'.$directoryDate;
                    if( !file_exists($basePath) ) {
                        mkdir($basePath, 0777, true);
                    }
                    $newFile =  $basePath."{$catName}.html"  ;

                    $result = file_put_contents($newFile, $htmlOut);
                }
                if(!$result) {
                    $this->helper->log(" Problem with {$path} file");
                }

            }
            $m = $date->format("m");
            $y = $date->format("Y");
            $d = $date->format("d");
            $ayer  = mktime(0, 0, 0, $m  , $d-1, $y);
            $iniDate = date("Ymd", $ayer);

        } //   while

    }

    // test in one file
    public function migrateTest() {
        $catName ='home';
        $path = OLD_LIBRARY."test/";
         foreach($this->categoryNames as $catName) {
           $oldFile =$path."/{$catName}.html";
           echo $oldFile."\n";
           if(file_exists($oldFile) ) {
            $html = file_get_contents($oldFile);

            $htmlOut = $this->migrateSources( $html );
            $htmlOut = $this->migrateSourcesImages( $htmlOut );
            $htmlOut = $this->migrateUrls( $htmlOut );
            $htmlOut = $this->migrateOtherUrls($htmlOut);

            $directoryDate =date("/Y/m/d/");
            $basePath = MEDIA_PATH.'/'.'library'.$directoryDate;
            if(!file_exists($basePath) ) {
                mkdir($basePath, 0777, true);
            }
            $newFile =  $basePath."{$catName}.html"  ;
            $result = file_put_contents($newFile, $htmlOut);
            var_dump($path."{$catName}.html");
            var_dump($newFile);
            if(!$result) {
                $this->helper->log(" Problem with {$path} file");
                var_dump("problem with {$path} \n");
            }
           }
         }
    }

    public function migrateSources($html) {

         /* URL's/CSS/JS/images
         *
         * themes/default/css->/themes/cronicas/css/old/.....
         * /js/->/themes/cronicas/js/old/
         * /js/scriptaculous/
         * /themes/default/images/
         * /themes/default/images/facebook.png
         * /hemeroteca/castillaleon/2012-02-07/
         */

        $replacements = array(
                            '@/themes/default/images/@' => '/themes/cronicas/images/old/' ,
                            '@/themes/default/css/@'    => '/themes/cronicas/css/old/',
                            '@/js/(.*)\.js@'            => '/themes/cronicas/js/old/$1.js',
                            '@/hemeroteca/(.*)/([0-9]{4}-[0-1][0-9]-[0-3][0-9])/@'  => '/hemeroteca/$1/$2.html',
                        );

        $htmlResult = preg_replace(
                        array_keys($replacements), array_values($replacements),
                        $html
        );

        return $htmlResult;
    }


    /*
     *
     * images->/media/instance_name/...
          /media/images/galicia/CE201003031459237N0A9655.JPG
     *  /media/cronicas/images/2011/02/03/2010030314599655.jpg
     *
          /media/images/advertisements/PU20081001CYL_ES_IDA_230X104.swf
          /media/images/opinion/OP20090402100648ManuelCorr.jpg
    */

    public function migrateSourcesImages($html) {

        $htmlResult = $html;
        $result = array();
        $patterns =  array();
        $replacements = array();
        preg_match_all('@src?=?\"/media/images/([^"])*@', $html, $result);

        $newImage = new Photo();
        foreach($result[0] as $res) {

            //get the path for search in db
            $path = $res;
            $img = preg_replace('@src?=?\"/media/images/@', '',$path);


            $imageID = $this->helper->imageIsImported($img, 'image');
            if(!empty($imageID)) {
                $newImage->read($imageID);
                $replacements[] =" src=\"/media/cronicas/images{$newImage->path_file}{$newImage->name} ";
                $patterns[] = "@{$res}@";

            }else{
                 $this->helper->log("Problem with no image {$img} imported - {$path} .\n");
            }
        }
        $htmlResult = preg_replace($patterns, $replacements, $html);

        return $htmlResult;

    }

    /*
     *  /articulo/cronicas/2010-03-15/anna-terron-tomo-posesion-secretaria-estado-inmigracion-emigracion/7627.html
        /album/cronicas/2010-06-06/toma-posesion-anna-terron-secretaria-estado-emigracion/7638.html
        /opinion/2010-03-29/arredor-da-singradura-emigrante/7644.html
        /especiales/castillaleon/2012-03-20/congreso-internacional-asociacionismo-emigracion-espanola-exterior-significaciones-vinculaciones/15918.html
        /letter/2012-03-26/buscando-parientes-espana/15947.html

     * /media/files/galicia/GA20090402Nacionalidad.pdf
     */

    public function migrateUrls($html) {


        $patterns =  array();
        $replacements = array();
        $result = array();

        preg_match_all('@(href ?= ?"/)(.*/)(\d+)\.html ?@', $html, $result, PREG_SET_ORDER);
        $newContent = new Content();
        foreach($result as $res) {

            $contentID =$res[3];

            $elementID = $this->helper->elementTranslate($contentID);
            if(!empty($elementID)) {
                $content = $newContent->get($elementID);

                $patterns[] = "@{$res[0]}@";
                $replacements[] ='href="/'.$content->uri;
            }else{
               $this->helper->log("Problem with element {$contentID} -> {$res[0]}.\n");

            }
        }

        $htmlResult = preg_replace($patterns, $replacements, $html);

        return $htmlResult;
    }

    /*
     *    /cronicas/ ->/seccion/cronicas/
     *    /galicia/ ->/seccion/galicia/
     *   /opiniones/  ->opinion
     */
    public function migrateOtherUrls($html) {

         $patterns = array( '@href="/cronicas/"@', '@href="/galicia/"@',
                            '@href="/castillaleon/"@','@href="/asturias/"@',
                            '@href="/madrid/"@','@href="/canarias/"@', '@href="/paisvasco/"@',
                            '@href="/andalucia/"@','@href="/cantabria/"@','@href="/baleares/"@',
                            '@href="/opiniones/"@','@href="/cartas.html#escribir_carta"@');

         $replacements = array( 'href="/seccion/cronicas/"','href="/seccion/galicia/"',
                            'href="/seccion/castillaleon/"','href="/seccion/asturias/"',
                            'href="/seccion/madrid/"','href="/seccion/canarias/"','href="/seccion/paisvasco/"',
                            'href="/seccion/andalucia/"','href="/seccion/cantabria/"','href="/seccion/baleares/"',
                            'href="/opinion/"','href="/cartas-al-director/"');

         $htmlResult = preg_replace($patterns, $replacements, $html,  -1, $count);


         return $htmlResult;

     }

     public function someImprovements() {
         //Added newslibrary calendar.
         //div id="dcalback"
         /*
          *
          *   <div id="dcalback">
    <div id="calendar">
        <script type="text/javascript">
            var mostra_calendar=function()  { navigate("10-04-2012","galicia"); }
            Event.observe(window, 'load', mostra_calendar );
        </script>
    </div>
</div>
          */

     }

     /*
     htaccess->    /hemeroteca/home/2010-03-05/ -> /hemeroteca/2010-03-05/home.html



         /agenda/ ->ok
         /agenda/10/espana.html ->ok

         /album/galicia/ ->ok
         /rss/home/ ->ok

         /especiales/ ->404
         /especiales/cronicas/->404


         /cartas.html#escribir_carta ->cartas-al-director


     */
}
