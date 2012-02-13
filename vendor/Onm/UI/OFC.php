<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\UI;
/**
 * Class for generate charts with Open Flash Charts.
 *
 * @package    Onm
 * @subpackage Common
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    Git: $Id: ofc.class.php Mar Xuñ 28 21:48:06 2011 frandieguez $
 */
class OFC
{

    static function graphicViewed($data)
    {

        $bar = new \bar_outline(50, '#87ADD0', '#014687');
        $bar->key('Visitas', 10);

        foreach ($data as $item) {
            if (strlen($item['title']) < 50) {
                $title = $item['title'];
            } else {
                $title = substr($item['title'], 0, 47).'...';
            }
            $tip = $title.'<br>Visitas: '.$item['views'];
            $bar->add_data_tip($item['views'], $tip);
        }

        $g = new \graph();
        $g->set_bg_colour('#ffffff');

        $g->set_base('/libs/ofc1/');
        $g->set_swf_path('/libs/ofc1/');

        $g->data_sets[] = $bar;
        $g->set_tool_tip('#tip#');
        $g->x_axis_colour('#002B53', '#E0E0E0');
        $g->y_axis_colour('#002B53', '#E0E0E0');

        $top = max($bar->data);
        $g->set_y_max($top);
        $g->y_label_steps(10);


        $g->set_width('100%');
        $g->set_height(250);

        $g->set_output_type('js');

        return $g->render();

    }

    static function graphicComented($data)
    {

        $bar = new \bar_outline(50, '#87ADD0', '#014687');
        $bar->key('Comentarios', 10);

        foreach ($data as $item) {
            if (strlen($item['title'])<50) {
                $title = $item['title'];
            } else {
                $title = substr($item['title'], 0, 47).'...';
            }
            $tip = $title.'<br>Comentarios: '.$item['num'];
            $bar->add_data_tip($item['num'], $tip);
        }

        $g = new \graph();
        $g->set_bg_colour('#ffffff');
        $g->set_base('/libs/ofc1/');
        $g->set_swf_path('/libs/ofc1/');

        $g->data_sets[] = $bar;
        $g->set_tool_tip('#tip#');
        $g->x_axis_colour('#002B53', '#E0E0E0');
        $top = max($bar->data);
        $g->set_y_max($top);
        $g->y_label_steps(10);
        $g->y_axis_colour('#002B53', '#E0E0E0');

        $g->set_width('100%');
        $g->set_height(250);

        $g->set_output_type('js');

        return $g->render();

    }

    static function graphicVoted($data)
    {

        $bar = new \bar_outline(50, '#87ADD0', '#014687');
        $bar->key('Valoración', 10);

        foreach ($data as $item) {
            if (strlen($item['title']) < 50) {
                $title = $item['title'];
            } else {
                $title = substr($item['title'], 0, 47).'...';
            }
            $tip = $title.'<br>Votos: '.$item['total_votes'].'<br>Valoración: '.$item['rate'];
            $bar->add_data_tip($item['rate'], $tip);
        }

        $g = new \graph();
        $g->set_bg_colour('#ffffff');
        $g->set_base('/libs/ofc1/');
        $g->set_swf_path('/libs/ofc1/');

        $g->data_sets[] = $bar;
        $g->set_tool_tip('#tip#');
        $g->x_axis_colour('#002B53', '#E0E0E0');
        $top = max($bar->data);
        $g->set_y_max($top);
        $g->y_label_steps(10);
        $g->y_axis_colour('#002B53', '#E0E0E0');

        $g->set_width('100%');
        $g->set_height(250);

        $g->set_output_type('js');

        return $g->render();

    }

}
