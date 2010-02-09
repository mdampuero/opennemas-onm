<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
define('MEDIA_PER_PAGE', 24);

/**
 * Class MediaItem, represents a media file.
 * This class manipulate a swf, jpg, png, ... files. It's possible extract
 * all information to print this resource. By example: generate tags <embed ...
 * to the swf file.
*/
class MediaItem {
    /* Absolute path and file name */
    var $filename = null;
    var $basename = null;

    /* Details of media resource */
    var $size   = null;
    var $width  = null;
    var $height = null;
    var $attrs  = null;
    var $type   = null;
    var $internalType = null;

    /* Details of file */
    var $atime = null;
    var $mtime = null;

    /* Metadata */
    var $description = null;
    var $tags = null;

    function MediaItem($file) {
        $this->filename = realpath( $file );
        $this->basename = basename($this->filename);

        // Details of file
        $details = @stat( $this->filename );

		$this->mtime	= $details['mtime'];
		$this->size     = $details['size'];
		$dimensions     = $this->getDimensions($this->filename);
		$this->width    = $dimensions[0];
		$this->height   = $dimensions[1];
		$this->attrs    = $dimensions[3];
        $this->type     = $this->getExtension();
        $this->internalType = $dimensions[2];
    }

    function __construct($file) {
        $this->MediaItem($file);
    }

    function getDimensions($filename=null) {
        if(is_null($filename)) {
            if(is_null($this->filename)) {
                return(null);
            }
		$filename = $this->filename;
        }

        $details = array();
        $details = @getimagesize($filename);

        return($details);
    }

    function getExtension($filename=null) {
        if(is_null($filename)) {
            if(is_null($this->filename)) {
                return(null);
            }

			$filename = $this->filename;
        }

        $_d = pathinfo($filename);

        return( strtoupper($_d['extension']) );
    }

    function getHTMLTag() {

    }
}

class MediaManager {
    var $_dirs = array();
    var $_files = array();

    var $_filter = NULL;
    var $pager   = NULL;

    function MediaManager() {
        $this->_filter = '/\.(jpg|jpeg|gif|png|swf|flv|zip|tar|gz)$/i';
        //$this->_filter = '/\.(.*?)$/i';
    }

    function __construct() {
        $this->MediaManager();
    }

    function listFiles($path) {
    	$files = array();
    	$path = realpath($path).'/';
		if (is_dir($path)) {
		    if ($dh = opendir($path)) {
		        while (($file = readdir($dh)) !== false) {
		        	if(!preg_match('/^\./', $file) && preg_match($this->_filter, $file)) {
		            	$files[] = new MediaItem($path . $file);
		        	}
		        }
		        closedir($dh);
		    }
		}

		usort($files, array('MediaManager', 'cmp_date'));

		return(array_reverse($files));
    }

    function listFilesByWeek($arr) {
        // Array resultado
        $result = array();
        
        if (is_array($arr)) {
            // Ordenar por fecha
            $this->sort($arr, 'dD');

            for($i=0; $i<count($arr); $i++) {
                $W = date('W', $arr[$i]->mtime);
                $Y = date('Y', $arr[$i]->mtime);
                $monday = $this->getFirstDayWeek($W, $Y);

                if(!isset($result[ $monday ][ 'week' ])) {
                    $result[ $monday ][ 'week' ] = $this->printWeek($W, $Y);
                }
                $result[ $monday ][ 'files' ][] = $arr[$i];
            }

            // Establece las nuevas claves 0,1,2,... en vez del timestamp
            $result = array_values($result);

        }


        return( $result );
    }

    function sort($arr, $criteria) {
        $initial = $criteria{0};
        switch($initial) {
            // Ordenar por nombre (name)
            case 'n':
                usort($arr, array('MediaManager', 'cmp_name'));
            break;

            // Ordenar por fecha de creación (date)
            case 'd':
                usort($arr, array('MediaManager', 'cmp_date'));
            break;

            // Ordenar por tamaño (size)
            case 's':
                usort($arr, array('MediaManager', 'cmp_size'));
            break;

            case 'r':
                usort($arr, array('MediaManager', 'cmp_resolution'));
            break;

            case 't':
                usort($arr, array('MediaManager', 'cmp_type'));
            break;

            default:
                usort($arr, array('MediaManager', 'cmp_name'));
            break;
        }

        if($criteria{1} == 'D') {
            $arr = array_reverse($arr);
        }

        return($arr);
    }

    function generateTree($path, $current_path) {
        $xml = '';
        $id = str_replace('/', '-', str_replace('.', '',$path));
        $id = str_replace('\\', '', str_replace(':', '',$id)); // Bug windows path

        $path = realpath($path).'/';

        if(is_dir($path) && !preg_match('/\./', $path)) {
            $listmode = (isset($_REQUEST['listmode']))? $_REQUEST['listmode']: 'details';
            $xml .= '<submenu nombre="'.basename($path).'" enlace="?path='.$path.'&amp;listmode='.$listmode.'"';
			if( preg_match('|^'.$current_path.'\/?$|', $path) ) {
				$xml .= ' highlight="#eee"';
			}
            $xml .= ' target="_self" id="'.$id.'">'."\n";

            if($d = opendir($path)) {
                while(($file=readdir($d))!==false) {
                    if(is_dir($path.$file) && !preg_match('/\./', $path.$file)) {
                        $xml = $xml . $this->generateTree($path.$file, $current_path);
                    }
                }
            }

            $xml .= '</submenu>'."\n";
        }

        return($xml);
    }

