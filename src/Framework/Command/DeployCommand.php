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
        $this->basePath = APPLICATION_PATH;
        $this->input = $input;
        $this->output = $output;

        chdir($this->basePath);

        $this->executeMaintenance('enable');

        $this->updateCoreCode();

        $this->executeMaintenance('disable');

        // Update themes
        $this->updateThemes();

        // Clean cache if required
        $skipCleaning = $input->getOption('skip-cleaning');
        if (!$skipCleaning) {
            $this->cleanCache();
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

        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $this->output);
    }

    /**
     * Updates the core application code
     **/
    public function updateCoreCode()
    {
        $phpBinPath = exec('which php');

        // Update onm-core
        $this->output->writeln(" - Updating onm instance");
        $gitOutput = exec('git pull');
        $this->output->writeln($gitOutput."\n");

        // Update dependencies
        $this->output->writeln(" - Updating vendor libraries");
        $composerOutput = exec($phpBinPath.' '.$this->basePath.'/bin/composer.phar install -o');
        $this->output->writeln($composerOutput."\n");
    }

    /**
     * Updates the public themes
     **/
    public function updateThemes()
    {
        $this->output->writeln(" - Updating public themes");
        foreach (glob($this->basePath.'/public/themes/*') as $theme) {
            // Avoid to execute pull in admin and manager themes.
            if (basename($theme) == 'admin' || basename($theme) == 'manager') {
                continue;
            }
            chdir($theme);
            $this->output->writeln("\t* ".basename($theme));
            $gitOutput = exec('git pull');
            chdir($this->basePath);
        }
        $this->output->writeln('');
    }

    /**
     * Executes the clean cache action
     **/
    public function cleanCache()
    {
        $command = $this->getApplication()->find('clean:smarty-cache');
        $arguments = array(
            'command' => 'clean:smarty-cache',
        );

        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $this->output);
    }
}
