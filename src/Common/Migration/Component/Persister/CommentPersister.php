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

use Common\ORM\Entity\Comment;

/**
 * The CommentPersister class provides methods to save a Comment from an
 * external data source.
 */
class CommentPersister extends Persister
{
    /**
     * {@inheritdoc}
     */
    public function persist($data)
    {
        $converter = $this->em->getConverter('Comment');
        $data      = $converter->objectify($data);

        unset($data['pk_content']);

        try {
            $comment = $this->find($data);
        } catch (\Exception $e) {
            $comment = new Comment($data);
            $this->em->persist($comment);
        }

        return $comment->id;
    }

    /**
     * Search for comments in target data source with the same information ready
     * to import.
     *
     * @param array $data The comment information.
     *
     * @return Comment The comment in target data source.
     */
    protected function find($data)
    {
        $oql = sprintf(
            '(author_email = "%s" and date = "%s")',
            $data['author_email'],
            $data['date'],
            'comment'
        );

        return $this->em->getRepository('Comment')->findOneBy($oql);
    }
}
