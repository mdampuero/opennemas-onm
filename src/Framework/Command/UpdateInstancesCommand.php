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

use Framework\ORM\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateInstancesCommand extends ContainerAwareCommand
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
                "Updates the counters in instances table in onm-instances by
                 collecting data from different sources."
            )
            ->addOption(
                'alexa',
                false,
                InputOption::VALUE_NONE,
                'If set, the command will check the domain rank in alexa.'
            )
            ->addOption(
                'views',
                false,
                InputOption::VALUE_NONE,
                'If set, the command will get the page views from Piwik.'
            )
            ->addOption(
                'created',
                false,
                InputOption::VALUE_NONE,
                'If set, the command will get the created date from instance.'
            )
            ->addOption(
                'debug',
                false,
                InputOption::VALUE_NONE,
                'If set, the command will be run in debug mode.'
            )->addOption(
                'offset',
                0,
                InputOption::VALUE_OPTIONAL,
                'If set, this command will only be run in 30 instances from page [offset].'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $alexa   = $input->getOption('alexa');
        $offset  = $input->getOption('offset');
        $created = $input->getOption('created');
        $views   = $input->getOption('views');

        $amount = ($offset) ? 30: null;

        $this->im  = $this->getContainer()->get('instance_manager');
        $this->em  = $this->getContainer()->get('orm.manager');
        $instances = $this->im->findBy(null, array('id', 'asc'), $amount, $offset);

        if (count($instances) == 0) {
            $output->writeln('No instances');
            return;
        }

        foreach ($instances as $instance) {
            if ($output->isVerbose()) {
                $output->writeln('Getting info about \''.$instance->internal_name.'\'');
            }

            $this->getInstanceInfo($instance, $alexa, $views, $created);
            $this->im->persist($instance);

            if ($instance->users > 1
                || $instance->page_views > 45000
                || $instance->media_size > 450
            ) {
                $this->createNotification($instance);
            }
        }
    }

    private function createNotification($instance)
    {
        $nr      = $this->em->getRepository('manager.notification');
        $tpl     = new \TemplateManager();

        $criteria = [
            'instance_id' => [ [ 'value' => $instance->id ] ],
            'fixed'       => [ [ 'value' => 1 ] ],
            'creator'     => [ [ 'value' => 'cron.update_instances' ] ]
        ];

        $notification = $nr->findOneBy($criteria);

        if (empty($notification)) {
            $notification = new Notification();

            $notification->instance_id = $instance->id;
            $notification->creator     = 'cron.update_instances';
            $notification->fixed       = 1;
            $notification->style       = 'warning';
            $notification->type        = 'info';
        }

        $notification->start = date('Y-m-d H:i:s');
        $notification->end   = date('Y-m-d H:i:s', time() + 86400);

        $notification->title = [
            'en' => 'Instance information',
            'es' => 'Información de la instancia',
            'gl' => 'Información da instancia',
        ];

        $notification->body = [
            'en' => $tpl->fetch(
                'base/instance_limit.tpl',
                [ 'instance' => $instance, 'language' => 'en' ]
            ),
            'es' => $tpl->fetch(
                'base/instance_limit.tpl',
                [ 'instance' => $instance, 'language' => 'es' ]
            ),
            'gl' => $tpl->fetch(
                'base/instance_limit.tpl',
                [ 'instance' => $instance, 'language' => 'gl' ]
            ),
        ];

        $this->em->persist($notification);
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

        return $rank;
    }

    /**
     * Gets the instance information.
     *
     * @param  Instance $i     The instance.
     * @param  boolean  $alexa Whether to get the Alexa's rank.
     * @param  boolean  $views Whether to get the page views.
     * @param  boolean  $views Whether to get the created date.
     */
    private function getInstanceInfo(&$i, $alexa = false, $views = false, $created = false)
    {
        if (empty($i->getDatabaseName())) {
            return false;
        }

        $this->im->getConnection()->selectDatabase($i->getDatabaseName());

        // Count contents
        $sql = 'SELECT content_type_name as type, count(*) as total '
            .'FROM contents GROUP BY `fk_content_type`';

        $rs = $this->im->conn->fetchAll($sql);

        if ($rs !== false && !empty($rs)) {
            $contents = 0;

            foreach ($rs as $value) {
                $allowedContentTypes = array(
                    'article',
                    'opinion',
                    'advertisement',
                    'album',
                    'photo',
                    'video',
                    'widget',
                    'static_page'
                );

                if (!in_array($value['type'], $allowedContentTypes)) {
                    continue;
                }

                $type = $value['type'] . 's';
                $i->{$type} = $value['total'];
                $contents += $value['total'];
            }

            $i->contents = $contents;
        }

        // Count users
        $sql = "SELECT COUNT(id) FROM users WHERE type = 0 and activated = 1 and
            fk_user_group NOT REGEXP '^4$|^4,|,4,|,4$'";
        $rs  = $this->im->getConnection()->fetchArray($sql);

        if ($rs !== false && !empty($rs)) {
            $i->users = $rs[0];
        }

        // Check domain's rank in Alexa
        if ($alexa && !empty($i->domains)) {
            $i->alexa = $this->getAlexa($i->getMainDomain());
        }

        // Count emails
        $sql = 'SELECT counter FROM action_counters WHERE action_name = \'newsletter\'';
        $rs  = $this->im->getConnection()->fetchArray($sql);

        if ($rs) {
            $i->emails = $rs[0];
        }

        // Update created data
        if ($created) {
            $sql = 'SELECT * FROM settings WHERE name=\'site_created\'';
            $rs  = $this->im->getConnection()->fetchAll($sql);

            if ($rs !== false && !empty($rs)) {
                foreach ($rs as $value) {
                    $i->created = unserialize($rs['value']);
                }
            }
        }

        // Get Piwik config and last invoice date
        $sql = 'SELECT * FROM settings WHERE name=\'piwik\' OR name=\'last_login\'';
        $rs  = $this->im->getConnection()->fetchAll($sql);

        $piwik = null;

        if ($rs !== false && !empty($rs)) {
            foreach ($rs as $value) {
                if ($value['name'] == 'piwik') {
                    $piwik = unserialize($value['value']);
                } else {
                    $i->last_login = unserialize($value['value']);
                }
            }
        }

        // Get the creation date of the last created content
        $sql = 'SELECT created FROM contents ORDER BY created desc LIMIT 1 ';
        $rs  = $this->im->getConnection()->fetchAll($sql);

        if ($rs !== false && !empty($rs)) {
            if ($rs[0]['created'] > $i->last_login) {
                $i->last_login = $rs[0]['created'];
            }
        }

        $this->im->getConnection()->close();

        // Get the page views from Piwik
        if ($views && !empty($piwik)) {
            $i->page_views = $this->getViews($piwik['page_id']);
        }

        // Get media size
        $size = 0;
        $mediaPath = realpath(SITE_PATH."media".DS.$i->internal_name);
        if ($mediaPath) {
            $size = (int) shell_exec('du -s '.$mediaPath.'/ | awk \'{ print $1}\'');
        }
        $i->media_size = $size / 1024;
    }

    /**
     * Gets the number of page views from Piwik.
     *
     * @param  integer $siteId The site id in Piwik.
     * @return integer         The number of page views.
     */
    private function getViews($siteId)
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

        $to = date('Y-m-d');

        $url .= "?module=API&method=API.get"
            . "&apiModule=VisitsSummary&apiAction=get"
            . "&idSite=$siteId"
            . "&period=range&date=$from,$to"
            . "&format=json"
            . "&showColumns=nb_visits"
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
