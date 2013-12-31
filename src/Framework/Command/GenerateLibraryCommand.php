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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateLibraryCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate:library-cronicas')
            ->setDescription('Cron task for generating the static library from cronicasdelaemigracion')
            ->setDefinition(
                array(
                    new InputArgument('instance', InputArgument::REQUIRED, 'user'),
                )
            )
            ->setHelp(
                <<<EOF
The <info>generate:library-cronicas</info> is a cron task for generating the static library.

<info>php app/console generate:library-cronicas</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $_SERVER['SERVER_NAME']   = 'www.cronicasdelaemigracion.com';
        $_SERVER['REQUEST_URI']   = '/';
        $_SERVER['REQUEST_PORT']  = '8080';
        $_SERVER['SERVER_PORT']   = '8080';
        $_SERVER['HTTP_HOST']     ='www.cronicasdelaemigracion.com';

        $instance = $input->getArgument('instance');

        global $sc;
        $framework = $sc->get('framework');


        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
        $requestEvent = new \Symfony\Component\HttpKernel\Event\GetResponseEvent($framework, $request, \Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST);

        $appBootupListener = new \Framework\EventListeners\ApplicationBootupListener();
        $appBootupListener->onKernelRequest($requestEvent);

        // Loads one ONM instance from database
        $im = $sc->get('instance_manager');

        $instance = $im->load($instance);

        $sc->setParameter('instance', $instance);
        $sc->setParameter('cache_prefix', $instance->internal_name);


        $GLOBALS['application']->conn = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->conn->Connect(BD_HOST, BD_USER, BD_PASS, BD_DATABASE);
        $GLOBALS['application']->conn->bulkBind = true;

        // multi handle
        $curlHandler = curl_multi_init();

        $urlBase = SITE_URL."seccion/";

        $date          = new \DateTime();
        $directoryDate = $date->format("/Y/m/d/");
        $basePath      = SITE_PATH."/media/cronicas/library".$directoryDate;
        $curly         = array();

        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $menu = new \Menu();
        $menu->getMenu('archive');

        if (count($menu->items) <= 0) {
            $input->writeln("There are no frontpages. You must define archive menu.");

            return;
        }


        foreach ($menu->items as $item) {
            $categoryName = $item->link;

            if (!empty($categoryName)) {
                $curly[$categoryName] = curl_init();

                $url = $urlBase. $categoryName.'/';
                curl_setopt($curly[$categoryName], CURLOPT_URL, $url);
                curl_setopt($curly[$categoryName], CURLOPT_HEADER, 0);
                curl_setopt($curly[$categoryName], CURLOPT_RETURNTRANSFER, 1);

                curl_multi_add_handle($curlHandler, $curly[$categoryName]);
            }
        }

          // execute the handles
        $running = null;
        do {
            curl_multi_exec($curlHandler, $running);
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
        foreach ($curly as $categoryName => $c) {
            $htmlOut = curl_multi_getcontent($c);

            $htmlOut = preg_replace($pattern, $replacement, $htmlOut);

            var_dump($htmlOut);die();


            $newFile = $basePath.$categoryName.".html";
            $result  = file_put_contents($newFile, $htmlOut);

            curl_multi_remove_handle($curlHandler, $c);
        }

        // all done
        curl_multi_close($curlHandler);

        $input->writeln('Generation done');
    }
}
