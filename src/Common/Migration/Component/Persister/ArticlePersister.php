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

/**
 * The ArticlePersister class provides methods to save a Article from an
 * external data source.
 */
class ArticlePersister extends Persister
{
    /**
     * {@inheritdoc}
     */
    public function persist($data)
    {
        unset($data['pk_article']);

        try {
            $article = $this->find($data);
        } catch (\Exception $e) {
            $article = new \Article();
            $article->create($data);
        }

        return $article->pk_article;
    }

    /**
     * Search for articles in target data source with the same information ready
     * to import.
     *
     * @param array $data The article information.
     *
     * @return Article The article in target data source.
     */
    protected function find($data)
    {
        $oql = sprintf(
            '(description = "%s" or title = "%s") and content_type_name = "%s"',
            $data['title'],
            $data['description'],
            'article'
        );

        return $this->em->getRepository('Content')->findOneBy($oql);
    }
}
