<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateLibraryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('generate:library-cronicas')
            ->setDefinition(
                array(
                    new InputArgument('internal_name', InputArgument::REQUIRED, 'user'),
                )
            )
            ->setDescription('Cron task for generating the static library from cronicasdelaemigracion')
            ->setHelp(
                <<<EOF
The <info>generate:library-cronicas</info> is a cron task for generating the static library.

<info>php app/console generate:library-cronicas</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $_SERVER['REQUEST_URI']   = '/';

        $internalName = $input->getArgument('internal_name');

        // Loads one ONM instance from database
        $im = $this->getContainer()->get('instance_manager');

        $instance = $im->findOneBy(
            array('internal_name' => array(array('value' => $internalName)))
        );

        //If found matching instance initialize its contants and return it
        if (is_object($instance)) {
            $instance->boot();

            // If this instance is not activated throw an exception
            if ($instance->activated != '1') {
                $message =_('Instance not activated');
                throw new \Onm\Instance\NotActivatedException($message);
            }
        } else {
            throw new \Onm\Instance\NotFoundException(_('Instance not found'));
        }

        $im->current_instance = $instance;
        $im->cache_prefix     = $instance->internal_name;

        $cache = $this->getContainer()->get('cache');
        $cache->setNamespace($instance->internal_name);

        // Initialize the instance database connection
        $databaseInstanceConnection = $this->getContainer()->get('db_conn');

        // CRAP: take this out, Workaround
        \Application::load();
        \Application::initDatabase($databaseInstanceConnection);

        // multi handle
        $curlHandler = curl_multi_init();

        $urlBase = "http://www.cronicasdelaemigracion.es/seccion/";

        $date          = new \DateTime();
        $directoryDate = $date->format("/Y/m/d/");
        $basePath      = SITE_PATH."/media/cronicas/library".$directoryDate;
        $curly         = array();

        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $menu = $this->generateMenu();

        if (count($menu->items) <= 0) {
            $input->writeln("There are no frontpages. You must define archive menu.");

            return;
        }

        $urlBase = "http://www.cronicasdelaemigracion.com/seccion/";

        // multi handle
        $mh = curl_multi_init();

        foreach ($menu->items as $item) {
            $category_name = $item->link;

            if (!empty($category_name)) {

                $curly[$category_name] = curl_init();

                $url = $urlBase. $category_name.'/';
                curl_setopt($curly[$category_name], CURLOPT_URL, $url);
                curl_setopt($curly[$category_name], CURLOPT_HEADER, 0);
                curl_setopt($curly[$category_name], CURLOPT_RETURNTRANSFER, 1);

                curl_multi_add_handle($mh, $curly[$category_name]);
            }
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);


        // change menu to stay in archive fronpages
        $pattern     = array();
        $replacement = array();

        foreach ($menu->items as $item) {
            $category = $item->link;
            $pattern[] = "@href=\"/seccion/{$category}\"@";
            //archive/digital/2013/02/02/home.html
            $replacement[] = "href=\"/archive/digital{$directoryDate}{$category}.html\"";
        }

        array_push($pattern, "@href=\"/\"@");
        //archive/digital/2013/02/02/home.html
        array_push($replacement, "href=\"/archive/digital{$directoryDate}home.html\"");

          // get content and remove handles
        foreach ($curly as $category_name => $c) {
            $htmlOut = curl_multi_getcontent($c);

            $htmlOut = preg_replace($pattern, $replacement, $htmlOut);

            $newFile = $basePath.$category_name.".html";
            file_put_contents($newFile, $htmlOut);

            curl_multi_remove_handle($mh, $c);
        }
          // all done
        curl_multi_close($mh);

        $output->write('Generation completed');
    }

    /**
     * Generates a dummy  menu
     *
     * @return Menu the object menu with elements
     **/
    public function generateMenu()
    {
        $menu = new \Menu();

        $menu->name  ='archive';
        $menu->items = array();

        $item       = new \stdClass();
        $item->link = 'home';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'cronicas';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'galicia';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'galicia-exporta';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'castillaleon';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'asturias';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'canarias';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'andalucia';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'catabria';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'paisvasco';
        array_push($menu->items, $item);

        return $menu;
    }
}
