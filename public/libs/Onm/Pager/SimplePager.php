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
     * Returns the HTML paginate
     *
     * @param string  $function  name of javascript function
     * @param array  $params     the params for this function
     *
     * @return string    the HTML for this menu
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


}
