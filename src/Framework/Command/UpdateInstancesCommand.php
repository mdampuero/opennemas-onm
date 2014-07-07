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
                'debug',
                false,
                InputOption::VALUE_NONE,
                'If set, the command will be run in debug mode.'
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
        $alexa = $input->getOption('alexa');
        $views = $input->getOption('views');

        $this->im = $this->getContainer()->get('instance_manager');

        $instances = $this->im->findBy(null, array('id', 'asc'));

        foreach ($instances as $instance) {
            $counters = $this->getInstanceInfo($instance, $alexa, $views);
            $this->setDatabaseInfo($instance->id, $counters);
        }
    }

    /**
     * Gets the Alexa's rank for the given domain.
     *
     * @param  string  $domain The domain to check in Alexa.
     * @return integer         The Alexa's rank for the domain.
     */
    private function getAlexa($domain)
    {
        $rank = 0;
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
     */
    private function getInstanceInfo($i, $alexa = false, $views = false)
    {
        $counters = array(
            'contents'   => 0,
            'media_size' => 0,
            'alexa'      => 0,
            'page_views' => 0,
            'users'      => 0,
            'emails'     => 0
        );

        $this->im->getConnection()->selectDatabase($i->settings['BD_DATABASE']);

        // Count contents
        $sql = 'SELECT COUNT(pk_content) FROM contents';
        $rs  = $this->im->getConnection()->fetchArray($sql);

        if ($rs !== false && !empty($rs)) {
            $counters['contents'] = $rs[0];
        }

        // Count users
        $sql = 'SELECT COUNT(id) FROM users WHERE type = 0';
        $rs  = $this->im->getConnection()->fetchArray($sql);

        if ($rs !== false && !empty($rs)) {
            $counters['users'] = $rs[0];
        }

        // Check domain's rank in Alexa
        if ($alexa && !empty($i->domains)) {
            $domains = explode(',', $i->domains);
            $counters['alexa'] = $this->getAlexa($domains[0]);
        }

        // Count emails
        $sql = 'SELECT counter FROM action_counters WHERE action_name = \'newsletter\'';
        $rs  = $this->im->getConnection()->fetchArray($sql);

        if ($rs) {
            $counters['emails'] = $rs[0];
        }

        // Get Piwik config and last invoice date
        $sql = 'SELECT * FROM settings WHERE name=\'piwik\' OR name=\'last_invoice\'';
        $rs  = $this->im->getConnection()->fetchAll($sql);

        $piwik       = null;
        $lastInvoice = null;
        if ($rs !== false && !empty($rs)) {
            foreach ($rs as $value) {
                if (array_key_exists('name', $value)
                    &&  $value['name'] == 'piwik'
                ) {
                    $piwik = unserialize($value['value']);
                } elseif (array_key_exists('name', $value)
                    &&  $value['name'] == 'last_invoice'
                ) {
                    $lastInvoice = unserialize($value['value']);
                }
            }
        }

        // Get the page views from Piwik
        if ($views && !empty($piwik) && !empty($lastInvoice)) {
            $counters['page_views'] = $this->getViews($piwik['page_id'], $lastInvoice);
        }

        // Get media size
        $size = explode("\t", shell_exec('du -s '.SITE_PATH."media".DS.$i->internal_name.'/'));
        if (is_array($size)) {
            $counters['media_size'] = $size[0] / 1024;
        }

        return $counters;
    }

    /**
     * Gets the number of page views from Piwik.
     *
     * @param  integer $siteId The site id in Piwik.
     * @param  string  $from   Date of the last invoice.
     * @return integer         The number of page views.
     */
    private function getViews($siteId, $from)
    {
        if (!$siteId) {
            return 0;
        }

        $url   = $this->getContainer()->getParameter('piwik.url');
        $token = $this->getContainer()->getParameter('piwik.token');

        $from = date_create_from_format('Y-m-d H:i:s', $from)->format('Y-m-d');
        $to   = date('Y-m-d');

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

        if (array_key_exists('value', $views)) {
            return $views['value'];
        }

        return 0;
    }

    /**
     * Updates the instance with the new information.
     *
     * @param array $id       The instance id.
     * @param array $counters Array of counters.
     */
    private function setDatabaseInfo($id, $counters)
    {
        $inserts = array();
        foreach ($counters as $key => $value) {
            $inserts[] = $key . ' = ' . $value;
        }

        $this->im->getConnection()->selectDatabase('onm-instances');

        $sql = 'UPDATE instances SET ' . implode(',', $inserts)
            . ' WHERE id=' . $id;

        $rs  = $this->im->getConnection()->executeQuery($sql);
    }
}
