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
class Dashboard
{

    public static function getMostViewed($contentType, $category=0, $days=3)
    {
        $cm = new ContentManager();

        $contentObjects = $cm->cache->getMostViewedContent($contentType,
            false, $category, 0, $days, 10, true);

        $contents = array();
        foreach ($contentObjects as $content) {

            $contents [] = array(
                'pk_content' => $content->id,
                'title'      => $content->title,
                'views'      => $content->views,
                'permalink'  => Uri::generate(
                    'article',
                    array(
                        'id'       => sprintf('%06d', $content->id),
                        'date'     => date('YmdHis', strtotime($content->created)),
                        'category' => $cm->getCategoryNameByContentId($content->id),
                        'slug'     => \Onm\StringUtils::get_title($content->title),
                    )
                ),

            );
        }

        return $contents;
    }

    public static function viewedTable($items, $title)
    {
        $output = "<div class=\"dashboardBox\">".$title."";

        if (count($items) > 0) {
            $output .= \Onm\UI\OFC::graphicViewed($items);
            $output .= "<div class=\"table\">";
            $output .= "<table class=\"table table-hover table-condensed\" border=0><thead><tr>";
            $output .= "<th align=\"center\" style=\"width:5%;\">Visitas</th>";
            $output .= "<th>T&iacute;tulo</th>";
            $output .= "</tr></thead>";
            foreach ($items as $article) {
                $output .= "<tr>";
                $output .= "<td align=\"center\">".$article["views"]."</th>";
                $output .= "<td><a href=\"".SITE_URL.$article["permalink"]
                    ."\" target=\"_blank\">".$article["title"]."</th>";
                $output .= "</tr>";
            }

            $output .= "</table>";
            $output .= "</div>";
        } else {
            $output .= "<p style=\"margin:5px;  color:red;\">Sin datos "
                ."obtenidos para este periodo de tiempo</p>";
        }

        $output .= "</div>";
        $output .= "<div class=\"clearer\"></div>";

        return $output;
    }

    public static function getMostComented($contentType,$category=0,$days=3)
    {
        $cm = new ContentManager();
        $mostComentedContentObjects = $cm->cache->getMostComentedContent(
            $contentType, false, $category, $days, 10, true
        );

        return $mostComentedContentObjects;
    }

    public static function comentedTable($items, $title)
    {
        $output = "<div class=\"dashboardBox\">".$title."";

        if (count($items)>0) {
            $output .= \Onm\UI\OFC::graphicComented($items);
            $output .= "<div class=\"table\">";
            $output .= "<table class=\"table table-hover table-condensed\" border=0>";
            $output .= "<thead><tr>";
            $output .=
                "<th align=\"center\" style=\"width:5%;\">Comentarios</th>";
            $output .= "<th >T&iacute;tulo</th>";
            $output .= "</tr></thead>";
            foreach ($items as $article) {
                $output .= "<tr>";
                $output .= "<td align=\"center\"".$article["num"]."</th>";
                $output .= "<td><a href=\"".SITE_URL.$article["permalink"]
                    ."\" target=\"_blank\">".$article["title"]."</th>";
                $output .= "</tr>";
            }

            $output .= "</table></div>";
        } else {
            $output .= "<p style=\"margin:5px;  color:red;\">Sin datos "
                ."obtenidos para este periodo de tiempo</p>";
        }

        $output .= "</div>";
        $output .= "<div class=\"clearer\"></div>";

        return $output;
    }

    public static function getMostVoted($contentType,$category=0,$days=3)
    {
        $cm = new ContentManager();
        $contents = $cm->cache->getMostVotedContent($contentType,
            false, $category, 0, $days, 10, true);

        $results = array();
        for ($i=0; $i<count($contents); $i++) {
            if ($contents[$i]->total_votes > 0) {
                $results[$i]['pk_content']  = $contents[$i]->id;
                $results[$i]['title']       = $contents[$i]->title;
                $results[$i]['total_votes'] = $contents[$i]->total_votes;
                $results[$i]['rate']        =
                round($contents[$i]->total_value/$contents[$i]->total_votes, 2);
                $results[$i]['permalink']   = $contents[$i]->permalink;
            }
        }

        return $results;
    }

    public static function votedTable($items, $title)
    {
        $output = "<div class=\"dashboardBox\">".$title."";

        if (count($items)>0) {
            $output .= \Onm\UI\OFC::graphicVoted($items)
                . "<div class=\"table\">"
                . "<table class=\"table table-hover table-condensed\" border=0>"
                . "<thead><tr>"
                . "<th align=\"center\" style=\"width:5%;\">Votos</th>"
                . "<th align=\"center\" style=\"width:5%;\">"
                ."Puntuaci&oacute;n</th>"
                . "<th>T&iacute;tulo</th>"
                . "</tr></thead>";

            foreach ($items as $article) {
                $output .= "<tr>"
                . "<td align=\"center\">".$article["total_votes"]."</th>"
                . "<td align=\"center\">".$article["rate"]."</th>"
                . "<td><a href=\"".SITE_URL.$article["permalink"]
                . "\" target=\"_blank\">".$article["title"]."</th>"
                . "</tr>";
            }

            $output .= "</table></div>";
        } else {
            $output .= "<p style=\"margin:5px;  color:red;\">Sin datos "
                ."obtenidos para este periodo de tiempo</p>";
        }

        $output .= "</div>";
        $output .= "<div class=\"clearer\"></div>";

        return $output;
    }

}
