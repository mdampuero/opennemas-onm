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
        $repository = $this->container->get('orm.manager')->getRepository('ContentPosition');
        return $repository->getContentPositions($categoryId, $frontpageId);
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
        $repository = $this->container->get('orm.manager')
            ->getRepository('ContentPosition');
        return $repository->getCategoriesWithManualFrontpage();
    }

    /**
     * Save the content positions for elements in a given category
     *
     * @param int $categoryID the id of the category we want to save positions into
     * @param array $elements an array with the id, placeholder, position
     *
     * @return boolean, if all went good this will be true and viceversa
     */
    public function saveContentPositionsForHomePage($categoryID, $frontpageVersionId, $elements = [])
    {
        $positions   = [];
        $contentIds  = [];
        $returnValue = false;

        if (empty($elements)) {
            return $returnValue;
        }

        $conn = $this->container->get('orm.manager')->getConnection('instance');

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
            $returnValue = true;
        } catch (\Exception $e) {
            $conn->rollback();

            getService('application.log')->error(
                'User ' . getService('core.user')->username
                . ' (' . getService('core.user')->id
                . ') updated frontpage of category ' . $categoryID . ' with error message: '
                . $e->getMessage()
            );
        }

        return $returnValue;
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
        $conn = $this->container->get('orm.manager')->getConnection('instance');

        // clean actual contents for the homepage of this category
        $sql  = 'DELETE FROM content_positions WHERE ';
        $sql .= empty($frontpageVersionId) ?
            '`fk_category` = ' . $categoryID :
            '`fk_category` = ' . $categoryID . ' AND frontpage_version_id IN (' .
            $frontpageVersionId . ', 0)';
        $conn->executeUpdate($sql);

        getService('application.log')->info(
            'User ' . getService('core.user')->username
            . ' (' . getService('core.user')->id
            . ') clear contents frontpage of category ' . $categoryID
        );

        return true;
    }
}
