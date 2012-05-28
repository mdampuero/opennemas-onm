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
                'permalink'  => Uri::generate('article', array(
                    'id'       => $content->id,
                    'date'     => date('Y-m-d', strtotime($content->created)),
                    'category' => $cm->get_categoryName_by_contentId($content->id),
                    'slug'     => StringUtils::get_title($content->title),
                )),

            );
        }

        return $contents;
    }

    public static function viewedTable($items, $title)
    {
            $output = "<div class=\"dashboardBox\">".$title."";

            if (count($items)>0) {
                $output .= \Onm\UI\OFC::graphicViewed($items);
                $output .= "<div class=\"table\">";
                $output .= "<table class=\"adminlist\" border=0><thead><tr>";
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
                $output .= "<p style=\"margin:5px;  color:red;\">Sin datos obtenidos para este periodo de tiempo</p>";
            }

            $output .= "</div>";
            $output .= "<div class=\"clearer\"></div>";

            return $output;
    }

    public static function getMostComented($contentType,$category=0,$days=3)
    {
        $cm = new ContentManager();
        $mostComentedContentObjects =
            $cm->cache->getMostComentedContent($contentType, false, $category, $days, 10, true);

        return $mostComentedContentObjects;
    }

    public static function comentedTable($items, $title)
    {
            $output = "<div class=\"dashboardBox\">".$title."";

            if (count($items)>0) {
                $output .= \Onm\UI\OFC::graphicComented($items);
                $output .= "<div class=\"table\">";
                $output .= "<table class=\"adminlist\" border=0>";
                $output .= "<thead><tr>";
                $output .= "<th align=\"center\" style=\"width:5%;\">Comentarios</th>";
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
                $output .= "<p style=\"margin:5px;  color:red;\">Sin datos obtenidos para este periodo de tiempo</p>";
            }

            $output .= "</div>";
            $output .= "<div class=\"clearer\"></div>";

            return $output;
    }

    public static function getMostVoted($contentType,$category=0,$days=3)
    {
        $cm = new ContentManager();
        $contentObjects = $cm->cache->getMostVotedContent($contentType,
            false, $category, 0, $days, 10, true);

        $mostVotedContent = array();
        for ($i=0;$i<count($contentObjects);$i++) {
            if ($contentObjects[$i]->total_votes > 0) {
                $mostVotedContent[$i]['pk_content'] = $contentObjects[$i]->id;
                $mostVotedContent[$i]['title'] = $contentObjects[$i]->title;
                $mostVotedContent[$i]['total_votes'] = $contentObjects[$i]->total_votes;
                $mostVotedContent[$i]['rate'] =
                    round($contentObjects[$i]->total_value/$contentObjects[$i]->total_votes, 2);
                $mostVotedContent[$i]['permalink'] = $contentObjects[$i]->permalink;
            }
        }

        return $mostVotedContent;
    }

    public static function votedTable($items, $title)
    {
            $output = "<div class=\"dashboardBox\">".$title."";

            if (count($items)>0) {
                $output .= \Onm\UI\OFC::graphicVoted($items);
                $output .= "<div class=\"table\">";
                $output .= "<table class=\"adminlist\" border=0>";
                $output .= "<thead><tr>";
                $output .= "<th align=\"center\" style=\"width:5%;\">Votos</th>";
                $output .= "<th align=\"center\" style=\"width:5%;\">Puntuaci&oacute;n</th>";
                $output .= "<th>T&iacute;tulo</th>";
                $output .= "</tr></thead>";

                foreach ($items as $article) {
                    $output .= "<tr>";
                    $output .= "<td align=\"center\">".$article["total_votes"]."</th>";
                    $output .= "<td align=\"center\">".$article["rate"]."</th>";
                    $output .= "<td><a href=\"".SITE_URL.$article["permalink"]
                        ."\" target=\"_blank\">".$article["title"]."</th>";
                    $output .= "</tr>";
                }

                $output .= "</table></div>";
            } else {
                $output .= "<p style=\"margin:5px;  color:red;\">Sin datos obtenidos para este periodo de tiempo</p>";
            }

            $output .= "</div>";
            $output .= "<div class=\"clearer\"></div>";

            return $output;
    }

}
