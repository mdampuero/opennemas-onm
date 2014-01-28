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

class TranslationCoreCommand extends Command
{
    public $supportedLanguages = array('es_ES', 'gl_ES', 'pt_BR');

    public $localeFolder = 'Resources/locale';

    protected function configure()
    {
        $this
            ->setName('translation:core')
            ->setDescription('Extracts and updates the localized strings')
            ->setDefinition(
                array(
                    new InputOption('only-compile', 'oc', InputOption::VALUE_NONE, 'Only compile translations'),
                )
            )
            ->setHelp(
                <<<EOF
The <info>l10n:core:update</info> extracts all the strings from the application and
updates the gettext files.

<info>php app/console translation:core</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = APPLICATION_PATH;
        $onlyCompile = $input->getOption('only-compile');

        chdir($basePath);

        // Update onm-core
        if (!$onlyCompile) {
            $this->extractTrans($input, $output);
            $this->updateTrans($input, $output);
        }
        $this->compileTrans($input, $output);
    }

    /**
     * Extract translations from a list of modules
     *
     * @return void
     **/
    private function extractTrans($input, $output)
    {
        $output->writeln(" * Extracting strings");
        $tplFolders = array(
            'public/themes/admin/tpl',
            'public/themes/manager/tpl',
        );

        $output->writeln("\t- From admin/manager templates");
        $command =
            "tsmarty2c "
            .implode(' ', $tplFolders)
            ." > ".APP_PATH.$this->localeFolder."/extracted_strings.c 2>&1";

        echo(exec($command));

        $output->writeln("\t- From PHP files");

        $phpFiles = array(
            SRC_PATH.'*/Controller/*.php',
            SRC_PATH.'*/Resources/Menu.php',
            SITE_VENDOR_PATH.'core/*.php',
            SITE_VENDOR_PATH.'Onm/**/**/*.php',
            APP_PATH.'models/*.php',
            SITE_VENDOR_PATH.'Onm/*/*.php',
            SITE_VENDOR_PATH.'smarty/onm-plugins/*.php',
            APP_PATH.$this->localeFolder.'/extracted_strings.c'
        );

        $command =
            "xgettext "
            .implode(' ', $phpFiles)
            ." -o ".APP_PATH.$this->localeFolder."/opennemas.pot  --from-code=UTF-8 2>&1";

        $commandOutput = shell_exec($command);
    }

    /**
     * Updates the translation files
     *
     * @return void
     **/
    private function updateTrans($input, $output)
    {
        $output->writeln(" * Updating translation files");

        $translationsDir = APP_PATH.$this->localeFolder;
        foreach ($this->supportedLanguages as $language) {
            $output->writeln("\t- Language ".$language);
            $languageDir = $translationsDir.'/'.$language.'/LC_MESSAGES';
            if (!is_dir($languageDir)) {
                $output->writeln("\t\t- Creating target directory");
                mkdir($languageDir, 0770, true);
            }
            $targetFile = $languageDir."/messages.po";
            if (!file_exists($targetFile)) {
                touch($targetFile);
            }
            $command = "msgmerge -U ".$targetFile. " ".APP_PATH.$this->localeFolder."/opennemas.pot 2>&1";
            $commandOutput = shell_exec($command);
        }
    }

    /**
     * Compiles the translation files
     *
     * @return void
     **/
    private function compileTrans($input, $output)
    {
        $output->writeln(" * Compiling translation databases");

        $translationsDir = APP_PATH.$this->localeFolder;
        foreach ($this->supportedLanguages as $language) {
            $output->writeln("\t- Language ".$language);
            $languageDir = $translationsDir.'/'.$language.'/LC_MESSAGES';

            $command = "LC_ALL=en msgfmt -vf ".$languageDir. "/messages.po -o ".$languageDir. "/messages.mo 2>&1";
            $commandOutput = shell_exec($command);

            $parts = explode(', ', $commandOutput);
            foreach ($parts as $part) {
                $output->writeln("\t\t+ ".$part);
            }
        }
    }
}
