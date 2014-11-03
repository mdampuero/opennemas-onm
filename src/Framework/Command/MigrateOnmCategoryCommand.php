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
 * testing: c-onm-cronica 27 54 33
 **/
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

use Onm\DatabaseConnection;

class MigrateOnmCategoryCommand extends ContainerAwareCommand
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
        chdir($basePath);

        $originDatabase = $input->getArgument('originDB');
        $originCategory = $input->getArgument('originCategory');
        $targetDatabase = $input->getArgument('finalDB');
        $finalCategory  = $input->getArgument('finalCategory');

        $dialog = $this->getHelperSet()->get('dialog');

        $dialog->askHiddenResponse(
            $output,
            'What is the database user password?',
            false
        );

        // Initialize internal constants for logger
        // Logger in content class when creating widgets
        define('INSTANCE_UNIQUE_NAME', 'opennemas');

        // Initialize Globals and Database
        $GLOBALS['application'] = new \Application();

        $this->targetConnection = $this->getContainer()->get('db_conn');
        $this->targetConnection->selectDatabase($targetDatabase);

        \Application::initDatabase($this->targetConnection);

        $this->originConnection = new DatabaseConnection(
            getContainerParameter('database')
        );
        $this->originConnection->selectDatabase($originDatabase);

        $_SESSION['username'] = 'script';
        $_SESSION['userid'] = 11;
        // Execute functions
        $output->writeln("\t<fg=blue;bg=white>Migrating ".$originCategory.": ".$originDatabase."->". $finalCategory."-".$targetDatabase."</fg=blue;bg=white>");
        // Migrate database
        $this->migrateImages($input, $output);
        $this->migrateArticles($input, $output);

        $output->writeln(
            "\n\t<fg=yellow;bg=white>Migration finished for Database: ".$targetDatabase."</fg=yellow;bg=white>"
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
                .' WHERE  pk_fk_content_category = \''.$input->getArgument('originCategory')
                .'\' AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                .'AND  `articles`.`pk_article` = `contents`.`pk_content` ';

        $request = $this->originConnection->Prepare($sql);
        $rs = $this->originConnection->Execute($request);

        if (!$rs) {
            $output->writeln('DB problem: '. $this->originConnection->ErrorMsg());
        } else {

            $totalRows = count($rs->getArray());
            $current = 1;

            $article = new \Article();
            while (!$rs->EOF) {

                $originalArticleID = $rs->fields['pk_article'];

                if ($this->elementIsImported($originalArticleID, 'article')) {
                    $output->writeln("[{$current}/{$totalRows}] Article $originalArticleID already imported");
                } else {
                    $category = $input->getArgument('finalCategory');
                    $data = array(
                        'title'          => $rs->fields['title'],
                        'title_int'      => $rs->fields['title_int'],
                        'category'       => $category,
                        'subtitle'       => $rs->fields['subtitle'],
                        'agency'         => $rs->fields['agency'],
                        'summary'        => $rs->fields['summary'],
                        'body'           => $rs->fields['body'],
                        'img1'           => $this->elementIsImported($rs->fields['img1'], 'image'),
                        'img1_footer'    => $rs->fields['img1_footer'],
                        'img2'           => $this->elementIsImported($rs->fields['img2'], 'image'),
                        'img2_footer'    => $rs->fields['img2_footer'],
                        'fk_video'       => $rs->fields['fk_video'],
                        'fk_video2'      => $rs->fields['fk_video2'],
                        'footer_video2'  => $rs->fields['footer_video2'],
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
                        $this->insertRefactorID($rs->fields['pk_content'], $articleID, 'article');
                        $output->writeln('-'. $articleID. ' article ok');
                    } else {
                        $output->writeln('Problem inserting article '.$originalArticleID.' - '.$data['title'] .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $output->writeln('Imported  '.$current.' articles \n');

            $rs->Close();
        }
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
                .' WHERE  pk_fk_content_category = \''.$input->getArgument('originCategory')
                .'\' AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                .' AND  `photos`.`pk_photo` = `contents`.`pk_content` ';

        $request = $this->originConnection->Prepare($sql);
        $rs = $this->originConnection->Execute($request);

        if ($rs) {
            $totalRows = $rs->_numOfRows;
            $current = 1;
            $photo = new \Photo();

            while (!$rs->EOF) {
                if ($this->elementIsImported($rs->fields['pk_content'], 'image')) {
                    $output->writeln("[{$current}/{$totalRows}] Image already imported");
                } else {
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
                        'nameCat'       => $rs->fields['category_name'],
                        'size'          => $rs->fields['size'],
                        'width'         => $rs->fields['width'],
                        'height'        => $rs->fields['height'],
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

                    if (!empty($imageID)) {
                        $this->insertRefactorID($rs->fields['pk_content'], $imageID, 'image');
                        //$this->updateFields('`available` ='.$rs->fields['available'], $rs->fields['pk_content']);
                        $output->writeln('- Image '. $imageID. ' ok');
                    } else {
                        $output->writeln('Problem inserting image '.$rs->fields['pk_content'].' - '.$rs->fields['title'] .'\n');
                    }
                }
                $current++;
                $rs->MoveNext();
            }

            $rs->Close();
        }
    }

    /**
     *  insert the correspondence between identifiers
     *
     * @return void
     **/
    protected function insertRefactorID($contentID, $newID, $type)
    {
        $sql = 'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`)
                VALUES (?, ?, ?)';
        $values = array($contentID, $newID, $type);

        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($stmt, $values);

        if (!$rss) {
            //$output->writeln('\n insertRefactorID: '.$GLOBALS['application']->conn->ErrorMsg());
        }

    }

    /**
     * Read the correspondence between identifiers
     *
     * @return void
     **/
    protected function elementIsImported($contentID, $contentType)
    {
        if (isset($contentID) && isset($contentType)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=?';

            $values = array($contentID, $contentType);

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($stmt, $values);

            if (!$rss) {
                //$output->writeln($GLOBALS['application']->conn->ErrorMsg());
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            //$output->writeln("There is imported {$contentID} - {$contentType}\n.");
        }
    }

    /**
     * update some fields in content table
     *
     * @param int $contentId the content id
     * @param string $params new values for the content table
     *
     * @return void
     **/
    protected function updateFields($contentID, $params)
    {
        if (isset($contentID) && isset($params)) {
            $sql = 'UPDATE `contents` SET {$params}  WHERE pk_content=?';
            $values = array($params, $contentID);

            $stmt = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($stmt, $values);
            if (!$rss) {
                //$output->writeln($GLOBALS['application']->conn->ErrorMsg());
            }

        } else {
            //$output->writeln("Please provide a contentID and views to update it.");
        }
    }
}
