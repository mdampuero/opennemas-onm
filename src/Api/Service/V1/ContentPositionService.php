<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

class ContentPositionService extends OrmService
{
    /**
     * Initializes the BaseService.
     *
     * @param ServiceContainer $container The service container.
     * @param string           $entity    The entity fully qualified class name.
     * @param string           $entity    The validator service name.
     */
    public function __construct($container, $entity, $validator = null)
    {
        parent::__construct($container, $entity, $validator);

        $this->ormManager     = $this->container->get('orm.manager');
        $this->applicationLog = $this->container->get('application.log');
        $this->user           = $this->container->get('core.user');

        $this->contentPositionRepository = $this->ormManager
            ->getRepository('ContentPosition');
    }

    /**
     * See: Common\ORM\Entity\ContentPosition\ContentPositionRepository::getContentPositions
     *
     * Returns a list of content positions for a given category and frontpage id
     *
     * @param int $categoryId the category id to get contents from
     * @param int $frontpageVersionId the category id to get contents from
     *
     * @return array
     */
    public function getContentPositions($categoryId, $frontpageId)
    {
        return $this->contentPositionRepository
            ->getContentPositions($categoryId, $frontpageId);
    }

    /**
     * See: Common\ORM\Entity\ContentPosition\ContentPositionRepository::getCategoriesWithManualFrontpage
     *
     * Returns the list of categories that have a manual frontpage already saved
     *
     * @return array
     */
    public function getCategoriesWithManualFrontpage()
    {
        return $this->contentPositionRepository
            ->getCategoriesWithManualFrontpage();
    }

    /**
     * Save the content positions for elements in a given category
     *
     * @param int $categoryID the id of the category we want to save positions into
     * @param array $elements an array with the id, placeholder, position
     *
     * @return boolean
     */
    public function saveContentPositionsForHomePage($categoryID, $frontpageVersionId, $elements = [])
    {
        $positions  = [];
        $contentIds = [];

        if (empty($elements)) {
            return false;
        }

        $conn = $this->ormManager->getConnection('instance');

        // Foreach element setup the sql values statement part
        foreach ($elements as $element) {
            $contentIds[] = $element['id'];
            $positions[]  = [
                $conn->quote($element['id'], \PDO::PARAM_INT),
                $conn->quote($categoryID, \PDO::PARAM_INT),
                $conn->quote($element['position'], \PDO::PARAM_INT),
                $conn->quote($element['placeholder'], \PDO::PARAM_STR),
                $conn->quote($element['content_type'], \PDO::PARAM_STR),
                $conn->quote($frontpageVersionId, \PDO::PARAM_INT),
            ];
        }

        try {
            $conn->beginTransaction();

            // Clean all the contents for this category after insert the new ones
            $this->clearContentPositionsForHomePageOfCategory($categoryID, $frontpageVersionId, $conn);

            // construct the final sql statement and execute it
            $stmt = 'INSERT INTO content_positions (pk_fk_content, fk_category,'
                  . ' position, placeholder, content_type, frontpage_version_id) '
                  . 'VALUES ';

            foreach ($positions as $position) {
                $stmt .= '(' . implode(',', $position) . '),';
            }

            $stmt = trim($stmt, ',');

            $conn->executeUpdate($stmt);

            // Unset suggested flag if saving content positions in frontpage
            if ($categoryID == 0) {
                \ContentManager::dropSuggestedFlagFromContentIdsArray($contentIds, $conn);
            }

            $conn->commit();

            return true;
        } catch (\Exception $e) {
            $conn->rollback();

            $this->applicationLog->error(sprintf(
                'User %d (%s) updated frontpage of category %s with error message: %s',
                $this->user->username,
                $this->user->id,
                $categoryID,
                $e->getMessage()
            ));

            return false;
        }
    }

    /**
    * Clear the content positions for elements in a given category
    *
    * @param int $categoryID the id of the category we want
    *                        to clear positions from
    * @return boolean if all went good this will be true and viceversa
    */
    public function clearContentPositionsForHomePageOfCategory($categoryID, $frontpageVersionId, $conn = false)
    {
        $conn = $this->ormManager->getConnection('instance');

        // clean actual contents for the homepage of this category
        $sql  = 'DELETE FROM content_positions WHERE ';
        $sql .= empty($frontpageVersionId) ?
            '`fk_category` = ' . $categoryID :
            '`fk_category` = ' . $categoryID . ' AND frontpage_version_id IN (' .
            $frontpageVersionId . ', 0)';

        $conn->executeUpdate($sql);

        $this->applicationLog->info(sprintf(
            'User %s (%d) clear contents frontpage of category %d',
            $this->user->username,
            $this->user->id,
            $categoryID
        ));

        return true;
    }
}
