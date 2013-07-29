<?php
/**
 * Defines the Onm\Pager\SimplePager class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_UI
 */
namespace Onm\Pager;

/**
 * Class for generate image with next and previous.
 *
 * @package    Onm_UI
 */
class SimplePager
{
    /**
     * Returns the HTML paginate with js action onclick
     *
     * @param array $params the params for this function
     *                      'function' javascript function name,
     *                      'total' totalelements,
     *                      'items' elements for page,
     *                      and 'page' page number
     *
     * @return string the HTML for pagination
     */
    public static function getPager($params = array())
    {

        $html      = '';
        $page      = $params['page'];
        $items     = $params['items'];
        $total     = $params['total'];
        $others    = $params['others'];
        $function  = $params['function'];

        $next     = "<a onClick='".$function."(".$others.", "
                    .($page+1).")' title='"
                    ._("Get next page")."'>"._("Next »")."</a>\n";
        $previous = "<a onClick='".$function."(".$others.", "
                    .($page-1).")' title='"
                    ._("Get previous page")."'>"._("« Previous")."</a>\n";

        if ($page == 1 || empty($page)) {
            if ($total <= $items) {
                $html = '';
            } else {
                $html .= "<li>{$next}</li>\n";
            }
        } elseif ($total <= $items) {
            $html.= "<li>{$previous}</li>\n";
        } else {
            $html.= "<li>{$previous}</li>\n";
            $html.= "<li>{$next}</li>\n";
        }

        $output = "<ul id='simplepager' class='clearfix'>\n".$html."\n</ul>\n";

        return $output;
    }

     /**
     * Returns the HTML paginate with href params
     *
     * @param array $params the params for this function
     *                           url-url to link, 'total' total elements,
     *                           'items' elements for page, and 'page' page number
     *
     * @return string the HTML for pagination
     */
    public static function getPagerUrl($params = array())
    {
        $html  = '';
        $page  = $params['page'];
        $items = $params['items'];
        $total = $params['total'];

        $url = $params['url'];

        $pageComponent = (strpos($url, '?')) ? '&page=' : '?page=';

        $next = sprintf("<a href='%s%s%d' title='Next'>%s</a>", $url, $pageComponent, $page+1, _('Next »'));
        $previous = sprintf("<a href='%s%s%d' title='Next'>%s</a>", $url, $pageComponent, $page-1, _('« Previous'));

        if (empty($page) || $page == 1) {
            if ($total <= $items) {
                $html ='';
            } else {
                $html.= "<li class='next'>";
                $html.= $next;
                $html.= "</li>";
            }
        } elseif ($total <= $items) {
            $html .= "<li class='previous'>";
            $html .= $previous;
            $html .= "</li>";
        } else {
            $html .= "<li class='previous'>";
            $html .= $previous;
            $html .= "</li>";
            $html .= "<li class='next'>";
            $html .= $next;
            $html .= "</li>";
        }

        $output = "<ul class='pager'>".$html."</ul>";

        return $output;
    }
}
