<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 * Handles all the vote system.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 *
 **/
class Vote
{

    public $pk_vote = null;
    public $value_pos = null;
    public $value_neg = null;
    public $karma = null;
    public $ips_count_vote = null;

    /**
     * _messages to use in links and image
     */
    private $_messages = array('', 'A Favor', 'En Contra');

    /**
     * Constructor PHP5
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Creates an vote to a element given the element if.
     *
     * @param string $votePK the pk of the vote.
     *
     * @return boolean true if all went well
     **/
    public function create($votePK, $vote, $ip)
    {

        $sql = "INSERT INTO votes (`pk_vote`,`value_pos`,
                                   `value_neg`, `karma`, `ips_count_vote`)
                VALUES (?,?,?,?,?)";

        if ($vote == '2') { // En contra

            $negValue = 1;
            $karma = 100 - 1;
            $posValue = 0;
        } else { // A favor

            $posValue = 1;
            $karma = 100 + 1;
            $negValue = 0;
        }
        $ipsCountVote[] = array('ip' => $ip, 'count' => 1);
        $values = array(
            $votePK, $posValue, $negValue, $karma, serialize($ipsCountVote)
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return (false);
        }

        return (true);
    }

    public function read($votePK)
    {
        $sql = 'SELECT value_pos, value_neg, ips_count_vote
                FROM votes WHERE pk_vote =' . $votePK;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->EOF) {

            //Si no existe un votacion pinta 0
            $sql = "INSERT INTO votes (`pk_vote`,`value_pos`,
                                       `value_neg`, `karma`, `ips_count_vote`)
                    VALUES (?,?,?,?,?)";
            $values = array($votePK, 0, 0, 100, serialize(array()));

            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if (!$rs) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
                $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

                return (false);
            }
            $this->pk_vote = $votePK;
            $this->value_pos = 0;
            $this->value_neg = 0;
            $this->karma = 100;
            $this->ips_count_vote = array();
        } else {
            $this->pk_vote = $votePK;
            $this->value_pos = $rs->fields['value_pos'];
            $this->value_neg = $rs->fields['value_neg'];

            //       $this->karma = $rs->fields['karma'];
            $this->ips_count_vote = unserialize($rs->fields['ips_count_vote']);
        }

        return (true);
    }

    /**
     * Updates the vote information.
     *
     * @param string $vote
     * @param string $ip
     *
     * @return boolean true if all went well
     **/
    public function update($vote, $ip)
    {
        $this->ips_count_vote = $this->add_count($this->ips_count_vote, $ip);

        if (!$this->ips_count_vote) return false;

        if ($vote == '2') {
            $value = ++$this->value_neg;
            $sql = "UPDATE votes SET  `value_neg`=?,  `ips_count_vote`=?
            WHERE pk_vote=" . $this->pk_vote;
        } else {
            $value = ++$this->value_pos;
            $sql = "UPDATE votes SET  `value_pos`=?,  `ips_count_vote`=?
            WHERE pk_vote=" . $this->pk_vote;
        }
        $values = array($value, serialize($this->ips_count_vote));

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return (false);
        }

        //creamos la cookie
       Application::setCookieSecure(
            "vote" . $this->pk_vote,
            'true',
            time() + 60 * 60 * 24 * 30
        );

        return true;
    }

    /**
     * Renders the html for the voting result.
     **/
    public function render($page, $type, $ajax = 0)
    {


        if (isset($_COOKIE["vote" . $this->pk_vote])) $type = "result";
        $outputHTML = "";
        $results = "";

        if ($type == "vote") {

            // Render links
            for ($i = 1;$i <= 2;$i++) {
                $results.= $this->renderLink($i, $this->pk_vote, $value);
            }
            $outputHTML.= "  <div class=\"CVotos\">";
            $outputHTML.= $results;
            $outputHTML.= "  </div>";
        } elseif ($type == "result") {
            for ($i = 1;$i <= 2;$i++) {
                $results.= $this->renderImg($i);
            }
            $outputHTML.= "  <div class=\"CVotos\">";
            $outputHTML.= $results;
            $outputHTML.= "  </div>";
            $outputHTML.= "  <div class=\"separadorVotos\"></div>";
            $outputHTML.= "  <div class=\"CVotos\">";
            $outputHTML.= "  ¡Gracias por votar!";
            $outputHTML.= "  </div>";
        }

        if (!$ajax) {
            $outputHTML = "<div class=\"CComent_Votos_nota\" id=\"vota"
                        . $this->pk_vote . "\">" . $outputHTML;
            $outputHTML.= "</div>";
        }

        return $outputHTML;
    }

    //ADD adressIP to votes array. Only permit 50 from  IP.
    public function add_count($countIPs, $ip)
    {

        $ips = array();
        foreach ($countIPs as $ip_array) {
            $ips[] = $ip_array['ip'];
        }

        //Se busca si existe algún voto desde la ip
        $countKIP = array_search($ip, $ips);

        if ($countKIP === FALSE) {

            //No se ha votado desde esa ip
            $countIPs[] = array('ip' => $ip, 'count' => 1);
        } else {

            if ($countIPs[$countKIP]['count'] == 50) return FALSE;
            $countIPs[$countKIP]['count']++;
        }

        return $countIPs;
    }

    /**
     * Gets the karm for a vote given its pk.
     *
     * @param string $votePk the pk of the vote.
     *
     * @return int the karma number for the vote.
     **/
    public function get_karma($votePk)
    {

        $sql = 'SELECT karma FROM votes WHERE pk_vote=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($votePk));

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return;
        }

        return $rs->fields['karma'];
    }

    /**
     * Returns the HTML for a vote image
     *
     * @return string the HTML for the vote image
     *
     **/
    private function renderImg($i)
    {

        $imgPath = TEMPLATE_USER_URL . "images/utilities/";
        $imageTpl = '<img src="%s%s.png" style="vertical-align:middle;" alt="%s" title="%s" /> ( %d ) ';

        return sprintf($imageTpl, $imgPath, ($i % 2 == 0) ? "vote-up" : "vote-down", $this->_messages[$i], $this->_messages[$i], ($i % 2 == 1) ? $this->value_pos : $this->value_neg);
    }

    /**
     * Returns the HTML for a vote link
     *
     * @example
     *  <a href="javascript:vote_comment(IP,1,PK_VOTE)" title="A favor">
     *    <img id="$this->pk_vote_1"
     *       src="TEMPLATE_USER_PATH."images/noticias/vote_pos.png "
     *       alt="A favor" />
     *  </a>
     *
     * @return string the HTML for the vote link
     *
     **/
    private function renderLink($i, $votePK, $value)
    {
        $imgPath = TEMPLATE_USER_URL . "images/utilities/";
        $linkTpl = <<< LINKTPLDOC
            <a href="#votar" onclick="javascript:vote_comment('%s', '%s', '%s'); return false;" title="%s">
                <img id="%s_%s" style="vertical-align:middle;"
                     src="{$imgPath}%s.png"
                     alt="%s" /> </a>   ( %d )
LINKTPLDOC;

        return sprintf(
            $linkTpl,
            $_SERVER['REMOTE_ADDR'],
            $i,
            $votePK,
            $this->_messages[$i],
            // $votePK, $i,
            $i,
            $votePK,
            ($i % 2 == 0) ? "vote-up" : "vote-down",
            $this->_messages[$i], ($i % 2 == 1) ? $this->value_pos : $this->value_neg
        );
    }
}
