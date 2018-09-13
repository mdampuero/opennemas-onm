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
     * The list of themes.
     *
     * @var array
     */
    protected $themes = [
        'anemoi', 'base', 'basic', 'bastet', 'bragi', 'cplandora', 'cronicas',
        'dryads', 'estrelladigital', 'fivex', 'flashnews', 'forseti',
        'galatea', 'gerion', 'hathor', 'horus', 'idealgallego', 'juno',
        'kalliope', 'khepri', 'kratos', 'laregion', 'lavozdelanzarote',
        'layin', 'lrinternacional', 'marruecosnegocios', 'mihos', 'moura',
        'nemty', 'notus', 'odin', 'olympus', 'orfeo', 'prontoar', 'retrincos',
        'selket', 'sercoruna', 'simplo', 'slido', 'sobek', 'stilo',
        'tecnofisis', 'televisionlr', 'verbeia', 'vidar', 'zisa'
    ];

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
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'If set, it will install all themes'
            )
            ->addOption(
                'remote',
                'r',
                InputOption::VALUE_NONE,
                'If set, it will get the list of themes from bitbucket.org'
            )
            ->setName('themes:install')
            ->setDescription('Deploys or installs themes to the latest version')
            ->setHelp(
                <<<EOF
The <info>themes:install</info> updates or installs themes code by executing
and updates the .deploy.php file.

- Install all themes
<info>php app/console themes:install</info>

- Install all themes in bitbucket.org
<info>php app/console themes:install -r</info>

- Install a theme
<info>php app/console themes:install THEME_NAME</info>
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

        $remote = $this->input->getOption('remote');
        $theme  = $input->getArgument('theme');

        if (empty($theme) && $remote) {
            $this->auth = $this->askCredentials($input, $output);
            $output->writeln('Getting themes from <info>bitbucket</info>...');

            $this->themes = $this->getThemes();
            $output->writeln('  <info>' . count($this->themes) . ' themes found</info>');
        }

        chdir($this->basePath);

        if (!empty($theme)) {
            $this->themes = [ $theme ];
        }

        if (empty($this->themes)) {
            $this->output->writeln('<error>No themes to install</error>');
            return;
        }

        $this->output->write('Installing <info>' . count($this->themes) . '</info> themes...');
        $this->installThemes();

        $this->output->write("\nGenerating <fg=blue>deploy number</>... ");
        $this->generateDeployFile();
        $this->output->writeln('<info>DONE</info>');
    }

    /**
     * Asks Bitbucket credentials to the user.
     *
     * @return string The authentication string.
     */
    protected function askCredentials()
    {
        $helper = $this->getHelper('question');

        $this->output->writeln('Please enter your <info>bitbucket</info> credentials...');

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
        $process->run(function ($type, $buffer) use ($output, $cmd) {
            if (Process::ERR === $type) {
                if (!$output->isVerbose()) {
                    $output->write('<error>FAIL</error> ');
                }

                if ($output->isVerbose()) {
                    $output->write("\n\t<error>" . $buffer . "</error>");
                }

                return Process::ERR;
            }

            if (!$output->isVerbose()) {
                $output->write('<info>DONE</info> ');
            }

            if ($output->isVerbose()) {
                $output->write("\n\t" . $buffer);
            }

            return 0;
        });
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
            $this->output->write("\n  - Installing <fg=blue>$theme</fg>... ");

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
