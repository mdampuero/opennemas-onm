<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class for handling Dashboard action
 *
 * @package Onm
 */
class Dashboard {

    function __construct() {
    }

    static function getMostViewed($content_type,$category=0,$days=3) {
        $cm = new ContentManager();

        $mostVisitedContentObjects = $cm->cache->getMostViewedContent($content_type, false, $category, 0, $days, 10,true);
        $mostVisitedContent = array();
        for ($i=0;$i<count($mostVisitedContentObjects);$i++) {
            $mostVisitedContent[$i]['pk_content'] = $mostVisitedContentObjects[$i]->id;
            $mostVisitedContent[$i]['title'] = $mostVisitedContentObjects[$i]->title;
            $mostVisitedContent[$i]['views'] = $mostVisitedContentObjects[$i]->views;
            $mostVisitedContent[$i]['permalink'] = Uri::generate('article',
                            array(
                                'id' => $mostVisitedContentObjects[$i]->id,
                                'date' => date('Y-m-d', strtotime($mostVisitedContentObjects[$i]->created)),
                                'category' => $cm->get_categoryName_by_contentId($mostVisitedContentObjects[$i]->id),
                                'slug' => StringUtils::get_title($mostVisitedContentObjects[$i]->title),
                            )
                        );;
        }

        return $mostVisitedContent;
    }

    static function viewedTable($items, $title) {
            $html_output = "<div class=\"dashboardBox\">".$title."";

            if (count($items)>0) {
                $html_output .= \Onm\UI\OFC::graphicViewed($items);
                $html_output .= "<div class=\"table\">";
                $html_output .= "<table class=\"adminlist\" border=0><thead><tr>";
                $html_output .= "<th align=\"center\" style=\"width:5%;\">Visitas</th>";
                $html_output .= "<th>T&iacute;tulo</th>";
                $html_output .= "</tr></thead>";
                foreach ($items as $article) {
                    $html_output .= "<tr>";
                    $html_output .= "<td align=\"center\">".$article["views"]."</th>";
                    $html_output .= "<td><a href=\"".SITE_URL.$article["permalink"]."\" target=\"_blank\">".$article["title"]."</th>";
                    $html_output .= "</tr>";
                }

                $html_output .= "</table>";
                $html_output .= "</div>";
            } else {
                $html_output .= "<p style=\"margin:5px;  color:red;\">Sin datos obtenidos para este periodo de tiempo</p>";
            }

            $html_output .= "</div>";
            $html_output .= "<div class=\"clearer\"></div>";

            return $html_output;
    }

    static function getMostComented($content_type,$category=0,$days=3) {
        $cm = new ContentManager();
        $mostComentedContentObjects = $cm->cache->getMostComentedContent($content_type, false, $category, $days, 10, true);
        return $mostComentedContentObjects;
    }

    static function comentedTable($items, $title) {
            $html_output = "<div class=\"dashboardBox\">".$title."";

            if (count($items)>0) {
                $html_output .= \Onm\UI\OFC::graphicComented($items);
                $html_output .= "<div class=\"table\">";
                $html_output .= "<table class=\"adminlist\" border=0>";
                $html_output .= "<thead><tr>";
                $html_output .= "<th align=\"center\" style=\"width:5%;\">Comentarios</th>";
                $html_output .= "<th >T&iacute;tulo</th>";
                $html_output .= "</tr></thead>";
                foreach ($items as $article) {
                    $html_output .= "<tr>";
                    $html_output .= "<td align=\"center\"".$article["num"]."</th>";
                    $html_output .= "<td><a href=\"".SITE_URL.$article["permalink"]."\" target=\"_blank\">".$article["title"]."</th>";
                    $html_output .= "</tr>";
                }

                $html_output .= "</table></div>";
            } else {
                $html_output .= "<p style=\"margin:5px;  color:red;\">Sin datos obtenidos para este periodo de tiempo</p>";
            }

            $html_output .= "</div>";
            $html_output .= "<div class=\"clearer\"></div>";

            return $html_output;
    }

    static function getMostVoted($content_type,$category=0,$days=3) {
        $cm = new ContentManager();
        $mostVotedContentObjects = $cm->cache->getMostVotedContent($content_type, false, $category, 0, $days, 10, true);

        $mostVotedContent = array();
        for ($i=0;$i<count($mostVotedContentObjects);$i++) {
            if ($mostVotedContentObjects[$i]->total_votes > 0) {
                $mostVotedContent[$i]['pk_content'] = $mostVotedContentObjects[$i]->id;
                $mostVotedContent[$i]['title'] = $mostVotedContentObjects[$i]->title;
                $mostVotedContent[$i]['total_votes'] = $mostVotedContentObjects[$i]->total_votes;
                $mostVotedContent[$i]['rate'] = round($mostVotedContentObjects[$i]->total_value/$mostVotedContentObjects[$i]->total_votes,2);
                $mostVotedContent[$i]['permalink'] = $mostVotedContentObjects[$i]->permalink;
            }
        }

        return $mostVotedContent;
    }

    static function votedTable($items, $title) {
            $html_output = "<div class=\"dashboardBox\">".$title."";

            if (count($items)>0) {
                $html_output .= \Onm\UI\OFC::graphicVoted($items);
                $html_output .= "<div class=\"table\">";
                $html_output .= "<table class=\"adminlist\" border=0>";
                $html_output .= "<thead><tr>";
                $html_output .= "<th align=\"center\" style=\"width:5%;\">Votos</th>";
                $html_output .= "<th align=\"center\" style=\"width:5%;\">Puntuaci&oacute;n</th>";
                $html_output .= "<th>T&iacute;tulo</th>";
                $html_output .= "</tr></thead>";

                foreach ($items as $article) {
                    $html_output .= "<tr>";
                    $html_output .= "<td align=\"center\">".$article["total_votes"]."</th>";
                    $html_output .= "<td align=\"center\">".$article["rate"]."</th>";
                    $html_output .= "<td><a href=\"".SITE_URL.$article["permalink"]."\" target=\"_blank\">".$article["title"]."</th>";
                    $html_output .= "</tr>";
                }

                $html_output .= "</table></div>";
            } else {
                $html_output .= "<p style=\"margin:5px;  color:red;\">Sin datos obtenidos para este periodo de tiempo</p>";
            }

            $html_output .= "</div>";
            $html_output .= "<div class=\"clearer\"></div>";

            return $html_output;
    }

}
