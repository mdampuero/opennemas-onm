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
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class OnmMigratorTagsCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('migrate:Tags')
            ->setDescription('Migrate tags from content table to tags table')
            ->setHelp(
                "Migrates the content tags from the contents table to the tags table."
            )
            ->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'The names of the instances to migrate or none for all'
            );
    }


    /**
     * Executes the current command for the migration of the tags. This command
     * transform all the metadata from the content table and put them in the
     * tables contents_tags and tags
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start        = time();
        $instanceName = $input->getArgument('instance');

        if (empty($instanceName)) {
            return null;
        }
        $conn   = $this->getContainer()->get('dbal_connection');
        $logger = $this->getContainer()->get('error.log');

        $output->writeln(
            '<fg=yellow>*** Starting ONM Tags Migrator for instance ' . $instanceName . ' ***</fg=yellow>'
        );

        $instance = $this->getContainer()->get('core.loader')
            ->loadInstanceFromInternalName($instanceName);
        $conn->selectDatabase($instance->getDatabaseName());

        $ls = $this->getContainer()->get('core.locale');

        $config = $this->getContainer()->get('orm.manager')
            ->getDataSet('Settings')
            ->get('locale');

        $ls->setContext('frontend')->configure($config);

        $locale = $ls->getLocale('frontend');

        $epp        = 500;
        $page       = 0;
        $tagService = $this->getContainer()->get('api.service.tag');
        $contents   = $conn->fetchAll(
            'SELECT pk_content, metadata, fk_content_type FROM contents ORDER BY pk_content limit ? offset ?;',
            [$epp, $epp * $page]
        );
        $tags       = $this->getTags($conn, $locale);
        while (!empty($contents)) {
            $newTagsInBatch = [];
            $contentTagRel  = [];
            $contentsId     = [];
            foreach ($contents as $content) {
                if (empty($content['metadata'])) {
                    continue;
                }
                list($contentTagRel, $newTagsInBatch) = $this->getContentTags(
                    $content,
                    $tags,
                    $newTagsInBatch,
                    $contentTagRel,
                    $tagService
                );
                $contentsId[]                         = $content['pk_content'];
            }
            $tags  = $this->insertData($newTagsInBatch, $contentTagRel, $locale, $tags, $contentsId);
            $page += 1;
            unset($contents);
            $contents = $conn->fetchAll(
                'SELECT pk_content, metadata, fk_content_type FROM contents ORDER BY pk_content limit ? offset ?;',
                [$epp, $epp * $page]
            );
            unset($newTagsInBatch);
            unset($contentsId);
        }

        $end = time();

        $output->writeln(
            '<fg=yellow>*** Finished ONM Tags Migrator for instance ' .
            $instanceName .
            ' migrate ' . ($epp * $page) . ' contents' .
            ' in ' .
            ($end - $start) .
            ' ***</fg=yellow>'
        );
    }

    /**
     *  Method to get all tags for the contents
     *
     * @param Object $content        The content from where retrieve the tags
     * @param array  $tags           List with all created tags
     * @param mixed  $newTagsInBatch List of new tags in the last batch and his contents
     * @param array  $contentTagRel  List with the relations of between contents and tags
     * @param Object $tagService     Service for the tags operations
     *
     * @return array
     */
    private function getContentTags($content, $tags, $newTagsInBatch, $contentTagRel, $tagService)
    {
        $contentTags = $this->getTagsFromString($content);

        if (!empty($contentTags)) {
            foreach ($contentTags as $tag) {
                if (strlen($tag) > 60) {
                    continue;
                }

                $searcheableWord = $this->getContainer()
                    ->get('data.manager.filter')
                    ->set($tag)
                    ->filter('slug')
                    ->get();

                if (empty($searcheableWord)) {
                    continue;
                }

                if (array_key_exists($tag, $tags)) {
                    $contentTagRel[] = [ 'content_id' => $content['pk_content'], 'tag_id' => $tags[$tag]];
                    continue;
                }

                if (!array_key_exists($tag, $newTagsInBatch)) {
                    $newTagsInBatch[$tag] = ['slug' => $searcheableWord, 'contents' => []];
                } elseif (in_array($content['pk_content'], $newTagsInBatch[$tag]['contents'])) {
                    continue;
                }

                $newTagsInBatch[$tag]['contents'][] = $content['pk_content'];
            }
        }
        return [$contentTagRel, $newTagsInBatch];
    }

    /**
     * Retrieve the list of the tags from the database
     *
     * @param Object $conn   Database connection
     * @param String $locale The locale for the tag search
     *
     * @return array
     */
    private function getTags($conn, $locale)
    {
        $tagList = $conn->fetchAll('SELECT id, name FROM tags WHERE language_id = ?;', [$locale]);
        $tagsMap = [];
        foreach ($tagList as $tag) {
            $tagsMap[$tag['name']] = $tag['id'];
        }
        return $tagsMap;
    }

    /**
     * Insert new tags and content tag relations
     *
     * @param mixed  $newTagsInBatch List of new tags in the last batch and his contents
     * @param array  $contentTagRel  List with the relations of between contents and tags
     * @param String $locale         Default language for the instance
     * @param array  $tags           List with all created tags
     * @param array  $contentsId     List of all contents to insert
     *
     * @return array
     */
    private function insertData($newTagsInBatch, $contentTagRel, $locale, $tags, $contentsId)
    {
        \Content::deleteTags($contentsId);
        foreach ($newTagsInBatch as $word => $value) {
            $tag         = [
                $word,
                $locale,
                $value['slug']
            ];
            $tagId       = $this->insertTags($tag);
            $tags[$word] = $tagId;
            foreach ($value['contents'] as $contentId) {
                $contentTagRel[] = [ 'content_id' => $contentId, 'tag_id' => $tagId];
            }
        }
        try {
            \Content::saveTags($contentTagRel);
        } catch (UniqueConstraintViolationException $e) {
            /*
            * The error message is "Duplicate entry 'tag-content' for key 'PRIMARY'". We need recover the tag and
            * content.
            */
            $prevEx = $e->getPrevious();
            if (!is_null($prevEx)) {
                $errorMessage = $prevEx->getMessage();
                $errorMessage = substr($errorMessage, strpos($errorMessage, '\'') + 1);
                $errorMessage = substr($errorMessage, 0, strpos($errorMessage, '\''));
                $pkContent    = substr($errorMessage, 0, strpos($errorMessage, '-'));

                $this->insertData($newTagsInBatch, $contentTagRel, $locale, $tags, []);
            }
        }
        return $tags;
    }

    /**
     * Insert a new tag in the database
     *
     * @param array @tag List with all values for a tag
     *
     * @return Integer Return the tag id
     */
    private function insertTags($tag)
    {
        if (empty($tag)) {
            return null;
        }
        $sql  = 'INSERT INTO tags (name, language_id, slug) VALUES (?, ?, ?)';
        $conn = getService('dbal_connection');
        $conn->executeUpdate(
            $sql,
            $tag
        );
        return $conn->lastInsertId();
    }

    /**
     *  Method to retrieve tags from a string
     *
     * @param array @content String with all tags
     *
     * @return array List with all the different tags
     *
     */
    private function getTagsFromString($content)
    {
        $tagsArr   = explode(',', $content['metadata']);
        $returnArr = [];
        $aux       = null;
        $isImg     = 8 == $content['fk_content_type'];

        foreach ($tagsArr as $tagAux) {
            // Remove text between parentheses
            $aux = preg_replace("/\([^)]+\)/", "", $tagAux);
            $aux = strlen($aux) < 61 ? [$aux] : explode(' ', $aux);

            foreach ($aux as $realTag) {
                $tag = $this->fixTag($realTag, $isImg);
                if (!empty($tag)) {
                    $returnArr[] = $tag;
                }
            }
        }
        return array_unique($returnArr);
    }

    /**
     * Tags filtering to remove those invalid tags
     *
     * @param String  $realTag tag to fix
     * @param boolean $isImg if the tags belong to an image
     *
     * @return null|string The tag fixed or null if is invalid
     */
    private function fixTag($realTag, $isImg)
    {
        $realTag = trim($realTag);

        // remove words without meaning
        if (strlen(trim(\Onm\StringUtils::removeShorts($realTag))) == 0) {
            return null;
        }

        // remove number tags from the images
        if ($isImg && is_numeric($realTag)) {
            return null;
        }

        // remove tags with less than one character
        if (strlen($realTag) < 2) {
            return null;
        }

        return $realTag;
    }
}
