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
        $themes = 'anemoi base basic bastet bragi cplandora cronicas dryads estrelladigital flashnews forseti galatea gerion hathor horus idealgallego khepri kalliope laregion lavozdelanzarote lrinternacional marruecosnegocios mihos nemty odin olympus prontoar retrincos selket sercoruna simplo slido stilo sobek tecnofisis televisionlr verbeia vidar zisa';

        $themes = explode(' ', $themes);

        $this->output->writeln("<info>Installing all themes</info>");
        foreach ($themes as $themeName) {
            if (file_exists($this->basePath.'/public/themes/'.$themeName)) {
                $this->updateSpecificTheme($themeName);
            } else {
                $this->installSpecificTheme($themeName);
            }
        }

        return;
    }

    /**
     * Updates the public themes
     **/
    public function updateThemes()
    {
        $this->output->writeln("<info>Updating themes</info>");
        foreach (glob($this->basePath.'/public/themes/*') as $theme) {
            // Avoid to execute pull in admin and manager themes.
            if (basename($theme) == 'admin' || basename($theme) == 'manager') {
                continue;
            }

            $this->updateSpecificTheme(basename($theme));
        }
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

        if (!realpath($this->basePath.'/public/themes/'.$themeName)) {
            $this->installSpecificTheme($themeName);

            return;
        }

        $this->output->writeln("  <info>$themeName: Updating</info>");
        chdir($this->basePath.'/public/themes/'.$themeName);
        $this->execProcess('git pull');
        chdir($this->basePath);
    }

    /**
     * Installs a theme by its name
     *
     * @param  string $themeName The theme name that will be installed
     *
     * @return void
     **/
    public function installSpecificTheme($themeName)
    {
        $this->output->writeln("  <info>$themeName: Installing</info>");
        chdir($this->basePath.'/public/themes/');
        $this->execProcess('git clone git@bitbucket.org:opennemas/onm-theme-'.$themeName.'.git '.$themeName);
        chdir($this->basePath);
    }

    /**
     * Executes in a shell the provided command line
     *
     * @param  string $processLine the command line that will be executed
     *
     * @return void
     **/
    public function execProcess($processLine)
    {
        $output = $this->output;
        $process = new Process($processLine);
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write("\t<error>".$buffer. "</error>");
                return Process::ERR;
            } else {
                $output->write("\t".$buffer);
                return 0;
            }
        });
    }
}
