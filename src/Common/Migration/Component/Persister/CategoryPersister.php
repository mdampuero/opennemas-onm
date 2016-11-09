<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Persister;

use Common\ORM\Entity\Category;

/**
 * The CategoryPersister class provides methods to save a Category from an
 * external data source.
 */
class CategoryPersister extends Persister
{
    /**
     * {@inheritdoc}
     */
    public function persist($data)
    {
        $converter = $this->em->getConverter('Category');
        $data      = $converter->objectify($data);

        unset($data['pk_content_category']);

        try {
            $category = $this->find($data);
        } catch (\Exception $e) {
            $category = new Category($data);
            $this->em->persist($category);
        }

        return $category->pk_content_category;
    }

    /**
     * Search for categories in target data source with the same information
     * ready to import.
     *
     * @param array $data The category information.
     *
     * @return Category The category in target data source.
     */
    protected function find($data)
    {
        $oql = sprintf(
            'name = "%s" or title = "%s"',
            $data['name'],
            $data['title']
        );

        return $this->em->getRepository('Category')->findOneBy($oql);
    }
}
