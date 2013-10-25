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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption('skip-cleaning', 's', InputOption::VALUE_NONE, 'Skip cleaning caches'),
                )
            )
            ->setName('app:deploy')
            ->setDescription('Deploys the application to the latest version')
            ->setHelp(
                <<<EOF
The <info>app:deploy</info> checks out the latest code for core and templates.
And updates all library dependencies in vendor/.

<info>php app/console app:deploy</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = APPLICATION_PATH;
        $phpBinPath = exec('which php');
        $this->input = $input;
        $this->output = $output;

        chdir($basePath);

        $this->executeMaintenance('enable');

        // Update onm-core
        $output->writeln(" - Updating onm instance");
        $gitOutput = exec('git pull');
        $output->writeln($gitOutput."\n");

        $this->executeMaintenance('disable');

        // Update themes
        $output->write(" - Updating public themes");
        foreach (glob($basePath.'/public/themes/*') as $theme) {
            // Avoid to execute pull in admin and manager themes.
            if (basename($theme) == 'admin' || basename($theme) == 'manager') {
                continue;
            }
            chdir($theme);
            $output->write("\n     * ".basename($theme));
            $gitOutput = exec('git pull');
            chdir($basePath);
            // $output->writeln("");
        }
        $output->writeln('');

        // Update dependencies
        $output->writeln(" - Updating vendor libraries");
        $composerOutput = exec($phpBinPath.' '.$basePath.'/bin/composer.phar install -o');
        $output->writeln($composerOutput."\n");

        $skipCleaning = $input->getOption('skip-cleaning');
        if (!$skipCleaning) {
            // Clean cache and compiles
            $command = $this->getApplication()->find('clean:smarty-cache');
            $arguments = array(
                'command' => 'clean:smarty-cache',
            );

            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        }
    }

    /**
     * Enables or disables the maintenance mode
     *
     * @param string $action enable or disable
     **/
    public function executeMaintenance($action)
    {
        $command = $this->getApplication()->find('app:maintenance');
        $arguments = array(
            'command' => 'app:maintenance',
            'action'  => $action,
        );

        $this->input = new ArrayInput($arguments);
        $returnCode = $command->run($this->input, $this->output);
    }
}
