<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class InstallThemesCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addArgument(
                'theme',
                InputArgument::OPTIONAL,
                'What theme do you want to install'
            )
            ->setName('themes:install')
            ->setDescription('Deploys or installs themes to the latest version')
            ->setHelp(
                <<<EOF
The <info>themes:install</> updates or installs themes code by executing
and updates the .deploy.php file.

- Install all themes
<info>php bin/console themes:install</>

- Install a theme
<info>php bin/console themes:install THEME_NAME</>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->basePath = APPLICATION_PATH;
        $this->input    = $input;
        $this->output   = $output;

        $theme = $input->getArgument('theme');

        if (empty($theme)) {
            $this->auth = $this->askCredentials();
            $output->writeln('Getting themes from <info>bitbucket</>...');

            $this->themes = $this->getThemes();
            $output->writeln('  <info>' . count($this->themes) . ' themes found</>');
        }

        chdir($this->basePath);

        if (!empty($theme)) {
            $this->themes = [ $theme ];
        }

        if (empty($this->themes)) {
            $this->output->writeln('<error>No themes to install</>');
            return;
        }

        $this->output->writeln('Installing <info>' . count($this->themes) . '</> themes...');
        $this->installThemes();

        $this->output->write("\nGenerating <fg=blue>deploy number</>... ");
        $this->generateDeployFile();
        $this->output->writeln('<info>DONE</>');
    }

    /**
     * Asks Bitbucket credentials to the user.
     *
     * @return string The authentication string.
     */
    protected function askCredentials()
    {
        $helper = $this->getHelper('question');

        $this->output->writeln('Please enter your <info>bitbucket</> credentials...');

        $question = new Question('  Username: ');
        $username = $helper->ask($this->input, $this->output, $question);

        $question = new Question('  Password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($this->input, $this->output, $question);

        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException('Invalid bitbucket credentials');
        }

        return $username . ':' . $password;
    }

    /**
     * Clones the theme.
     *
     * @param string $theme The theme name.
     */
    protected function cloneTheme($theme)
    {
        chdir($this->basePath . '/public/themes/');
        $this->execProcess('git clone git@bitbucket.org:opennemas/onm-theme-' . $theme . '.git ' . $theme);
        chdir($this->basePath);
    }

    /**
     * Executes in a shell the provided command line.
     *
     * @param string $cmd The command to execute.
     */
    protected function execProcess($cmd)
    {
        $output  = $this->output;
        $process = new Process($cmd);

        $process->setTimeout(3600);
        $process->run();

        $process->isSuccessful() ?
            $output->writeln('<info>DONE</>') :
            $output->writeln('<fg=red>FAIL</>');

        if ($output->isVerbose()) {
            $output->write($process->getOutput());
        }
    }

    /**
     * Saves a file with a deploy version with the actual timestamp
     */
    protected function generateDeployFile()
    {
        $time     = time();
        $contents = "<?php define('THEMES_DEPLOYED_AT', '$time');";

        file_put_contents(APPLICATION_PATH . '/.deploy.themes.php', $contents);
    }

    /**
     * Gets the list of repositories for themes from bitbucket.
     *
     * @param string $url The URL to get the themes from.
     *
     * @return null|array
     */
    protected function getThemes($url = 'https://api.bitbucket.org/2.0/repositories/opennemas?pagelen=100')
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->auth);

        $resp = curl_exec($ch);

        if (!$resp) {
            return;
        }

        $resp   = json_decode($resp, true);
        $themes = array_filter($resp['values'], function ($a) {
            return strpos($a['name'], 'onm-theme-') !== false
                && $a['name'] !== 'onm-theme-designer';
        });

        $themes = array_map(function ($a) {
            return str_replace('onm-theme-', '', $a['name']);
        }, $themes);

        // Get the next page
        if (array_key_exists('next', $resp)) {
            $themes = array_merge($themes, $this->getThemes($resp['next']));
        }

        return array_values($themes);
    }

    /**
     * Installs all themes in the list.
     */
    protected function installThemes()
    {
        foreach ($this->themes as $theme) {
            $this->output->write("\n  - Installing <fg=blue>$theme</>... ");

            if (file_exists($this->basePath . '/public/themes/' . $theme)) {
                $this->pullTheme($theme);
            } else {
                $this->cloneTheme($theme);
            }
        }
    }

    /**
     * Updates the theme.
     *
     * @param string $theme The theme name.
     */
    protected function pullTheme($theme)
    {
        chdir($this->basePath . '/public/themes/' . $theme);
        $this->execProcess('git pull');
        chdir($this->basePath);
    }
}
