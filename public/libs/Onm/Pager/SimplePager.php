<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Pager;

/**
 * Class for generate image with next and previus.
 *
 * @package    Onm
 * @subpackage UI
 * @author     Sandra Pereira <sandra@openhost.es>
 * @version    SVN: $Id: SimplePager.class.php 28842 Mon July 4 16:37:26 2011 $
 */
class SimplePager {

    const ITEMS = 20;
    public $next = null;
    public $previus = null;



    public function __construct() {
 

    }

    /**
     * Returns the HTML paginate with js action onclick
     *
     * @param array  $params     the params for this function
     *  function-javascript function name, 'total' totalelements,
     *  'items' elements for page, and 'page' page number
     *
     * @return string    the HTML for pagination
     */
    public static function getPager($params = array()) {
 
        $html = '';
        $page = $params['page'];
        $items = $params['items'];       
        $total = $params['total'];
        $others =$params['others'];
        $function = $params['function'];

        $next = "<a style='cursor:pointer;' onClick='".$function."(".$others.",".($page+1).")' title='Next'> Next(".($page+1).") </a>";
        $previous = "<a style='cursor:pointer;' onClick='".$function."(".$others.",".($page-1).")' title='Previous'> Previous(".($page-1).") </a>";

        if ($page == 1 || empty($page)) {
            if($total <= $items) {
                $html ='';
            } else {
                $html.= "<li>";
                $html.= $next;
                $html.= "</li>";
            }
        } elseif ($total <= $items) {
                     $html.= "<li>";
                     $html.= $previous;
                     $html.= "</li>";
        } else {
            $html.= "<li>";
            $html.= $previous;
            $html.= "</li>";
            $html.= "<li>";
            $html.= $next;
            $html.= "</li>";
        }

        $output = "<ul id='menu' class='clearfix'>".$html."</ul>";

        

        return $output;
        
    }

     /**
     * Returns the HTML paginate with href params
     *
     * @param array  $params     the params for this function
     *  url-url to link, 'total' total elements,
     *  'items' elements for page, and 'page' page number
     *
     * @return string    the HTML for pagination
     */
      public static function getPagerUrl($params = array()) {

        $html = '';
        $page = $params['page'];
        $items = $params['items'];
        $total = $params['total'];

        $url = $params['url'];

        $next = "<a style='cursor:pointer;' href='".$url."&page=".($page+1)."' title='Next'> Next(".($page+1).") </a>";
        $previous = "<a style='cursor:pointer;' href='".$url."&page=".($page-1)."' title='Previous'> Previous(".($page-1).") </a>";

        if ($page == 1 || empty($page)) {
            if($total <= $items) {
                $html ='';
            } else {
                $html.= "<li>";
                $html.= $next;
                $html.= "</li>";
            }
        } elseif ($total <= $items) {
                     $html.= "<li>";
                     $html.= $previous;
                     $html.= "</li>";
        } else {
            $html.= "<li>";
            $html.= $previous;
            $html.= "</li>";
            $html.= "<li>";
            $html.= $next;
            $html.= "</li>";
        }

        $output = "<ul id='menu' class='clearfix'>".$html."</ul>";



        return $output;

    }


}
