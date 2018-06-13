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
 */
/**
 * Handles all the vote system.
 *
 * @package    Model
 */
class Vote
{
    /**
     * The content id that this vote references
     *
     * @var int
     */
    public $pk_vote = null;

    /**
     * Summary of positive votes
     *
     * @var int
     */
    public $value_pos = null;

    /**
     * Summary of negative votes
     *
     * @var int
     */
    public $value_neg = null;

    /**
     * Final karma for the content
     *
     * @var
     */
    public $karma = null;

    /**
     * Serialized array of ips that voted
     *
     * @var
     */
    public $ips_count_vote = null;

    /**
     * messages to use in links and image
     *
     * @var  array
     */
    private $messages = [];

    /**
     * Initializes the vote given an content id
     *
     * @param int $id the content id to fetch the votes
     *
     * @return void
     */
    public function __construct($id = null)
    {
        $this->messages = [
            '',
            _('A Favor'),
            _('En Contra'),
        ];

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
     */
    public function create($votePK, $vote, $ip)
    {
        // TODO: clearly this a remanent from old opennemas times, for now I'll
        // keep it here but no uses are in place.
        $karma = 100;

        try {
            getService('dbal_connection')->insert("votes", [
                'pk_vote'       => $votePK,
                'value_pos'     => ($vote == '2') ? 0 : 1,
                'value_neg'     => ($vote !== '2') ? 0 : 1,
                'karma'         => ($vote !== '2') ? $karma - 1 : $karma + 1,
                'ips_count_vote' => serialize([$ip])
            ]);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Loads all the votes for a given content id
     *
     * @param int $votePK the content id
     *
     * @return Vote the object instance
     */
    public function read($votePK)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT value_pos, value_neg, ips_count_vote, karma FROM votes WHERE pk_vote=?",
                [ $votePK ]
            );
        } catch (\Exception $e) {
            getService('error.log')->error('Error on Vote::read: ' . $e->getMessage());

            return false;
        }

        if ($rs) {
            $this->pk_vote        = $votePK;
            $this->value_pos      = $rs['value_pos'];
            $this->value_neg      = $rs['value_neg'];
            $this->karma          = $rs['karma'];
            $this->ips_count_vote = unserialize($rs['ips_count_vote']);

            return $this;
        }

        try {
            $rs = getService('dbal_connection')->insert("votes", [
              'pk_vote'        => $votePK,
              'value_pos'      => 0,
              'value_neg'      => 0,
              'karma'          => 100,
              'ips_count_vote' => serialize([]),
            ]);

            $this->pk_vote        = $votePK;
            $this->value_pos      = 0;
            $this->value_neg      = 0;
            $this->karma          = 100;
            $this->ips_count_vote = [];

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Updates the vote information.
     *
     * @param string $vote the vote value
     * @param string $ip   the ip that performs the vote
     *
     * @return boolean true if all went well
     */
    public function update($vote, $ip)
    {
        $this->ips_count_vote = $this->addCount($this->ips_count_vote, $ip);

        if (!$this->ips_count_vote) {
            return false;
        }

        $values = [ 'ips_count_vote' => serialize($this->ips_count_vote) ];
        if ($vote == '2') {
            $values['value_neg'] = ++$this->value_neg;
        } else {
            $values['value_pos'] = ++$this->value_pos;
        }

        try {
            getService('dbal_connection')->update(
                "votes",
                $values,
                [ 'pk_vote' => (int) $this->pk_vote ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error('Error on Vote:update: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Adds a new vote for an IP. Only 50 votes/IP allowed
     *
     * @param array  $countIPs list of ips that voted
     * @param string $ip       the ip that performs the vote
     *
     * @return array the new list of ips and vote counts
     */
    public function addCount($countIPs, $ip)
    {
        $ips = [];
        if (!is_array($countIPs)) {
            $countIPs = [];
        }

        foreach ($countIPs as $ipArray) {
            $ips[] = $ipArray['ip'];
        }

        // Se busca si existe algún voto desde la ip
        $countKIP = array_search($ip, $ips);

        if ($countKIP === false) {
            // No se ha votado desde esa ip
            $countIPs[] = [
                'ip' => $ip,
                'count' => 1
            ];
        } else {
            if ($countIPs[$countKIP]['count'] == 50) {
                return false;
            }

            $countIPs[$countKIP]['count']++;
        }

        return $countIPs;
    }
}
