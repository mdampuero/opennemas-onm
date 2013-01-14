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

// l10n: extracttrans updatepofiles compiletranslations extracttrans-backend
// updatepofiles-backend compiletranslations-backend

// extracttrans-backend:
//     @echo "Extracting backend translations";
//     @tsmarty2c $(TPL_FOLDER) > app/Backend/Resources/locale/extracted_strings.c
//     @xgettext app/Backend/Controllers/* \
//             app/Backend/Resources/Menu.php vendor/core/*.php \
//             vendor/Onm/**/**/*.php \
//             app/models/*.php \
//             public/themes/admin/**/*.php \
//             app/Backend/Resources/locale/extracted_strings.c \
//           -o app/Backend/Resources/locale/onmadmin.pot --from-code=UTF-8

// updatepofiles-backend:
//     @echo "Updating backend translations";
//     @for i in $(LINGUAS); do \
//         echo " - $$i";  \
//         msgmerge -U "app/Backend/Resources/locale/$$i/LC_MESSAGES/messages.po" \
//             'app/Backend/Resources/locale/onmadmin.pot'; \
//     done

// compiletranslations-backend:
//     @echo "Compiling backend translations";
//     @for i in $(LINGUAS); do \
//         echo " - $$i: " && \
//         msgfmt -vf "app/Backend/Resources/locale/$$i/LC_MESSAGES/messages.po" \
//             -o "app/Backend/Resources/locale/$$i/LC_MESSAGES/messages.mo"; \
//     done

// extracttrans:
//     @echo "Extracting translations";
//     @xgettext public/controllers/* \
//           -o app/Frontend/Resources/locale/onmfront.pot --from-code=UTF-8

// updatepofiles:
//     @echo "Updating translations";
//     @for i in $(LINGUAS); do \
//         echo " - $$i";  \
//         msgmerge -U "app/Frontend/Resources/locale/$$i/LC_MESSAGES/messages.po" \
//             'app/Frontend/Resources/locale/onmfront.pot'; \
//     done

// compiletranslations:
//     @echo "Compiling translations";
//     @for i in $(LINGUAS); do \
//         echo " - $$i: " && \
//         msgfmt -vf "app/Frontend/Resources/locale/$$i/LC_MESSAGES/messages.po" \
//             -o "app/Frontend/Resources/locale/$$i/LC_MESSAGES/messages.mo"; \
//     done
class L10nCommand extends Command
{

    public $supportedLanguages = array('es_ES', 'gl_ES', 'pt_BR');

    public $tplFolder = array(
        'public/themes/admin/tpl/',
        'public/themes/manager/tpl'
    );

    protected function configure()
    {
        $this
            ->setName('l10n:update')
            ->setDescription('Extracts and updates the localized strings')
            ->setHelp(
                <<<EOF
The <info>l10n:update</info> extracts all the strings from the application and
updates the gettext files.

<info>php app/console l10n:update</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = SRC_PATH;

        chdir($basePath);

        // Update onm-core
        $modules = glob($basePath.'/*');
        $output->writeln(" - Extracting translations");
        $this->extractTrans($input, $output, $modules);
        $this->updateTrans($input, $output, $modules);
        $this->compileTrans($input, $output, $modules);
    }

    /**
     * Extract translations from a list of modules
     *
     * @return void
     * @author
     **/
    private function extractTrans($input, $output, $modules)
    {
        foreach ($modules as $module) {
            $translationsDir = $module.'/Resources/locale/';
            foreach ($this->supportedLanguages as $language) {
                $languageDir = $translationsDir.'/'.$language;
                if (!is_dir($languageDir)) {
                    $output->writeln("\t- Creating directory ".$languageDir);
                    mkdir($languageDir, 0770, true);
                }
            }
        }

        $output->writeln("\t- Extracting smarty strings");
        exec("tcsmarty2c ".implode(' ', $this->tplFolder)." > src/Backend/Resources/locale/extracted_strings.c");

    }
}
