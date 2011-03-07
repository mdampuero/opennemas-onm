<?php

class ImportHelper {
    
    private $logFile = '';
    
    public function __construct($params = '')
    {
        $this->logFile = $params;
        spl_autoload_register('ImportHelper::autoload');
    }
    
    static public function autoload($className) {
        $filename = strtolower($className);
    
        $includePaths = explode(':', get_include_path());
        // Try convert MethodCacheManager to method_cache_manager
        $filename = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));
    
        foreach ($includePaths as $path) {
            if (file_exists($path.DIRECTORY_SEPARATOR.$filename.'.class.php')) {
                require $path.DIRECTORY_SEPARATOR.$filename.'.class.php';
            }
        }
    }
    
    public function logElementInsert($original, $final, $type) {

        $sql_translation_request =
                'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $translation_values = array($original, $final, $type);
        $translation_ids_request = $GLOBALS['application']->conn->Prepare($sql_translation_request);
        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request,
                                                      $translation_values);
        
        if (!$rss) {
            echo $GLOBALS['application']->conn->ErrorMsg();
        }
    
    }

    
    public function log($text = null) {
        if(isset($text) && !is_null($text) ) {
            $handle = fopen( $this->logFile , "wb");
            if ($handle) {
                $datawritten = fwrite($handle, $text);
                fclose($handle);
            } else {
                echo "There was a problem while trying to log your message.";
            }
        }
    }
    
    public function updateViews($contentID, $views)
    {
        if(isset($contentID) && isset($views)) {
            $sql = 'UPDATE `contents` SET `views`=? WHERE pk_content=?';
            
            $values = array($contentID, $views);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql,
                                                          $values);
            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            }
            
        } else {
            echo "Please provide a contentID and views to update it.";
        }
    }
    
    static public function dumpObjectToString($object)
    {
        if (is_object($object)) {
            $reflect = new ReflectionClass($object);
            $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);
            
            $finalString = '';
            foreach ($props as $prop) {
                $finalString .= $prop->getName()."-> ".$object->{$prop}.", ";
            }
            return $finalString."\n";
        } else {
            return 'no';
        }
    }
    
    public function messageStatus($text)
    {
        system('clear');
        $date = date('d.m.Y');
        //$percent = ($total)? floor($current*100/$total): 0;
        echo sprintf("[%s] %s", $date, $text);
    }
    
    static public function getCmdOpts() {
    
        $opts = getopt("t:s:");
        return $opts;
    }
}
