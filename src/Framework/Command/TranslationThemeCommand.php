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

class TranslationThemeCommand extends Command
{
    public $supportedLanguages = array('es_ES', 'gl_ES', 'pt_BR');

    public $translationDir = '/locale';

    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('theme', InputArgument::REQUIRED, 'theme'),
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = APPLICATION_PATH;

        chdir($basePath);

        $theme = $input->getArgument('theme');

        $this->themeFolder = 'public/themes/'.$theme;

        if (!file_exists($this->themeFolder)) {
            $output->writeln("<error>Theme $theme doesn't exists</error>");
            return;
        }

        $this->theme = include($this->themeFolder.'/init.php');

        if (!$this->theme->hasL10nSupport()) {
            $output->writeln('<error>This theme has no support for translations</error>');

            return false;
        }

        $this->translationsDir      = $this->theme->getTranslationsDir();
        $this->translationsDomain = $this->theme->getTranslationDomain();

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
     * Extract translations from a list of modules
     *
     * @return void
     **/
    private function extractTrans($output)
    {
        $output->writeln(" * Extracting strings");

        $tplFolders = array(
            $this->themeFolder.'/tpl/',
        );

        $output->writeln("\t- From templates templates");
        $command =
            APPLICATION_PATH."/bin/tsmarty2c.php -o "
            .$this->translationsDir."/".$this->translationsDomain."_tpl.pot "
            .implode(' ', $tplFolders);

        echo(exec($command));

        $command = "msgattrib --no-location -o "
            .$this->translationsDir."/".$this->translationsDomain."_tpl.pot "
            .$this->translationsDir."/".$this->translationsDomain."_tpl.pot ";

        echo(exec($command));

        $output->writeln("\t- From PHP files");

        if (is_dir($this->themeFolder.'/tpl/widgets/')) {
            $phpFiles = array(
                $this->themeFolder.'/tpl/widgets/*.php',
            );

            $command =
                "xgettext "
                .implode(' ', $phpFiles)
                ." -o ".$this->translationsDir."/".$this->translationsDomain."_php.pot  --from-code=UTF-8 2>&1";

            $commandOutput = shell_exec($command);
            echo $commandOutput;
        } else {
            touch($this->translationsDir."/".$this->translationsDomain."_php.pot");
        }

        $command = "msgcat -o ".$this->translationsDir."/".$this->translationsDomain.".pot "
            .$this->translationsDir."/".$this->translationsDomain."_tpl.pot "
            .$this->translationsDir."/".$this->translationsDomain."_php.pot";

        $commandOutput = shell_exec($command);
    }

    /**
     * Updates the translation files for the given theme
     *
     * @return void
     **/
    private function updateTrans($output)
    {
        $output->writeln(" * Updating translation files");

        $translationsDir = $this->theme->getTranslationsDir();
        foreach ($this->supportedLanguages as $language) {
            $output->writeln("\t- Language ".$language);
            $languageDir = $translationsDir.'/'.$language.'/LC_MESSAGES';

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
     * Compiles the translation files for the given theme
     *
     * @return void
     **/
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