    // TODO: Incluir más opciones para personalizar la paginación
    function paginate($items) {
        $_items = array();

        foreach($items as $v) {
            $_items[] = $v->filename;
        }

        $items_page = (defined(MEDIA_PER_PAGE))?MEDIA_PER_PAGE: 24;

        $params = array(
            'itemData' => $_items,
			'perPage' => $items_page,
			'delta' => 1,
			'append' => true,
			'separator' => '|',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator' => 1,
			'clearIfVoid' => true,
			'urlVar' => 'page',
			'mode'  => 'Sliding',
            'linkClass' => 'pagination',
            'altFirst' => 'primera p&aacute;gina',
            'altLast' => '&uacute;ltima p&aacute;gina',
            'altNext' => 'p&aacute;gina seguinte',
            'altPrev' => 'p&aacute;gina anterior',
            'altPage' => 'p&aacute;gina'
        );

        $this->pager = &Pager::factory($params);
        $data  = $this->pager->getPageData();

		$result = array();
		foreach($items as $k => $v) {
			if( in_array($v->filename, $data) ) {
                $result[] = $v; // Array 0-n compatible con sections Smarty
			}
		}

		return($result);
    }

    /**
     * Crear la miniatura de una imagen
     * @static
    */
    function miniatura($image, $width, $height) {
        if(!file_exists($image)) {
            header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
            exit(0);
        }

        $dir_cache = dirname(__FILE__)."/../cache/thumbs/";
        $file_info = pathinfo($image);

        $thumbnail = new PThumb();
        $thumbnail->use_cache = true;
        $thumbnail->cache_dir = $dir_cache;
        $thumbnail->error_mode = 2;

        $data = $thumbnail->fit_thumbnail($image, $width, $height, 1, true);
        if(!$data) {
            header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
            exit(0);
        }

        $data = $thumbnail->print_thumbnail($image, $data[0], $data[1], true);
        if(!$data) {
            //$thumbnail->display_x();
            header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
            exit(0);
        }

        // Salida de la imagen
        header('Content-Type: image/'.$file_info['extension']);
        echo("thumnail".$data);
        exit(0);
    }

    /* callbacks para ordenar los listados de ficheros */
    function cmp_name($a, $b) {
        return strcmp($a->basename, $b->basename);
    }

	function cmp_date($a, $b) {
		if($a->mtime == $b->mtime) {
			return(0);
		} elseif($a->mtime < $b->mtime) {
			return(-1);
		}

		return(1);
	}

    function cmp_size($a, $b) {
        if($a->size == $b->size) {
            return(0);
        } elseif($a->size < $b->size) {
            return(-1);
        }

        return(1);
    }

    function cmp_resolution($a, $b) {
        $a_res = $a->width * $a->height;
        $b_res = $b->width * $b->height;
        if($a_res == $b_res) {
            return(0);
        } elseif($a_res < $b_res) {
            return(-1);
        }

        return(1);
    }

    function cmp_type($a, $b) {
        return strcmp($a->type, $b->type);
    }

    /* Funciones de fecha */
    // Recuperar el lunes de la semana en formato timestamp
    function getFirstDayWeek($week, $year) {
        $days = ($week*7);
        $offset = date('w', mktime(0, 0, 0, 1, $days, $year));
        $leap_year = date('L', mktime(0, 0, 0, 1, 1, $year));

        if($offset != 0) {
            $days += (7 - $offset);
        }

        if($leap_year) {
            $days -= 7;
        }

        $tm0 = mktime(0, 0, 0, 1, $days-6, $year);

        return( $tm0 );
    }

    function getDaysWeek($week, $year) {
        /* $leap_year = (date('L', mktime(0, 0, 0, 1, 1, $year)) + 1) % 2;
        $days = ($week-1)*7 + $leap_year;

        $tm0 = mktime(0, 0, 0, 1, $days, $year);
        $tm1 = mktime(0, 0, 0, 1, $days+6, $year);

        return( array('I' => array(date("d", $tm0), date("m", $tm0)),
                      'F' => array(date("d", $tm1), date("m", $tm1)) ) ); */

        // Buscar o domingo
        $days = ($week*7);
        $offset = date('w', mktime(0, 0, 0, 1, $days, $year));
        $leap_year = date('L', mktime(0, 0, 0, 1, 1, $year));

        if($offset != 0) {
            $days += (7 - $offset);
        }

        if($leap_year) {
            $days -= 7;
        }

        $tm0 = mktime(0, 0, 0, 1, $days-6, $year);
        $tm1 = mktime(0, 0, 0, 1, $days, $year);

        return( array('I' => array(date("d", $tm0), date("m", $tm0), date("Y", $tm0)),
                      'F' => array(date("d", $tm1), date("m", $tm1), date("Y", $tm1)) ) );
    }

    function printWeek($week, $year) {
        $meses = array('', 'Enero',   'Febrero', 'Marzo',
                       'Abril',   'Mayo',    'Junio',
                       'Julio',   'Agosto',  'Septiembre',
                       'Octubre', 'Noviembre', 'Diciembre');
        $d = $this->getDaysWeek($week, $year);

        $str = 'Lunes, '.intval($d['I'][0]).'-'.$meses[ intval($d['I'][1]) ]." - ".
               'Domingo, '.intval($d['F'][0]).'-'.$meses[ intval($d['F'][1]) ];

        return( $str );
    }
}

?>
