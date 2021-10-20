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

use Common\Core\Component\Exception\Instance\InstanceNotActivatedException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateLibraryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('generate:library-cronicas')
            ->setDefinition([
                new InputArgument('internal_name', InputArgument::REQUIRED, 'user'),
            ])
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
        $_SERVER['REQUEST_URI'] = '/';

        $internalName = $input->getArgument('internal_name');

        // Loads one ONM instance from database
        $instance = $this->getContainer()->get('core.loader.instance')
            ->loadInstanceByName($internalName)
            ->getInstance();

        if (empty($instance->activated)) {
            throw new InstanceNotActivatedException(_('Instance not activated'));
        }

        $date          = new \DateTime();
        $curly         = [];
        $directoryDate = $date->format("/Y/m/d/");
        $basePath      = SITE_PATH . "/media/" . $instance->internal_name
            . "/library" . $directoryDate;

        if (!file_exists($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $menu = $this->generateMenu();

        if (empty($menu->items)) {
            $input->writeln("There are no frontpages. You must define archive menu.");

            return;
        }

        $urlBase = "https://www.cronicasdelaemigracion.com/seccion/";

        // multi handle
        $mh = curl_multi_init();

        foreach ($menu->items as $item) {
            $category_slug = $item->link;

            if (!empty($category_slug)) {
                $curly[$category_slug] = curl_init();

                $url = $urlBase . $category_slug . '/';
                curl_setopt($curly[$category_slug], CURLOPT_URL, $url);
                curl_setopt($curly[$category_slug], CURLOPT_HEADER, 0);
                curl_setopt($curly[$category_slug], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curly[$category_slug], CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curly[$category_slug], CURLOPT_SSL_VERIFYPEER, 0);

                curl_multi_add_handle($mh, $curly[$category_slug]);
            }
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);


        // change menu to stay in archive fronpages
        $pattern     = [];
        $replacement = [];

        foreach ($menu->items as $item) {
            $category  = $item->link;
            $pattern[] = "@href=\"/seccion/{$category}\"@";
            //archive/digital/2013/02/02/home.html
            $replacement[] = "href=\"/archive/digital{$directoryDate}{$category}.html\"";
        }

        array_push($pattern, "@href=\"/\"@");
        //archive/digital/2013/02/02/home.html
        array_push($replacement, "href=\"/archive/digital{$directoryDate}home.html\"");

          // get content and remove handles
        foreach ($curly as $category_slug => $c) {
            $htmlOut = curl_multi_getcontent($c);

            $htmlOut = preg_replace($pattern, $replacement, $htmlOut);

            $newFile = $basePath . $category_slug . ".html";
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
     */
    public function generateMenu()
    {
        $menu = new \Menu();

        $menu->name  = 'archive';
        $menu->items = [];

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
        $item->link = 'cantabria';
        array_push($menu->items, $item);
        $item       = new \stdClass();
        $item->link = 'paisvasco';
        array_push($menu->items, $item);

        return $menu;
    }
}
