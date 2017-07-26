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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class TranslationThemeCommand extends ContainerAwareCommand
{
    /**
     * The list of supported languages.
     *
     * @var array
     */
    public $supportedLanguages = array('es_ES', 'gl_ES', 'pt_BR');

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('uuid', InputArgument::REQUIRED, 'The theme UUID in format es.openhost.theme.name'),
                )
            )
            ->setName('translation:theme')
            ->setDescription('Extracts and updates the localized strings for a theme')
            ->setHelp(
                <<<EOF
The <info>translation:theme</info> extracts all the strings for a given theme

<info>php app/console translation:theme theme-name</info>

EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('core.loader')->init();
        $basePath = APPLICATION_PATH;
        chdir($basePath);

        $uuid = 'es.openhost.theme.' . str_replace('es.openhost.theme.', '', $input->getArgument('uuid'));

        $theme = $this->getContainer()->get('orm.manager')
            ->getRepository('Theme')->findOneBy(sprintf('uuid = "%s"', $uuid));

        if (empty($theme->text_domain)) {
            $output->writeln("<error>[FAIL] Theme $uuid has no translation support</error>");
            return;
        }

        $output->writeln("Translating <info>$uuid</> theme...");

        $this->themeFolder = 'public/' . $theme->path;

        $path  = $this->getContainer()->getParameter('core.paths.themes') . '/'
            . str_replace('es.openhost.theme.', '', $theme->uuid) . '/locale';

        $this->translationsDir    = $path;
        $this->translationsDomain = $theme->text_domain;

        if (!file_exists($this->translationsDir)) {
            $output->writeln(" * Creating the locale folder");
            mkdir($this->translationsDir, 0775, true);
        }

        // Update onm-core
        $this->extractTrans($output);
        $this->updateTrans($output);
        $this->compileTrans($output);
    }

    /**
     * Extract translations from the theme.
     *
     * @param OutputInterface $output The output interface.
     */
    private function extractTrans($output)
    {
        $output->writeln(" * Extracting strings");
        $phpBinary = trim(shell_exec('which php'));

        $tplTranslationsFile = $this->translationsDir."/strings_from_tpl.pot";
        $phpTranslationsFile = $this->translationsDir."/strings_from_php.pot";
        $finalTranslationFile = $this->translationsDir."/".$this->translationsDomain.".pot ";

        $tplFolder = [
            $this->themeFolder.'/tpl/',
        ];

        $command =
            $phpBinary.' '.APPLICATION_PATH."/bin/tsmarty2c.php -o "
            .$tplTranslationsFile.' '.implode(' ', $tplFolder);

        $output->writeln("\t- From templates");
        $commandOutput = shell_exec($command);
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln($command .'>> '.$commandOutput);
        }

        $command = 'msgattrib --no-location -o '.$tplTranslationsFile.' '.$tplTranslationsFile;
        $commandOutput = shell_exec($command);
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->output->writeln($command .'>> '.$commandOutput);
        }

        $files = glob($this->themeFolder.'/**/*.php');

        if (count($files) > 0) {
            $output->writeln("\t- From PHP files");
            $command =
                "xgettext "
                .implode(' ', $files)
                ." -o ".$phpTranslationsFile." --no-location  --from-code=UTF-8 2>&1";

            $commandOutput = shell_exec($command);
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->output->writeln($command .'>> '.$commandOutput);
            }

            $command = "msgattrib --no-location -o ".$phpTranslationsFile.' '.$phpTranslationsFile;
            $commandOutput = shell_exec($command);
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->output->writeln($command .'>> '.$commandOutput);
            }
        }

        $extractedtranslationsFiles = [];
        if (file_exists($phpTranslationsFile)) {
            $extractedtranslationsFiles []= $phpTranslationsFile;
        }

        if (file_exists($tplTranslationsFile)) {
            $extractedtranslationsFiles []= $tplTranslationsFile;
        }

        if (count($extractedtranslationsFiles)) {
            $command = "msgcat -o ".$finalTranslationFile.' '.implode(' ', $extractedtranslationsFiles);
            $commandOutput = shell_exec($command);
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $this->output->writeln($command .'>> $commandOutput');
            }
        }
    }

    /**
     * Updates the translation files for the theme.
     *
     * @param OutputInterface $output The output interface.
     */
    private function updateTrans($output)
    {
        $output->writeln(" * Updating translation files");

        foreach ($this->supportedLanguages as $language) {
            $output->writeln("\t- Language ".$language);
            $languageDir = $this->translationsDir.'/'.$language.'/LC_MESSAGES';

            if (!is_dir($languageDir)) {
                $output->writeln("\t\t- Creating target directory");
                mkdir($languageDir, 0770, true);
            }

            $targetFile = $languageDir."/".$this->translationsDomain.".po";
            if (!file_exists($targetFile)) {
                touch($targetFile);
            }
            $command = "msgmerge -U ".$targetFile. " {$this->translationsDir}/{$this->translationsDomain}.pot 2>&1";

            shell_exec($command);
        }
    }

    /**
     * Compiles the translation files for the theme.
     *
     * @param OutputInterface $output The output interface.
     */
    private function compileTrans($output)
    {
        $output->writeln(" * Compiling translation databases");

        foreach ($this->supportedLanguages as $language) {
            $output->writeln("\t- Language ".$language);
            $languageDir = $this->translationsDir.'/'.$language.'/LC_MESSAGES';
            $translationDomain = $this->translationsDomain;

            $targetFile = $languageDir."/".$this->translationsDomain.".mo";
            if (!file_exists($targetFile)) {
                touch($targetFile);
            }

            $command =
                "LC_ALL=en msgfmt -vf $languageDir/$translationDomain.po "
                ."-o $targetFile 2>&1";

            $commandOutput = shell_exec($command);

            $parts = explode(', ', $commandOutput);
            foreach ($parts as $part) {
                $output->writeln("\t\t+ ".$part);
            }
        }
    }
}
