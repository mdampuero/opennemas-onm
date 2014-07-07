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
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $alexa = $input->getOption('alexa');
        $views = $input->getOption('views');

        $im = $this->getContainer()->get('instance_manager');

        $instances = $im->findBy(null, array('id', 'asc'));

        foreach ($instances as $instance) {
            $this->getInstanceInfo($im, $instance, $alexa, $views);
            $this->setDatabaseInfo($im);
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
     * @param  InstanceManager $im    The instance manager.
     * @param  Instance        $i     The instance.
     * @param  boolean         $alexa Whether to get the Alexa's rank.
     * @param  boolean         $views Whether to get the page views.
     */
    private function getInstanceInfo($im, $i, $alexa = false, $views = false)
    {
        $im->getConnection()->selectDatabase($i->settings['BD_DATABASE']);

        $sql = 'SELECT COUNT(pk_content) FROM contents';
        $rs  = $im->getConnection()->fetchArray($sql);

        if ($rs !== false) {
            $this->contents = $rs[0];
        }

        $sql = 'SELECT COUNT(id) FROM users';
        $rs  = $im->getConnection()->fetchArray($sql);

        if ($rs !== false) {
            $this->users = $rs[0];
        }

        // Check domain's rank in Alexa
        if ($alexa && !empty($i->domains)) {
            $domains = explode(',', $i->domains);
            $this->alexa = $this->getAlexa($i->domains[0]);
        }

        // Get the page views from Piwik
        if ($views && !empty($i->domains)) {
            $domains = explode(',', $i->domains);
            $this->views = $this->getViews($i->domains[0]);
        }
    }

    /**
     * Gets the number of page views from Piwik.
     *
     * @param  string  $domain The domain to check in Alexa.
     * @return integer         The number of page views.
     */
    private function getViews($domain)
    {
    }

    /**
     * Updates the instance with the new information.
     *
     * @param InstanceManager $im The instance manager.
     */
    private function setDatabaseInfo($im)
    {
        if (!$this->contents && !$this->users) {
            return;
        }

        $inserts = array();
        if ($this->contents) {
            $inserts[] = 'contents = ' . $this->contents;
        }

        if ($this->users) {
            $inserts[] = 'users = ' . $this->users;
        }

        if ($this->alexa) {
            $inserts[] = 'alexa = ' . $this->alexa;
        }

        if ($this->views) {
            $inserts[] = 'views = ' . $this->views;
        }

        $im->getConnection()->selectDatabase('onm-instances');

        $sql = 'UPDATE instances SET ' . implode(',', $inserts);
        $rs  = $im->getConnection()->executeQuery($sql);
    }
}
