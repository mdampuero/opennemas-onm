<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Common\Data\Serialize\Serializer\PhpSerializer;
use Common\ORM\Core\Connection;
use Common\ORM\Entity\Instance;
use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;

class InstanceHelper
{
    /**
     * The Http client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * The Filesystem component.
     *
     * @var Filesystem
     */
    protected $fs;

    /**
     * The path to public/media folder.
     *
     * @var string
     */
    protected $mediaPath;

    /**
     * The piwik parameters.
     *
     * @var array
     */
    protected $piwik;

    /**
     * Initializes the InstanceHelper.
     *
     * @param Connection $conn      The database connection.
     * @param string     $mediaPath The path to public/media folder.
     * @param array      $piwik     The list of piwik parameters.
     */
    public function __construct(Connection $conn, string $mediaPath, array $piwik)
    {
        $this->client    = new Client();
        $this->conn      = $conn;
        $this->fs        = new Filesystem();
        $this->mediaPath = $mediaPath;
        $this->piwik     = $piwik;
    }

    /**
     * Returns the number of contents grouped by content type.
     *
     * @param Instance $instance The instance.
     *
     * @return array The number of contents grouped by content type.
     */
    public function countContents(Instance $instance) : array
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select content_type_name, count(*) as total from contents'
                . ' group by content_type_name';

            $contents = $this->conn->fetchAll($sql);
            $stats    = [];

            foreach ($contents as $content) {
                $stats[$content['content_type_name']] = $content['total'];
            }

            return $stats;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Returns the number of enabled backend users.
     *
     * @param Instance $instance The instance.
     *
     * @return array The number of enabled backend users.
     */
    public function countUsers(Instance $instance) : int
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select count(*) as total from users'
               . ' where activated = 1 and type in (0, 2)';

            $users = $this->conn->fetchAssoc($sql);

            return $users['total'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Fetches the date of creation of the instance
     *
     * @param Instance $i The instance to get stats from
     */
    public function getCreated($instance) : ?\DateTime
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select value from settings where name = "site_created"';

            $created = $this->conn->fetchAssoc($sql);
            $created = new \DateTime(PhpSerializer::unserialize($created['value']));

            $created->setTimeZone(new \DateTimeZone('UTC'));

            return $created;
        } catch (\Exception $e) {
            return $instance->created;
        }
    }

    /**
     * Returns the date of the last activity executed in the instance.
     *
     * @param Instance $instance The instance.
     *
     * @return \DateTime The date of the last activity.
     */
    public function getLastActivity(Instance $instance) : ?\DateTime
    {
        return max(
            $this->getLastAuthentication($instance),
            $this->getLastCreated($instance)
        );
    }

    /**
     * Returns the size of media folder for one instance o
     *
     * @param Instance $instance The instance.
     *
     * @return int The size of the instance media folder.
     *
     * @codeCoverageIgnore
     */
    public function getMediaSize(Instance $instance) : int
    {
        $path = sprintf(
            '%s/%s',
            $this->mediaPath,
            empty($instance) ? '*' : $instance->internal_name
        );

        if (!$this->fs->exists($path)) {
            throw new \InvalidArgumentException('No such file or directory: ' . $path);
        }

        exec("du -s $path", $output, $status);

        if (!empty($status)) {
            throw new \Exception();
        }

        return (int) preg_replace('/\s+.*/', '', $output[0]);
    }

    /**
     * Returns the number of page views for the instance.
     *
     * @param Instance $instance The instance.
     *
     * @return int The number of page views.
     *
     * @throws \Exception When the request fails or there is no valid result.
     *
     * @throws \InvalidArgumentException When there is no valid piwik
     *         configuration.
     */
    public function getPageViews(Instance $instance) : int
    {
        $piwik = $this->getPiwikSettings($instance);

        if (empty($piwik)
            || !is_array($piwik)
            || !array_key_exists('page_id', $piwik)
            || empty($piwik['page_id'])
        ) {
            throw new \InvalidArgumentException('No valid piwik configuration');
        }

        $from = new \DateTime('now');

        if ($from->format('d') <= '27') {
            $from->modify('-1 month');
        }

        $from->setDate($from->format('Y'), $from->format('m'), 27);

        $from = $from->format('Y-m-d');
        $to   = date('Y-m-d');

        $url = sprintf(
            '%s?module=API&method=API.get'
            . '&apiModule=VisitsSummary&apiAction=get'
            . '&idSite=%s'
            . '&period=range&date=%s,%s'
            . '&format=json'
            . '&showColumns=nb_pageviews'
            . '&token_auth=%s',
            $this->piwik['url'],
            $piwik['page_id'],
            $from,
            $to,
            $this->piwik['token']
        );

        $response = $this->client->get($url);
        $body     = json_decode($response->getBody(), true);

        if (!array_key_exists('value', $body)) {
            throw new \Exception(
                array_key_exists('message', $body) ? $body['message'] : ''
            );
        }

        return (int) $body['value'];
    }

    /**
     * Returns the date of the last successful authentication action.
     *
     * @param Instance $instance The current instance.
     *
     * @return \DateTime The date of the last successful authentication action.
     */
    protected function getLastAuthentication(Instance $instance) : ?\DateTime
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select value from settings where name = "last_login"';

            $auth = $this->conn->fetchAssoc($sql);
            $auth = new \DateTime(PhpSerializer::unserialize($auth['value']));

            $auth->setTimeZone(new \DateTimeZone('UTC'));

            return $auth;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the created date of the last created content.
     *
     * @param Instance $instance The current instance.
     *
     * @return \DateTime The date of the last logging in action.
     */
    protected function getLastCreated(Instance $instance) : ?\DateTime
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select created from contents order by created desc limit 1';

            $created = $this->conn->fetchAssoc($sql);
            $created = new \DateTime(PhpSerializer::unserialize($created['created']));

            $created->setTimeZone(new \DateTimeZone('UTC'));

            return $created;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the piwik setting for the instance.
     *
     * @param Instance $instance The instance.
     *
     * @return array The piwik settings.
     */
    protected function getPiwikSettings(Instance $instance) : ?array
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select value from settings where name = "piwik"';

            $piwik = $this->conn->fetchAssoc($sql);

            return PhpSerializer::unserialize($piwik['value']);
        } catch (\Exception $e) {
            return null;
        }
    }
}
