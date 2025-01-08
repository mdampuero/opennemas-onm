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

use Common\Model\Entity\Instance;
use DateTime;
use GuzzleHttp\Client;
use Opennemas\Data\Serialize\Serializer\PhpSerializer;
use Opennemas\Orm\Core\Connection;
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
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

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
     * Initializes the InstanceHelper.
     *
     * @param Connection $conn      The database connection.
     * @param string     $mediaPath The path to public/media folder.
     */
    public function __construct(Connection $conn, string $mediaPath)
    {
        $this->client    = new Client();
        $this->conn      = $conn;
        $this->fs        = new Filesystem();
        $this->mediaPath = $mediaPath;
    }

    /**
     * Returns the number of comments.
     *
     * @param Instance $instance The instance.
     *
     * @return array The number of comments.
     */
    public function countComments(Instance $instance) : int
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select count(*) as total from comments';

            $comments = $this->conn->fetchAssoc($sql);

            return $comments['total'];
        } catch (\Exception $e) {
            return 0;
        }
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
     * Returns the number mailing sends
     *
     * @param Instance $instance The instance.
     *
     * @return array The number of emails.
     */
    public function countEmails(Instance $instance) : int
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql     = 'select value from settings where name like \'last_invoice\'';
            $setting = $this->conn->fetchAssoc($sql);

            $lastInvoice = new \DateTime(substr($setting['value'], 6, 19));
            $today       = new \DateTime();

            $sql = sprintf(
                'select sum(sent_items) as total FROM newsletters'
                . ' where updated >= "%s" and updated <= "%s" and sent_items > 0',
                $lastInvoice->format('Y-m-d H:i:s'),
                $today->format('Y-m-d H:i:s')
            );

            $emails = $this->conn->fetchAssoc($sql);

            return $emails['total'] ?? 0;
        } catch (\Exception $e) {
            return 0;
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
     * Returns the number of enabled backend users.
     *
     * @param Instance $instance The instance.
     *
     * @return array The number of enabled backend users.
     */
    public function countTags(Instance $instance) : int
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select count(*) as total from tags';

            $tags = $this->conn->fetchAssoc($sql);

            return $tags['total'];
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
            $auth = PhpSerializer::unserialize($auth['value']);

            if (empty($auth)) {
                return null;
            }

            $auth = new \DateTime($auth);

            $auth->setTimeZone(new \DateTimeZone('UTC'));

            return $auth;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the amount of current Web Push subscirbers
     *
     * @param Instance $instance The current instance.
     *
     * @return int The amount of current Web Push subscirbers
     */
    public function getWebPushSubscribers(Instance $instance)
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $sql = 'select value from settings where name = "webpush_active_subscribers"';

            $auth = $this->conn->fetchAssoc($sql);
            $auth = PhpSerializer::unserialize($auth['value']);

            if (empty($auth)) {
                return null;
            }

            return $auth[0];
        } catch (\Exception $e) {
            return null;
        }
    }

        /**
     * Returns the amount of current Web Push subscirbers
     *
     * @param Instance $instance The current instance.
     *
     * @return int The amount of current Web Push subscirbers
     */
    public function getSpentAI(Instance $instance)
    {
        try {
            $this->conn->selectDatabase($instance->getDatabaseName());

            $date       = new DateTime();
            $currentDay = (int) $date->format('d');

            $startDate = new DateTime();

            if ($currentDay < 27) {
                $startDate->modify('first day of last month')
                    ->setDate($date->format('Y'), $date->format('m') - 1, 27)
                    ->setTime(0, 0, 0);
            } else {
                $startDate->setDate($date->format('Y'), $date->format('m'), 27)
                    ->setTime(0, 0, 0);
            }

            $startDateStr = $startDate->format('Y-m-d H:i:s');

            $sql = "SELECT params, tokens FROM ai_actions WHERE date >= '$startDateStr'";

            $results = $this->conn->fetchAll($sql);

            if (empty($results)) {
                return null;
            }

            return $results;
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
}
