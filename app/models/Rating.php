<?php

/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
/**
 * Handles all the CRUD operations for Ratings.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 *
 */

class Rating
{

    var $pk_rating = null;
    var $total_votes = null;
    var $total_value = null;
    var $ips_count_rating = null;
    var $num_of_stars = 5;

    /**
     * Messages to use in links and image
     */
    private $_messages = array(
        '',
        'Sin interés',
        'Poco interesante',
        'De interés',
        'Muy interesante',
        'Imprescindible'
    );

    /**
     * Constructor PHP5
     */
    public function __construct($id = null)
    {
        // Si existe idcontenido, entonces cargamos los datos correspondientes

        if (!is_null($id)) {
            $this->read($id);
        }
    }

    public function create($pk_rating)
    {

        $sql = "INSERT INTO ratings (`pk_rating`,`total_votes`,
                                     `total_value`, `ips_count_rating`)
                VALUES (?,?,?,?)";
        $values = array($pk_rating, 0, 0, serialize(array()));

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return (false);
        }

        return (true);
    }

    public function read($pk_rating)
    {

        $sql = 'SELECT total_votes, total_value, ips_count_rating
                FROM ratings WHERE pk_rating =' . $pk_rating;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->EOF) {

            //Si no existe un valoración para dicho contenido
            //comprobamos que el contenido exista y
            //despues creamos la valoración

            //$this->create($pk_rating);

            $this->pk_rating = $pk_rating;
            $this->total_value = 0;
            $this->total_votes = 0;
            $this->ips_count_rating = array();

            //Lo creamos en la bd
            $sql = "INSERT INTO ratings (`pk_rating`,`total_votes`,
                                        `total_value`, `ips_count_rating`)
                        VALUES (?,?,?,?)";
            $values = array(
                $this->pk_rating, $this->total_votes,
                $this->total_value, serialize($this->ips_count_rating)
            );
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if (!$rs) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
                $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

                return (false);
            }

            return;
        }

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return;
        }
        $this->pk_rating = $pk_rating;
        $this->total_votes = $rs->fields['total_votes'];
        $this->total_value = $rs->fields['total_value'];
        $this->ips_count_rating = unserialize($rs->fields['ips_count_rating']);
    }

    public function get_value($pk_rating)
    {

        $sql = 'SELECT total_votes, total_value
                FROM ratings WHERE pk_rating =' . $pk_rating;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return;
        }
        $value = 0;

        if ($rs->fields['total_votes'] != 0) {
            $valor = $rs->fields['total_value'] / $rs->fields['total_votes'];
            $value = round($valor * 100) / 100;
        }

        return $value;
    }

    public function update($vote_value, $ip)
    {

        $this->ips_count_rating = $this->add_count(
            $this->ips_count_rating, $ip
        );

        if (!$this->ips_count_rating) return (false);
        $this->total_votes++;
        $this->total_value = $this->total_value + $vote_value;
        $sql = "UPDATE ratings "
               ."SET  `total_votes`=?, `total_value`=?, `ips_count_rating`=?"
               ."WHERE pk_rating=?";
        $values = array(
            $this->total_votes,
            $this->total_value,
            serialize($this->ips_count_rating),
            $this->pk_rating,
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return (false);
        }

        //creamos la cookie
        Application::setCookieSecure(
            "vote" . $this->pk_rating,
            'true',
            time() + 60 * 60 * 24 * 30
        );

        return (true);
    }

    public function add_count($ips_count, $ip)
    {

        $ips = array();
        foreach ($ips_count as $ip_array) {
            $ips[] = $ip_array['ip'];
        }

        //Se busca si existe algún voto desde la ip
        $countKIP = array_search($ip, $ips);

        if ($countKIP === FALSE) {

            //No se ha votado desde esa ip
            $ips_count[] = array('ip' => $ip, 'count' => 1);
        } else {

            if ($ips_count[$countKIP]['count'] == 50) return FALSE;
            $ips_count[$countKIP]['count']++;
        }

        return $ips_count;
    }

    private function renderLink($i, $page, $pk_rating, $value)
    {

        $active = ($value >= $i) ? 'active' : '';
        $output = "<li>
                <a href=\"#votar\" onclick=\"javascript:"
                    ."rating('{$_SERVER['REMOTE_ADDR']}', "
                    ."{$i}, '{$page}', '{$pk_rating}'); ".
                    "return false;\" title=\"{$this->_messages[$i]}\">
                    <div class='vote-element {$active} {$pk_rating}_{$i}'
                        onmouseover=\"change_rating({$i}, '{$pk_rating}')\"
                        onmouseout=\"change_rating({$value}, '{$pk_rating}')\">
                    </div>
                </a>
            </li>";

        return $output;
    }

    private function renderImg($i, $value, $page = 'article')
    {

        $active = ($value >= $i) ? "active" : '';

        return $imageTpl =
            "<li><div class='vote-element {$active}'>&nbsp;</div></li>";
    }

    /**
     * Get an integer and returns an string with the humanized num of votes
     *
     * @param integer $total_votes num of votes
     * @return  string description
     * @author  Fran Dieguez <fran@openhost.es>
     * @since   Mon Sep 13 2010 18:12:58 GMT+0200 (CEST)
     */
    private function humanizeNumVotes($total_votes)
    {

        return $total_votes . (($total_votes > 1) ? " votos" : " voto");
    }

    /**
     * Prints the list of img elements representing the actual votes
     *
     * @param dobule $actualVotes average of votes
     * @param string $pageType    the kind of page this'll be rendered in
     * @return  string elements imgs representing the actual votes
     * @author  Fran Dieguez <fran@openhost.es>
     * @since   Mon Sep 13 2010 18:12:58 GMT+0200 (CEST)
     */
    private function getVotesOnImages($actualVotes, $pageType)
    {

        $votes_on_images = '';
        for ($i = 1;$i <= $this->num_of_stars;$i++) {
            $votes_on_images.= $this->renderImg($i, $actualVotes, $pageType);
        }

        return $votes_on_images;
    }

    /**
     * Prints the list of elements links representing the actual votes
     *
     * @param dobule $actualVotes average of votes
     * @param string $pageType    the kind of page this'll be rendered in
     * @return  string elements links representing the actual votes
     * @author  Fran Dieguez <fran@openhost.es>
     * @since   Mon Sep 13 2010 18:12:58 GMT+0200 (CEST)
     */
    private function getVotesOnLinks($actualVotes, $pageType)
    {

        $votes_on_links = '';
        for ($i = 1;$i <= $this->num_of_stars;$i++) {
            $votes_on_links.= $this->renderLink(
                $i, $pageType, $this->pk_rating, $actualVotes
            );
        }

        return $votes_on_links;
    }

    /**
     * Get an integer and returns an string with the humanized num of votes
     *
     * @param string $page num of votes
     * @param string $type the type of
     * @return  string description
     * @author  Fran Dieguez <fran@openhost.es>
     * @since   Mon Sep 13 2010 18:12:58 GMT+0200 (CEST)
     */
    public function render($pageType, $action, $ajax = 0)
    {

        /**
         * If the vote+id cookie exist just show the
         * results and don't allow to vote again
         **/

        if (isset($_COOKIE["vote" . $this->pk_rating])) $action = "result";
        /**
         * Calculate the total votes to render
         */
        ($this->total_votes == 0
            ? $actualVotes = 0
            : $actualVotes =
                (int)floor($this->total_value / $this->total_votes));
        $htmlOut = "";

        switch ($pageType) {
            case "home":
            case "article":
            case "video":
                $htmlOut.= "<ul class=\"voting\">";

                // if the user can vote render the links to vote

                if ($action == "vote") {

                    // Render links
                    $htmlOut.= $this->getVotesOnLinks(
                        $actualVotes, $pageType
                    );

                    //if the user can't vote render the static images

                } elseif ($action === "result") {

                    // Render images
                    $htmlOut.= $this->getVotesOnImages(
                        $actualVotes, $pageType
                    );
                }
                $htmlOut.= "</ul> ";

                // append the counter of total votes
                //$htmlOut .= $this->humanizeNumVotes($this->total_votes);

                // if this request is not an AJAX request wrap it.


                if (!$ajax) {
                    $htmlOut =
                        "<span class=\"vota" . $this->pk_rating . "\">"
                        . $htmlOut
                        . "</span>";
                }
                break;

            default:
                $htmlOut = _(
                    'This content type has not '.
                    'support for the voting system'
                );
                break;
        }

        return $htmlOut;
    }
}
