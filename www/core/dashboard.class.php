<?php
/**
 * This class
 *
 * @author toni
 */
class Dashboard {

    function Dashboard() {
        //Construtor
    }

    function __construct() {
        $this->Dashboard();
    }

    static function getMostViewed($content_type,$category=0,$days=3) {
        $cm = new ContentManager();
        $mostVisitedContentObjects = $cm->cache->getMostViewedContent($content_type, false, $category, 0, $days, 10,true);

        $mostVisitedContent = array();
        for ($i=0;$i<count($mostVisitedContentObjects);$i++) {
            $mostVisitedContent[$i]['pk_content'] = $mostVisitedContentObjects[$i]->id;
            $mostVisitedContent[$i]['title'] = $mostVisitedContentObjects[$i]->title;
            $mostVisitedContent[$i]['views'] = $mostVisitedContentObjects[$i]->views;
            $mostVisitedContent[$i]['permalink'] = $mostVisitedContentObjects[$i]->permalink;
        }

        return $mostVisitedContent;
    }

    static function viewedTable($items, $title) {
            $html_output = "<div class=\"dashboardBox\">".$title."";

            if (count($items)>0) {
                $html_output .= OFC::graphicViewed($items);
                $html_output .= "<div class=\"table\">";
                $html_output .= "<table class=\"adminlist\" border=0><tr>";
                $html_output .= "<th align=\"center\" style=\"width:20%;\">Visitas</th>";
                $html_output .= "<th style=\"width:80%\">T&iacute;tulo</th>";
                $html_output .= "</tr>";

                foreach ($items as $article) {
                    $html_output .= "<tr>";
                    $html_output .= "<td align=\"center\" style=\"width:20%;\">".$article["views"]."</th>";
                    $html_output .= "<td style=\"width:80%\"><a href=\"".$article["permalink"]."\" target=\"_blank\">".$article["title"]."</th>";
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
                $html_output .= OFC::graphicComented($items);
                $html_output .= "<div class=\"table\">";
                $html_output .= "<table class=\"adminlist\" border=0>";
                $html_output .= "<tr>";
                $html_output .= "<th align=\"center\" style=\"width:20%;\">Comentarios</th>";
                $html_output .= "<th style=\"width:80%\">T&iacute;tulo</th>";
                $html_output .= "</tr>";

                foreach ($items as $article) {
                    $html_output .= "<tr>";
                    $html_output .= "<td align=\"center\" style=\"width:20%;\">".$article["num"]."</th>";
                    $html_output .= "<td style=\"width:80%\"><a href=\"".$article["permalink"]."\" target=\"_blank\">".$article["title"]."</th>";
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
                $html_output .= OFC::graphicVoted($items);
                $html_output .= "<div class=\"table\">";
                $html_output .= "<table class=\"adminlist\" border=0>";
                $html_output .= "<tr>";
                $html_output .= "<th align=\"center\" style=\"width:10%;\">Votos</th>";
                $html_output .= "<th align=\"center\" style=\"width:10%;\">Puntuaci&oacute;n</th>";
                $html_output .= "<th style=\"width:60%\">T&iacute;tulo</th>";
                $html_output .= "</tr>";

                foreach ($items as $article) {
                    $html_output .= "<tr>";
                    $html_output .= "<td align=\"center\">".$article["total_votes"]."</th>";
                    $html_output .= "<td align=\"center\">".$article["rate"]."</th>";
                    $html_output .= "<td><a href=\"".$article["permalink"]."\" target=\"_blank\">".$article["title"]."</th>";
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
