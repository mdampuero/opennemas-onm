<?php
/**
 * Defines the Rating class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 **/

/**
 * Handles all the CRUD operations for Ratings.
 *
 * @package    Model
 */
class Rating
{
    /**
     * Id of the rating
     *
     * @var int
     **/
    public $pk_rating = null;

    /**
     * Number of total votes
     *
     * @var int
     **/
    public $total_votes = null;

    /**
     * Current value average
     *
     * @var int
     **/
    public $total_value = null;

    /**
     * Serialized array of ips
     *
     * @var int
     **/
    public $ips_count_rating = null;

    /**
     * Number of stars to render in the HTML
     *
     * @var int
     **/
    public $num_of_stars = 5;

    /**
     * Loads ratings for a given content id
     *
     * @param int $id the content id
     *
     * @return void
     **/
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Creates an empty rating for a given content id
     *
     * @param int $contentId the content id to create the new rating for
     *
     * @return boolean true if the rating was created
     **/
    public function create($contentId)
    {
        $sql = "INSERT INTO ratings
                       (`pk_rating`,`total_votes`, `total_value`, `ips_count_rating`)
                VALUES (?,?,?,?)";
        $values = array($contentId, 0, 0, serialize(array()));

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Reads all the rating information for a given content id
     *
     * @param int $contentId the content id to load ratings from
     *
     * @return Rating|null the object with all the properties loaded
     **/
    public function read($contentId)
    {
        $sql = 'SELECT total_votes, total_value, ips_count_rating
                FROM ratings WHERE pk_rating =?';
        $values = array($contentId);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        // If doesn't exists a previous rating create an empty one.
        if ($rs->EOF) {
            $this->pk_rating = $contentId;
            $this->total_value = 0;
            $this->total_votes = 0;
            $this->ips_count_rating = array();

            $this->create($contentId);

            return $this;
        }

        if (!$rs) {
            return false;
        }
        $this->pk_rating = $contentId;
        $this->total_votes = $rs->fields['total_votes'];
        $this->total_value = $rs->fields['total_value'];
        $this->ips_count_rating = unserialize($rs->fields['ips_count_rating']);

        return $this;
    }

    /**
     * Returns the current average rating for a given content id
     *
     * @param int $contentId the content id
     *
     * @return int the average rating for the content
     **/
    public function getValue($contentId)
    {
        $sql = 'SELECT total_votes, total_value
                FROM ratings WHERE pk_rating =?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($contentId));

        if (!$rs) {
            return;
        }
        $value = 0;

        if ($rs->fields['total_votes'] != 0) {
            $value = $rs->fields['total_value'] / $rs->fields['total_votes'];
            $value = round($value * 100) / 100;
        }

        return $value;
    }

    /**
     * Adds a new vote to the current content
     *
     * @param int $vote_value the vote value to add
     * @param string $ip the IP that performed the vote
     *
     * @return boolean true if the vote was added
     **/
    public function update($vote_value, $ip)
    {
        $this->ips_count_rating = $this->add_count(
            $this->ips_count_rating,
            $ip
        );

        if (!$this->ips_count_rating) {
            return false;
        }
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
            return false;
        }

        return true;
    }

    /**
     * Adds a new IP to the list of ratings for a content
     *
     * @param array $ipsCount the actual list of IPs that have performed the rating before
     * @param string $ip the new IP to add
     *
     * @return array the new array of IPs with the new one
     **/
    public function add_count($ipsCount, $ip)
    {
        $ips = array();
        foreach ($ipsCount as $ipArray) {
            $ips[] = $ipArray['ip'];
        }

        //Se busca si existe algún voto desde la ip
        $countKIP = array_search($ip, $ips);

        if ($countKIP === false) {
            $ipsCount[] = array('ip' => $ip, 'count' => 1);
        } else {

            if ($ipsCount[$countKIP]['count'] == 50) {
                return false;
            }
            $ipsCount[$countKIP]['count']++;
        }

        return $ipsCount;
    }

    /**
     * Renders the link part for the rating HTML
     *
     * @param int $currentPos the current position of the link in the list
     * @param int $pk_rating the content id to rate
     * @param int $value  the value of the vote
     *
     * @return string the HTML for the rating link
     **/
    private function renderLink($currentPos, $pk_rating, $value)
    {
        $active = ($value >= $currentPos) ? 'active' : '';
        $output =
            "<li>
                <a href=\"#votar-{$currentPos}\" data-vote=\"{$currentPos}\">
                    <div class='vote-element {$active} {$pk_rating}_{$currentPos}'></div>
                </a>
            </li>";

        return $output;
    }

        /**
     * Renders the link part for the rating HTML
     *
     * @param int $currentPos the current position of the link in the list
     * @param int $value  the value of the vote
     *
     * @return string the HTML for the rating link
     **/
    private function renderImg($currentPos, $value)
    {
        $active = ($value >= $currentPos) ? "active" : '';

        return $imageTpl = "<li><div class='vote-element {$active}'>&nbsp;</div></li>";
    }

    /**
     * Prints the list of img elements representing the actual votes
     *
     * @param  dobule $actualVotes average of votes
     *
     * @return string elements imgs representing the actual votes
     */
    private function getVotesOnImages($actualVotes)
    {
        $votes_on_images = '';
        for ($i = 1; $i <= $this->num_of_stars; $i++) {
            $votes_on_images.= $this->renderImg($i, $actualVotes);
        }

        return $votes_on_images;
    }

    /**
     * Prints the list of elements links representing the actual votes
     *
     * @param  dobule $actualVotes average of votes
     *
     * @return string elements links representing the actual votes
     */
    private function getVotesOnLinks($actualVotes)
    {
        $votes_on_links = '';
        for ($i = 1; $i <= $this->num_of_stars; $i++) {
            $votes_on_links.= $this->renderLink(
                $i,
                $this->pk_rating,
                $actualVotes
            );
        }

        return $votes_on_links;
    }

    /**
     * Get an integer and returns an string with the humanized num of votes
     *
     * @param  string $pageType the type of page on which the rating will be rendered
     * @param  string $action   whether if this method should return a list of images
     *                          or a list of links
     * @param  string $ajax     whether if the HTML is called from AJAX
     *
     * @return string description
     */
    public function render($pageType, $action, $ajax = 0)
    {
        // Calculate the total votes to render
        if ($this->total_votes == 0) {
            $actualVotes = 0;
        } else {
            $actualVotes = (int) floor($this->total_value / $this->total_votes);
        }

        $htmlOut = "<ul class=\"voting\">";
        // if the user can vote render the links to vote
        if ($action == "vote") {
            // Render the static images, so the user can't read
            $htmlOut.= $this->getVotesOnLinks($actualVotes);
        } elseif ($action === "result") {
            // Render images
            $htmlOut.= $this->getVotesOnImages($actualVotes);
        }
        $htmlOut.= "</ul> ";

        if (!$ajax) {
            $htmlOut =
                "<span class=\"vota" . $this->pk_rating . "\">"
                . $htmlOut
                . "</span>";
        }

        return $htmlOut;
    }
}
