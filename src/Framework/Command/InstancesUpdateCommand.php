<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstancesUpdateCommand extends ContainerAwareCommand
{
    /**
     * If true, debug messages will be shown during importing.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * Array of database settings to use in migration process.
     *
     * @var array
     */
    protected $settings;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('instances:update')
            ->setDescription('Updates onm-instances database counters')
            ->setHelp(
                "Updates the counters in instances table in onm-instances by collecting data from different sources."
            )
            ->addOption(
                'instance-stats',
                'i',
                InputOption::VALUE_NONE,
                'If set, the command will gather content, users, sent emails info.'
            )
            ->addOption(
                'alexa',
                'a',
                InputOption::VALUE_NONE,
                'If set, the command will gathe the domain rank in alexa.'
            )
            ->addOption(
                'views',
                'p',
                InputOption::VALUE_NONE,
                'If set, the command will gather the page views from Piwik.'
            )
            ->addOption(
                'media-size',
                'm',
                InputOption::VALUE_NONE,
                'If set, the command will gather the media sixe for the instance.'
            )
            ->addOption(
                'created',
                'r',
                InputOption::VALUE_NONE,
                'If set, the command will gather the created date from instance.'
            )->addOption(
                'offset',
                'o',
                InputOption::VALUE_OPTIONAL,
                'If set, this command will only be run in 30 instances from page [offset].'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loader = $this->getContainer()->get('core.loader');
        $loader->init();

        $options = [
            'instance_stats'  => $input->getOption('instance-stats'),
            'alexa'           => $input->getOption('alexa'),
            'views'           => $input->getOption('views'),
            'media_size'      => $input->getOption('media-size'),
            'created'         => $input->getOption('created'),
        ];

        $offset       = $input->getOption('offset');
        $this->input  = $input;
        $this->output = $output;

        if (!$options['instance_stats']
            && !$options['alexa']
            && !$options['views']
            && !$options['media_size']
            && !$options['created']
        ) {
            $this->output->writeln('<error>Please provide --instance-stats --alexa, '
                . '--views, --media-size or --created</error>');
            return 1;
        }

        $amount = !is_null($offset) ? 30 : null;

        $this->getContainer()->get('cache_manager')->setNamespace('manager');
        $this->em = $this->getContainer()->get('orm.manager');
        $oql      = 'order by id asc';

        if ($amount) {
            $oql = ' limit ' . $amount;
        }

        if ($offset) {
            $oql .= ' offset ' . ($offset - 1) * 30;
        }

        $instances = $this->em->getRepository('Instance')->findBy($oql);

        if (count($instances) == 0) {
            $output->writeln('No instances');
            return 1;
        }

        foreach ($instances as $instance) {
            if ($output->isVerbose()) {
                $output->writeln('Getting info about \'' . $instance->internal_name . '\'');
            }

            try {
                $this->getInstanceInfo($instance, $options);
                $this->em->persist($instance);
            } catch (\Exception $e) {
                error_log($e->getMessage());
                $output->writeln(
                    '<error>Error while getting info about \''
                    . $instance->internal_name . '\': ' . $e->getMessage() . '</>'
                );
            }
        }

        // No errors
        return 0;
    }

    /**
     * Gets the instance information.
     *
     * @param  Instance $i       The instance.
     * @param  array    $options Whether to get the Alexa's rank.
     *
     * @return boolean
     */
    private function getInstanceInfo(&$i, $options)
    {
        if (empty($i->getDatabaseName())) {
            return false;
        }
        $conn = $this->getContainer()->get('orm.manager')->getConnection('instance');
        $conn->selectDatabase($i->getDatabaseName());

        // Update instance stats
        if ($options['instance_stats']) {
            if ($this->output->isVeryVerbose()) {
                $this->output->write("\t- Getting instance stats (emails, users, content numbers) ");
            }

            $this->getInstanceStats($i);

            if ($this->output->isVeryVerbose()) {
                $this->output->writeln("<fg=green>DONE</>");
            }
        }

        // Update created data
        if ($options['created']) {
            if ($this->output->isVeryVerbose()) {
                $this->output->write("\t- Getting instance creational date ");
            }

            $this->getCreatedDate($i);

            if ($this->output->isVeryVerbose()) {
                $this->output->writeln("<fg=green>DONE</>");
            }
        }

        // Get the page views from Piwik
        if ($options['views']) {
            if ($this->output->isVeryVerbose()) {
                $this->output->write("\t- Getting page num views ");
            }
            $sql = 'SELECT value FROM settings WHERE name=\'piwik\'';
            $rs  = $conn->fetchAll($sql);

            if ($rs !== false && !empty($rs)) {
                $piwik = unserialize($rs[0]['value']);

                if (is_array($piwik) && array_key_exists('page_id', $piwik)) {
                    $i->page_views = $this->getPageViews($piwik['page_id']);
                }

                $message = "<fg=green>DONE</>";
            } else {
                $message = "<error>FAILED</>" . "Piwik code not available";
            }

            if ($this->output->isVeryVerbose()) {
                $this->output->writeln($message);
            }
        }

        $conn->close();

        // Check domain's rank in Alexa
        if ($options['alexa'] && !empty($i->domains)) {
            if ($this->output->isVeryVerbose()) {
                $this->output->write("\t- Getting rank from alexa ");
            }

            $i->alexa = $this->getAlexa($i->getMainDomain());

            if ($this->output->isVeryVerbose()) {
                $this->output->writeln("<fg=green>DONE</>");
            }
        }

        // Get media size
        if ($options['media_size']) {
            if ($this->output->isVeryVerbose()) {
                $this->output->write("\t- Getting media size ");
            }

            $this->getMediaSize($i);

            if ($this->output->isVeryVerbose()) {
                $this->output->writeln("<fg=green>DONE</>");
            }
        }
    }

    /**
     * Fetches the instance stats ()
     *
     * @param Instance $i The instance to get stats from
     */
    public function getInstanceStats(&$i)
    {
        $conn = $this->getContainer()->get('orm.manager')->getConnection('instance');

        // Count contents
        $sql = 'SELECT count(*) as total FROM contents';
        $rs  = $conn->fetchAll($sql);

        if (!empty($rs)) {
            $i->contents = $rs[0]['total'];
        }

        $sql = 'SELECT content_type_name as type, count(*) as total '
            .'FROM contents WHERE in_litter != 1 GROUP BY `fk_content_type`, `content_type_name`';

        $rs = $conn->fetchAll($sql);

        if (!empty($rs)) {
            foreach ($rs as $value) {
                $allowedContentTypes = array(
                    'advertisement',
                    'attachment',
                    'album',
                    'article',
                    'letter',
                    'opinion',
                    'photo',
                    'poll',
                    'static_page',
                    'video',
                    'widget'
                );

                if (!in_array($value['type'], $allowedContentTypes)) {
                    continue;
                }

                $type = $value['type'] . 's';
                $i->{$type} = $value['total'];
            }
        }

        // Count users
        $sql = "SELECT COUNT(id) FROM users WHERE type = 0 and activated = 1 and
            fk_user_group NOT REGEXP '^4$|^4,|,4,|,4$'";
        $rs  = $conn->fetchArray($sql);

        if ($rs !== false && !empty($rs)) {
            $i->users = $rs[0];
        }

        // Count emails
        $sql = 'SELECT counter FROM action_counters WHERE action_name = \'newsletter\'';
        $rs  = $conn->fetchArray($sql);

        if ($rs) {
            $i->emails = $rs[0];
        }

        // Get last login date
        $sql = 'SELECT * FROM settings WHERE name=\'last_login\' or name=\'time_zone\'';
        $rs  = $conn->fetchAll($sql);
        $tz  = new \DateTimeZone(date_default_timezone_get());

        $i->last_login = null;

        if ($rs !== false && !empty($rs)) {
            $settings = [];
            foreach ($rs as $r) {
                $settings[$r['name']] = unserialize($r['value']);
            }

            if (array_key_exists('time_zone', $settings) && !empty($settings['time_zone'])) {
                $this->getContainer()->get('core.locale')->setTimeZone($settings['time_zone']);
                $tz = $this->getContainer()->get('core.locale')->getTimeZone();
            }

            if (array_key_exists('last_login', $settings) && !empty($settings['last_login'])) {
                $i->last_login = new \DateTime($settings['last_login']);
            }
        }

        // Get the creation date of the last created content
        $sql = 'SELECT created FROM contents ORDER BY created desc LIMIT 1 ';
        $rs  = $conn->fetchAll($sql);

        if (!empty($rs) && !empty($rs[0]['created'])) {
            $created = new \DateTime($rs[0]['created'], $tz);
            $created->setTimeZone(new \DateTimeZone('UTC'));

            if (empty($i->last_login) || $created > $i->last_login) {
                $i->last_login = $created;
            }
        }
    }

    /**
     * Fetches the date of creation of the instance
     *
     * @param Instance $i The instance to get stats from
     */
    public function getCreatedDate(&$i)
    {
        $sql  = 'SELECT * FROM settings WHERE name=\'site_created\'';
        $conn = $this->getContainer()->get('orm.manager')->getConnection('instance');
        $rs   = $conn->fetchAssoc($sql);

        if ($rs !== false && !empty($rs)) {
            $created = new \DateTime(unserialize($rs['value']));
            $created->setTimeZone(new \DateTimeZone('UTC'));

            $i->created = $created;
        }
    }

    /**
     * Caculates the amount of Mb that an instance has
     *
     * @param Instance $i The instance to get stats from
     */
    public function getMediaSize(&$i)
    {
        $size      = 0;
        $mediaPath = realpath(SITE_PATH . "media" . DS . $i->internal_name);

        if ($mediaPath) {
            $size = (int) shell_exec('du -s '.$mediaPath.'/ | awk \'{ print $1}\'');
        }
        $i->media_size = $size / 1024;
    }

    /**
     * Gets the Alexa's rank for the given domain.
     *
     * @param  string  $domain The domain to check in Alexa.
     * @return integer         The Alexa's rank for the domain.
     */
    private function getAlexa($domain)
    {
        $rank = 100000000;
        $url  = "http://data.alexa.com/data?cli=10&dat=snbamz&url=" . $domain;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $resp = curl_exec($ch);
        curl_close($ch);

        preg_match('/<POPULARITY .* TEXT="(\\d+)"/', $resp, $matches);

        if (count($matches) > 0) {
            $rank = $matches[1];
        }

        return (int) $rank;
    }

    /**
     * Gets the number of page views from Piwik.
     *
     * @param  integer $siteId The site id in Piwik.
     * @return integer         The number of page views.
     */
    private function getPageViews($siteId)
    {
        if (!$siteId) {
            return 0;
        }

        $url   = $this->getContainer()->getParameter('piwik.url');
        $token = $this->getContainer()->getParameter('piwik.token');

        $from = new \DateTime('now');

        if ($from->format('d') <= '27') {
            $from->modify('-1 month');
        }

        $from->setDate($from->format('Y'), $from->format('m'), 27);

        $from = $from->format('Y-m-d');
        $to   = date('Y-m-d');

        $url .= "?module=API&method=API.get"
            . "&apiModule=VisitsSummary&apiAction=get"
            . "&idSite=$siteId"
            . "&period=range&date=$from,$to"
            . "&format=json"
            . "&showColumns=nb_pageviews"
            . "&token_auth=$token";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $resp = curl_exec($ch);
        curl_close($ch);

        $views = json_decode($resp, true);

        if (is_array($views) && array_key_exists('value', $views)) {
            return $views['value'];
        }

        return 0;
    }
}
