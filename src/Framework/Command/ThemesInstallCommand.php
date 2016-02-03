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

class ThemesInstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->addArgument(
                'theme',
                InputArgument::OPTIONAL,
                'What theme do you want to install'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, it will install all themes'
            )
            ->setName('themes:install')
            ->setDescription('Deploys or installs themes to the latest version')
            ->setHelp(
                <<<EOF
The <info>themes:install</info> updates or installs themes code by executing
and updates the .deploy.php file.

- Update all themes already installed
<info>php app/console themes:install</info>

- Install all themes or update all themes already installed
<info>php app/console themes:install --all</info>

- Install or update an specific theme
<info>php app/console themes:install THEME_NAME</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->basePath = APPLICATION_PATH;
        $this->input = $input;
        $this->output = $output;

        chdir($this->basePath);


        // Get database name from prompt
        $themeName = $input->getArgument('theme');
        if ($themeName) {
            $this->updateSpecificTheme($themeName);
        } elseif ($this->input->getOption('all')) {
            $this->installAllThemes();
        } else {
            $this->updateThemes();
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
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function installAllThemes()
    {
        $themes = 'africainteligencia base basic bastet bragi cplandora cronicas estrelladigital flashnews forseti '
            .'hathor horus idealgallego khepri laregion lavozdelanzarote lrinternacional marruecosnegocios '
            .'nemty odin prontoar retrincos selket sercoruna sermos sobek tecnofisis televisionlr vidar zisa';

        $themes = explode(' ', $themes);

        foreach ($themes as $themeName) {
            chdir($this->basePath.'/public/themes/');
            if (file_exists($themeName)) {
                $this->updateSpecificTheme($themeName);
            } else {
                $this->execProcess('git clone ssh://gitolite@git.openhost.es:23911/onm-theme-'.$themeName.'.git '.$themeName);
            }
        }

        return;

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
            $output = exec("git pull");
            $this->output->writeln("\t\t".$output);
            chdir($this->basePath);
        }
        $this->output->writeln('');
    }

    /**
     * Updates the public themes
     **/
    public function updateSpecificTheme($themeName)
    {
        if (basename($themeName) == 'admin' || basename($themeName) == 'manager') {
            $this->output->writeln('Not valid theme names');
            return;
        }

        $this->output->writeln("<info>Updating $themeName theme</info>");
        if (!realpath($this->basePath.'/public/themes/'.$themeName)) {
            $this->output->writeln('Not available');
            chdir($this->basePath.'/public/themes/');
            $this->execProcess('git clone ssh://gitolite@git.openhost.es:23911/onm-theme-'.$themeName.'.git '.$themeName);
            chdir($this->basePath);

            return;
        }

        chdir($this->basePath.'/public/themes/'.$themeName);
        $this->execProcess('git pull');
        chdir($this->basePath);

        $this->output->writeln('');
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
