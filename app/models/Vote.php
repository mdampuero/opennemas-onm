<?php
/**
 * Defines the Vote class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 **/
/**
 * Handles all the vote system.
 *
 * @package    Model
 **/
class Vote
{
    /**
     * The content id that this vote references
     *
     * @var int
     **/
    public $pk_vote        = null;

    /**
     * Summary of positive votes
     *
     * @var int
     **/
    public $value_pos      = null;

    /**
     * Summary of negative votes
     *
     * @var int
     **/
    public $value_neg      = null;

    /**
     * Final karma for the content
     *
     * @var
     **/
    public $karma          = null;

    /**
     * Serialized array of ips that voted
     *
     * @var
     **/
    public $ips_count_vote = null;

    /**
     * messages to use in links and image
     *
     * @var  array
     */
    private $messages = array('', 'A Favor', 'En Contra');

    /**
     * Initializes the vote given an content id
     *
     * @param int $id the content id to fetch the votes
     *
     * @return void
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
     * @param string $votePK the pk of the vote
     * @param int    $vote   the vote value
     * @param string $ip     the IP that performs the vote
     *
     * @return boolean true if all went well
     **/
    public function create($votePK, $vote, $ip)
    {
        $sql = "INSERT INTO votes
                    (`pk_vote`,`value_pos`, `value_neg`, `karma`, `ips_count_vote`)
                VALUES (?,?,?,?,?)";

        // En contra
        if ($vote == '2') {
            $negValue = 1;
            $karma    = 100 - 1;
            $posValue = 0;
        } else {
            // A favor
            $posValue = 1;
            $karma    = 100 + 1;
            $negValue = 0;
        }
        $ipsCountVote[] = array('ip' => $ip, 'count' => 1);
        $values = array(
            $votePK, $posValue, $negValue, $karma, serialize($ipsCountVote)
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Loads all the votes for a given content id
     *
     * @param int $votePK the content id
     *
     * @return Vote the object instance
     **/
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
                return false;
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

        return true;
    }

    /**
     * Updates the vote information.
     *
     * @param string $vote the vote value
     * @param string $ip   the ip that performs the vote
     *
     * @return boolean true if all went well
     **/
    public function update($vote, $ip)
    {
        $this->ips_count_vote = $this->addCount($this->ips_count_vote, $ip);

        if (!$this->ips_count_vote) {
            return false;
        }

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
            return false;
        }

        return true;
    }

    /**
     * Renders the html for the voting result
     *
     * @param string $page the page where the vote takes place
     * @param string $type the type of the vote
     * @param string $ajax whether the votes is from an ajax request
     *
     * @return string the HTML generated
     **/
    public function render($page, $type, $ajax = 0)
    {
        if (isset($_COOKIE["vote" . $this->pk_vote])) {
            $type = "result";
        }
        $outputHTML = "";
        $results = "";

        if ($type == "vote") {

            // Render links
            for ($i = 1; $i <= 2; $i++) {
                $results.= $this->renderLink($i, $this->pk_vote);
            }
            $outputHTML.= "  <div class=\"CVotos\">";
            $outputHTML.= $results;
            $outputHTML.= "  </div>";
        } elseif ($type == "result") {
            for ($i = 1; $i <= 2; $i++) {
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

    /**
     * Adds a new vote for an IP. Only 50 votes/IP allowed
     *
     * @param array  $countIPs list of ips that voted
     * @param string $ip       the ip that performs the vote
     *
     * @return array the new list of ips and vote counts
     **/
    public function addCount($countIPs, $ip)
    {
        $ips = array();
        foreach ($countIPs as $ip_array) {
            $ips[] = $ip_array['ip'];
        }

        //Se busca si existe algún voto desde la ip
        $countKIP = array_search($ip, $ips);

        if ($countKIP === false) {
            //No se ha votado desde esa ip
            $countIPs[] = array('ip' => $ip, 'count' => 1);
        } else {
            if ($countIPs[$countKIP]['count'] == 50) {
                return false;
            }
            $countIPs[$countKIP]['count']++;
        }

        return $countIPs;
    }

    /**
     * Returns the HTML for a vote image
     *
     * @param int $i the voting position
     *
     * @return string the HTML for the vote image
     *
     **/
    private function renderImg($i)
    {
        $imgPath = TEMPLATE_USER_URL . "images/utilities/";
        $imageTpl = '<img src="%s%s.png" style="vertical-align:middle;" alt="%s" title="%s" /> ( %d ) ';

        return sprintf(
            $imageTpl,
            $imgPath,
            ($i % 2 == 0) ?  "vote-up" : "vote-down",
            $this->messages[$i],
            $this->messages[$i],
            ($i % 2 == 1) ? $this->value_pos : $this->value_neg
        );
    }

    /**
     * Returns the HTML for a vote link
     *
     * @param int $i the voting position
     * @param int $votePK the content id to vote
     * @param int $value the voting value
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
    private function renderLink($i, $votePK)
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
            $this->messages[$i],
            // $votePK, $i,
            $i,
            $votePK,
            ($i % 2 == 0) ? "vote-up" : "vote-down",
            $this->messages[$i],
            ($i % 2 == 1) ? $this->value_pos : $this->value_neg
        );
    }
}
