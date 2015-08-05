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
use Symfony\Component\Process\Process;

class DeployThemesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption('skip-cleaning-caches', 'c', InputOption::VALUE_NONE, 'Skip cleaning caches'),
                )
            )
            ->setName('app:deploy:themes')
            ->setDescription('Deploys all themes to the latest version')
            ->setHelp(
                <<<EOF
The <info>app:deploy:themes</info> checks out the latest code for templates.

<info>php app/console app:deploy:themes</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->basePath = APPLICATION_PATH;
        $this->input = $input;
        $this->output = $output;

        chdir($this->basePath);

        $this->updateThemes();

        // Clean cache if required
        $skipCleaning = $input->getOption('skip-cleaning-caches');
        if (!$skipCleaning) {
            $this->cleanCache();
        }

        $this->generateDeployFile();
    }

    /**
     * Saves a file with a deploy version with the actual timestamp
     *
     * @return void
     **/
    public function generateDeployFile()
    {
        $time = time();
        $contents = "<?php define('DEPLOYED_AT', '$time');";

        file_put_contents(APPLICATION_PATH.'/.deploy.php', $contents);
    }

    /**
     * Updates the public themes
     **/
    public function updateThemes()
    {
        $this->output->writeln("<info>Updating public themes</info>");
        foreach (glob($this->basePath.'/public/themes/*') as $theme) {
            // Avoid to execute pull in admin and manager themes.
            if (basename($theme) == 'admin' || basename($theme) == 'manager') {
                continue;
            }
            chdir($theme);
            $this->output->writeln("\t* ".basename($theme));
            $output = $this->execProcess('git pull');
            $this->output->writeln("\t\t".$output);
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
        $command->run($input, $this->output);
    }

    /**
     * Executes in a shell the provided command line
     *
     * @return void
     **/
    public function execProcess($processLine)
    {
        $output = $this->output;
        $process = new Process($processLine);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write("\t<error>".$buffer. "</error>");
            } else {
                $output->write("\t".$buffer);
            }
        });
    }
}
