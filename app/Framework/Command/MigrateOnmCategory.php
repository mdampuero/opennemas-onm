<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Migrate one Category between two Openemas
 *
 * testing: c-onm-cronica 22 54 33
 **/
namespace Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateOnmCategory extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('originDB', InputArgument::REQUIRED, 'originDB'),
                    new InputArgument('originCategory', InputArgument::REQUIRED, 'originCategory'),
                    new InputArgument('finalDB', InputArgument::REQUIRED, 'finalDB'),
                    new InputArgument('finalCategory', InputArgument::REQUIRED, 'finalCategory'),
                )
            )
            ->setName('migrate:category')
            ->setDescription('Migrate a Category between two Openemas')
            ->setHelp(
                <<<EOF
The <info>migrate:category</info> command migrates one opennemas DB to new openenmas database.

<info>php bin/console migrate:category originDB originCategory finalDB finalCategory</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = APPLICATION_PATH;
        $phpBinPath = exec('which php');

        chdir($basePath);

        $dataBaseHost = 'localhost';
        $dataBaseType = 'mysqli';
        $dataBaseUser = 'root';

        $originDataBaseName = $input->getArgument('originDB');
        $originCategory     = $input->getArgument('originCategory');
        $dataBaseName       = $input->getArgument('finalDB');
        $finalCategory      = $input->getArgument('finalCategory');

        $dialog = $this->getHelperSet()->get('dialog');

        $validator = function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The password can not be empty');
            }
        };

        $dataBasePass = $password = $dialog->askHiddenResponse(
            $output,
            'What is the database user password?',
            false
        );

        define('BD_HOST', $dataBaseHost);
        define('BD_USER', $dataBaseUser);
        define('BD_PASS', $dataBasePass);
        define('BD_TYPE', $dataBaseType);
        define('BD_DATABASE', $dataBaseName);
        define('ORIGIN_BD_DATABASE', $originDataBaseName);

        include_once $basePath.'/app/autoload.php';
        include_once 'Application.php';

        // Initialize internal constants for logger
        // Logger in content class when creating widgets
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', 'opennemas');

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        \Application::initLogger();

        $GLOBALS['application']->connOrigin = \ADONewConnection(BD_TYPE);
        $GLOBALS['application']->connOrigin->Connect(BD_HOST, BD_USER, BD_PASS, ORIGIN_BD_DATABASE);

        $_SESSION['username'] = 'script';
        $_SESSION['userid'] = 11;
        // Execute functions
        $output->writeln("\t<fg=blue;bg=white>Migrating ".$originCategory.": ".$originDataBaseName."->". $finalCategory."-".$dataBaseName."</fg=blue;bg=white>");
        // Migrate database
        $this->migrateArticles($input, $output);
        $this->migrateImages($input, $output);


        $output->writeln(
            "\n\t<fg=yellow;bg=white>Migration finished for Database: ".$dataBaseName."</fg=yellow;bg=white>"
        );

    }

    /**
     * Read articles data and insert in new database
     *
     * @return void
     **/
    protected function migrateArticles($input, $output)
    {
        $sql = 'SELECT * FROM articles, contents, contents_categories '
                .' WHERE  pk_fk_content_category = '.$input->getArgument('originCategory')
                .' AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                .'AND  `articles`.`pk_article` = `contents`.`pk_content` ';

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);
var_dump($sql);
        if (!$rs) {
            $output->writeln('DB problem: '. $GLOBALS['application']->connOrigin->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;

            $article = new \Article();
            while (!$rs->EOF) {

                $originalArticleID = $rs->fields['pk_article'];

                $category = $input->getArgument('finalCategory');
                $data = array(
                    'title'          => $rs->fields['title'],
                    'title_int'      => $rs->fields['title_int'],
                    'category'       => $category,
                    'subtitle'       => $rs->fields['subtitle'],
                    'agency'         => $rs->fields['agency'],
                    'summary'        => $rs->fields['summary'],
                    'body'           => $rs->fields['body'],
                    'img1'           => $rs->fields['img1'],
                    'img1_footer'    => $rs->fields['img1_footer'],
                    'img2'           => $rs->fields['img2'],
                    'img2_footer'    => $rs->fields['img2_footer'],
                    'fk_video'       => $rs->fields['fk_video'],
                    'fk_video2'       => $rs->fields['fk_video2'],
                    'footer_video2'   => $rs->fields['footer_video2'],
                    'description'    => $rs->fields['description'],
                    'frontpage'      => $rs->fields['frontpage'],
                    'content_status' => $rs->fields['content_status'],
                    'available'      => $rs->fields['available'],
                    'in_home'        => $rs->fields['in_home'],
                    'position'       => $rs->fields['position'],
                    'home_pos'       => $rs->fields['home_pos'],
                    'created'        => $rs->fields['created'],
                    'changed'        => $rs->fields['changed'],
                    'starttime'      => $rs->fields['starttime'],
                    'endtime'        => $rs->fields['endtime'],
                    'views'          => $rs->fields['views'],
                    'fk_user'        => $rs->fields['fk_author'],
                    'fk_author'      => $rs->fields['fk_author'],
                    'fk_publisher'   => $rs->fields['fk_author'],
                    'slug'           => $rs->fields['slug'],
                    'metadata'       => $rs->fields['metadata'],
                    'urn_source'     => $rs->fields['urn_source'],
                    'params'         => $rs->fields['params'],
                );

                $articleID = $article->create($data);

                if (!empty($articleID)) {
                    $output->writeln('-'. $articleID. ' article ok');

                } else {
                    $output->writeln('Problem inserting article '.$originalArticleID.' - '.$title .'\n');
                }

                $current++;
                $rs->MoveNext();
            }
            $output->writeln('Imported  '.$current.' articles \n');
        }
        $rs->Close();
        return true;
    }

    /**
     * Read images data and insert this in new database
     *
     * @return void
     **/
    protected function migrateImages($input, $output)
    {
        $sql = 'SELECT * FROM photos, contents, contents_categories '
                .' WHERE  pk_fk_content_category = '.$input->getArgument('originCategory')
                .' AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                .' AND  `photos`.`pk_photo` = `contents`.`pk_content` ';

        $request = $GLOBALS['application']->connOrigin->Prepare($sql);
        $rs = $GLOBALS['application']->connOrigin->Execute($request);

        $photo = new \Photo();

        while (!$rs->EOF) {
            $imageData = array(
                'category'      => $input->getArgument('finalCategory'),
                'title'         => $rs->fields['title'],
                'name'          => $rs->fields['name'],
                'metadata'      => $rs->fields['metadata'],
                'description'   => $rs->fields['description'],
                'created'       => $rs->fields['created'],
                'starttime'     => $rs->fields['starttime'],
                'endtime'        => $rs->fields['endtime'],
                'changed'       => $rs->fields['changed'],
                'path_file'     => $rs->fields['path_file'],
                'fk_category'   => $input->getArgument('finalCategory'),
                'date'          => $rs->fields['date'],
                'size'          => $rs->fields['size'],
                'width'         => $rs->fields['width'],
                'height'        => $rs->fields['height'],
                'type_img'      => $rs->fields['type_img'],
                'media_type'    => $rs->fields['media_type'],
                'author_name'   => $rs->fields['author_name'],
                'frontpage'      => $rs->fields['frontpage'],
                'content_status' => $rs->fields['content_status'],
                'available'      => $rs->fields['available'],
                'in_home'        => $rs->fields['in_home'],
                'position'       => $rs->fields['position'],
                'home_pos'       => $rs->fields['home_pos'],
                'views'          => $rs->fields['views'],
                'fk_user'        => $rs->fields['fk_author'],
                'fk_author'      => $rs->fields['fk_author'],
                'fk_publisher'   => $rs->fields['fk_author'],
                'slug'           => $rs->fields['slug'],
                'urn_source'     => $rs->fields['urn_source'],
                'params'         => $rs->fields['params'],
            );
            $imageID = $photo->create($imageData);

            $rs->MoveNext();
        }

        $rs->Close();
    }
}