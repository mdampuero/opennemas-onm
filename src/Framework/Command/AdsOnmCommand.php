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

use Onm\StringUtils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class AdsOnmCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('action', InputArgument::REQUIRED, 'action'),
                    new InputArgument('instance-name', InputArgument::REQUIRED, 'instance-name'),
                    new InputArgument('ads-file', InputArgument::REQUIRED, 'ads-file'),
                )
            )
            ->setName('ads:onm')
            ->setDescription(
                'Executes command to create onm ads on instace'
            )
            ->setHelp(
                <<<EOF
The <info>ads:onm</info> executes command to create/remove onm ads in an instance.

<info>php app/console ads:onm action instance-name ads-file</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action       = $input->getArgument('action');
        $instanceName = $input->getArgument('instance-name');
        $ads          = $input->getArgument('ads-file');

        $this->input  = $input;
        $this->output = $output;

        if (!file_exists($ads)) {
            throw new \Exception("Can't access the ads file or doesn't exists");
        }

        $loader = $this->getContainer()->get('core.loader');
        $loader->loadInstanceFromInternalName($instanceName);

        $instance = $loader->getInstance();
        $database = $instance->getDatabaseName();

        $this->getContainer()->get('dbal_connection')->selectDatabase($database);
        $this->getContainer()->get('session')->set(
            'user',
            json_decode(json_encode([ 'id' => 0, 'username' => 'console' ]))
        );

        $yaml = new Parser();
        $ads  = $yaml->parse(file_get_contents($ads));

        switch ($action) {
            case 'create':
                $this->createAds($ads);
                break;

            case 'remove':
                $this->removeAds($ads);
                break;

            case 'update':
                $this->updateAds($ads);
                break;

            default:
                $output->writeln('Unknown action parameter. Choose create, update or remove.');
                break;
        }
    }

    /**
     * Creates ads on a instance
     *
     */
    public function createAds($ads)
    {
        if (is_null($ads) || empty($ads)) {
            throw new \Exception("File content is not valid");
        }

        $fm = $this->getContainer()->get('data.manager.filter');
        $am = new \Advertisement();
        foreach ($ads as $ad) {
            $data = [
                'title' => $ad['title'],
                'metadata' => $fm->set($ad['title'])->filter('tags')->get(),
                'category' => '0',
                'categories' => $ad['categories'],
                'available' => '1',
                'content_status' => '1',
                'with_script' => '1',
                'overlap' => '',
                'type_medida' => 'NULL',
                'num_clic' => '',
                'num_view' => '',
                'starttime' => '2015-08-14 00:00:00',
                'endtime' => '',
                'timeout' => '15',
                'url' => 'http://www.opennemas.com',
                'img' => '',
                'script' => $ad['script'],
                'type_advertisement' => $ad['type'],
                'fk_author' => '0',
                'fk_publisher' => '0',
                'params' =>
                    [
                        'width' => $ad['width'],
                        'height' => $ad['height'],
                        'openx_zone_id' => '',
                        'googledfp_unit_id' => '',
                    ]
            ];

            if ($am->create($data)) {
                $this->output->writeln('Ad <fg=green>' . $data['title'] . '</fg=green> created successfully');
            } else {
                $this->output->writeln('<bg=red>Failed to create ad ' . $data['title'] . '</bg=red>');
            }
        }
    }

    /**
     * Remove ads on a instance
     *
     */
    public function removeAds($ads)
    {
        if (is_null($ads) || empty($ads)) {
            throw new \Exception("File content is not valid");
        }

        foreach ($ads as $ad) {
            $filters = [
                'title'                 => [[ 'value' => $ad['title'] ]],
                'type_advertisement'    => [[ 'value' => $ad['type'] ]],
                'available'             => [[ 'value' => '1' ]],
                'content_status'        => [[ 'value' => '1' ]],
            ];

            if (array_key_exists('categories', $ad)) {
                if (is_array($ad['categories'])) {
                    $ad['categories'] = implode(',', $ad['categories']);
                }

                $filters['fk_content_categories'] = [[ 'value' => $ad['categories'] ]];
            }

            $adv = getService('advertisement_repository')->findOneBy($filters, null, null, null);

            if (!is_null($adv)) {
                if ($adv->remove($adv->id)) {
                    $this->output->writeln('Ad <fg=green>' . $ad['title'] . '</fg=green> removed successfully');
                } else {
                    $this->output->writeln('<bg=red>Failed to remove ad ' . $ad['title'] . '</bg=red>');
                }
            } else {
                $this->output->writeln('<bg=red>Ad ' . $ad['title'] . ' does not exist</bg=red>');
            }
        }
    }

    /**
     * Update ads on a instance
     *
     */
    public function updateAds($ads)
    {
        if (is_null($ads) || empty($ads)) {
            throw new \Exception("File content is not valid");
        }

        foreach ($ads as $ad) {
            $filters = [
                'title'          => [[ 'value' => $ad['old']['title'] ]],
                'available'      => [[ 'value' => '1' ]],
                'content_status' => [[ 'value' => '1' ]],
            ];

            if (array_key_exists('categories', $ad['old'])) {
                if (is_array($ad['old']['categories'])) {
                    $ad['old']['categories'] = implode(',', $ad['old']['categories']);
                }

                $filters['fk_content_categories'] = [[ 'value' => $ad['old']['categories'] ]];
            }

            if (array_key_exists('type', $ad['old'])) {
                $filters['type_advertisement'] = [[ 'value' => $ad['old']['type'] ]];
            }

            $adv = getService('advertisement_repository')->findOneBy($filters, null, null, null);

            if (!is_null($adv)) {
                $title      = (array_key_exists('title', $ad['new']))
                    ? $ad['new']['title'] : $adv->title;
                $categories = (array_key_exists('categories', $ad['new']))
                    ? $ad['new']['categories'] : implode(',', $adv->fk_content_categories);
                $script     = (array_key_exists('script', $ad['new']))
                    ? $ad['new']['script'] : $adv->script;
                $type       = (array_key_exists('type', $ad['new']))
                    ? $ad['new']['type'] : $adv->type_advertisement;
                $width      = (array_key_exists('width', $ad['new']))
                    ? $ad['new']['width'] : $adv->params['width'];
                $height     = (array_key_exists('height', $ad['new']))
                    ? $ad['new']['height'] : $adv->params['height'];

                $fm   = $this->getContainer()->get('data.manager.filter');
                $data = [
                    'id'                 => $adv->id,
                    'title'              => $title,
                    'metadata'           => $fm->set($title)->filter('tags')->get(),
                    'category'           => '0',
                    'categories'         => $categories,
                    'available'          => '1',
                    'content_status'     => '1',
                    'with_script'        => '1',
                    'overlap'            => '',
                    'type_medida'        => 'NULL',
                    'num_clic'           => '',
                    'num_view'           => '',
                    'starttime'          => '2015-08-14 00:00:00',
                    'endtime'            => '',
                    'timeout'            => '15',
                    'url'                => 'http://www.opennemas.com',
                    'img'                => '',
                    'script'             => $script,
                    'type_advertisement' => $type,
                    'fk_author'          => '0',
                    'fk_publisher'       => '0',
                    'params'             =>
                        [
                            'width' => $width,
                            'height' => $height,
                            'openx_zone_id' => '',
                            'googledfp_unit_id' => '',
                        ]
                ];

                if ($adv->update($data)) {
                    $this->output->writeln('Ad <fg=green>' . $title . '</fg=green> updated successfully');
                } else {
                    $this->output->writeln('<bg=red>Failed to update ad ' . $title . '</bg=red>');
                }
            } else {
                $this->output->writeln('<bg=red>Ad ' . $ad['old']['title'] . ' does not exist</bg=red>');
            }
        }
    }
}
