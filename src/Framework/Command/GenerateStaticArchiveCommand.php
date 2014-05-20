<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Generate static archive Openemas
 *
 * t
 **/
namespace Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateStaticArchiveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('serverName', InputArgument::REQUIRED, 'serverName'),
                )
            )
            ->setName('generate:staticArchive')
            ->setDescription('Generate static archive')
            ->setHelp(
                <<<EOF
The <info>generate:staticArchive</info> command generate file pages for static pages archive.

<info>php bin/console generate:staticArchive serverName</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = APPLICATION_PATH;
        $phpBinPath = exec('which php');

        chdir($basePath);

        $serverName = $input->getArgument('serverName');

        $_SERVER['HTTP_HOST']     ='www.cronicasdelaemigracion.com';
        $_SERVER['SERVER_NAME']   = 'www.cronicasdelaemigracion.com';
        $_SERVER['REQUEST_URI']   = '/';


        $this->generateStatics($input, $output);

        $output->writeln(
            "\n\t<fg=yellow;bg=white>Migration finished for Database: ".$serverName."</fg=yellow;bg=white>"
        );
    }

    /**
     * Read articles data and insert in new database
     *
     * @return void
     **/
    protected function generateStatics($input, $output)
    {

        $request = Request::createFromGlobals();
        $request->setTrustedProxies(array('127.0.0.1'));

        $framework = $sc->get('framework');
        $response = $framework->handle($request);
        $response->send();
        $framework->terminate($request, $response);

        $urlBase = SITE_URL."seccion/";

        $date          =  new DateTime();
        $directoryDate = $date->format("/Y/m/d/");
        $basePath      = SITE_PATH."/media/cronicas/library".$directoryDate;
        $curly         = array();

        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }

        // multi handle
        $mh = curl_multi_init();

        $menu = new \Menu();
        $menu->getMenu('archive');

        if (count(($menu->items)) <= 0) {
            echo "There are no frontpages. You must define archive menu. \n";
            die();
        }

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
            $result  = file_put_contents($newFile, $htmlOut);

            curl_multi_remove_handle($mh, $c);
        }
          // all done
        curl_multi_close($mh);
        $output->writeln("generate ok \n");
        return true;
    }
}
