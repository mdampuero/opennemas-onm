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

use Common\Core\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

class InstanceConvertLogoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('instance:convertlogo')
            ->setDescription('Convert logos to image objects.')
            ->addOption(
                'instances',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The list of instances to convert logo(e.g. norf quux)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->start();
        $output->writeln(sprintf(
            str_pad('<options=bold>(1/3) Starting command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $instances = $this->getInstances($input->getOption('instances'));


        $output->writeln(sprintf(
            str_pad('<options=bold>(2/3) Processing instances', 43, '.')
                . '<fg=yellow;options=bold>IN PROGRESS</> '
                . '<fg=blue;options=bold>(%s instances)</></>',
            count($instances)
        ));

        $i = 1;
        foreach ($instances as $instance) {
            $this->getContainer()->get('core.loader')
                ->load($instance->internal_name);

            $this->getContainer()->get('core.security')->setInstance($instance);

            $ds = $this->getContainer()->get('orm.manager')->getDataSet('Settings', 'instance');
            $ps = $this->getContainer()->get('api.service.photo');
            $sh = $this->getContainer()->get('core.helper.setting');

            $logos = [
                'default' => 'site_logo',
                'simple'  => 'mobile_logo',
                'favico'  => 'favico',
                'embed'   => 'sn_default_img',
            ];

            $output->writeln(str_pad(sprintf(
                '<fg=blue;options=bold>==></><options=bold> (%s/%s)'
                . ' Processing instance %s</>',
                $i++,
                count($instances),
                $instance->internal_name
            ), 50, '.'));

            foreach ($logos as $key => $setting) {
                $output->write(str_pad(sprintf(
                    '<fg=yellow;options=bold>=====></><options=bold> Processing %s</> ',
                    $key,
                ), 100, '.'));

                if ($sh->hasLogo($key) || empty($ds->get($setting))) {
                    $output->writeln(sprintf(
                        '<fg=yellow;options=bold>SKIP</>',
                    ));
                    continue;
                }

                $path = 'sections/' . $ds->get($setting);
                $file = $this->getContainer()->getParameter('core.paths.public')
                    . $instance->getMediaShortPath()
                    . '/' . $path;

                if (!file_exists($file)) {
                    $output->writeln(sprintf(
                        '<fg=yellow;options=bold>SKIP</>'
                    ));

                    continue;
                }

                try {
                    $photo = $ps->createItem([
                        'created'     => '2021-01-01 00:00:00',
                        'description' => 'logo_' . $key
                    ], new File($file), true);

                    $ds->set([ 'logo_' . $key => $photo->pk_content ]);

                    $output->writeln(sprintf(
                        '<fg=green;options=bold>DONE</>'
                    ));
                } catch (\Exception $e) {
                    $output->writeln(sprintf(
                        '<fg=red;options=bold>FAIL</>'
                    ));
                }
            }
        }

        $this->end();
        $output->writeln(sprintf(
            str_pad('<options=bold>(3/3) Ending command', 50, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</></>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
    }

    /**
     * Returns the list of active instances.

     * @return array The list of instances.
     */
    protected function getInstances(?array $names = []) : array
    {
        $oql = sprintf(
            'activated = 1',
        );

        if (!empty($names)) {
            $oql .= sprintf(
                ' and internal_name in ["%s"]',
                implode('","', $names)
            );
        }

        return $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);
    }
}
