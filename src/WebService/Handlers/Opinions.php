<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Luracast\Restler\RestException;

/**
 * Handles REST actions for opinions.
 *
 * @package WebService
 */
class Opinions
{
    /*
    * @url GET /opinions/complete/:id
    */
    public function complete($id = null)
    {
        $this->validateInt($id);

        $opinion = getService('content_url_matcher')
            ->matchContentUrl('opinion', $id);

        if (empty($opinion)) {
            throw new RestException(404, 'Page not found');
        }

        try {
            // Get author information
            $author          = getService('api.service.author')->getItem($opinion->fk_author);
            $opinion->author = $author;

            // Get author name slug
            $opinion->author_name_slug = \Onm\StringUtils::generateSlug($author->name);
        } catch (\Exception $e) {
        }

        //Fetch the other opinions for this author
        $opinion->otherOpinions = $this->others($opinion->id);

        return serialize($opinion);
    }

    /*
    * @url GET /opinions/id/:id
    */
    public function id($id = null)
    {
        $this->validateInt($id);

        try {
            $opinion = getService('api.service.opinion')->getItem($id);
        } catch (GetItemException $e) {
            return null;
        }

        return $opinion;
    }

    /*
    * @url GET /opinions/authorsinhome/
    */
    public function authorsInHome()
    {
        try {
            $opinions = getService('api.service.opinion')
                ->getList(
                    'content_type_name = "opinion" and in_home = 1 '
                    . 'and content_status = 1 order by starttime asc'
                )['items'];

            foreach ($opinions as &$opinion) {
                $opinion->externalUri = 'ext' . get_url($opinion);
            }
            return $opinions;
        } catch (GetListException $e) {
            return [];
        }
    }

    /*
    * @url GET /opinions/authorsnotinhome/
    */
    public function authorsNotInHome()
    {
        try {
            $opinions = getService('api.service.opinion')
                ->getList(
                    'content_type_name = "opinion" and in_home = 0 and '
                    . 'content_status = 1 order by starttime asc'
                )['items'];

            foreach ($opinions as &$opinion) {
                $opinion->externalUri = 'ext' . get_url($opinion);
            }
            return $opinions;
        } catch (GetListException $e) {
            return [];
        }
    }

    /*
    * @url GET /opinions/authorsnotinhomepaged/:page
    */
    public function authorsNotInHomePaged($page = null)
    {
        $this->validateInt($page);

        try {
            $opinions = getService('api.service.opinion')->getList(
                sprintf(
                    'content_type_name = "opinion" in_home = 0 and content_status = 1 limit %s',
                    ($page - 1) * 20 . ',' . (20)
                )
            )['items'];

            foreach ($opinions as &$opinion) {
                $opinion->externalUri = 'ext' . get_url($opinion);
            }

            return $opinions;
        } catch (GetListException $e) {
            return [];
        }
    }

    /*
    * @url GET /opinions/allopinionsauthor/:page/:id
    */
    public function allOpinionsAuthor($page = null, $id = null)
    {
        $this->validateInt($page);

        $limit = ' limit ' . (($page - 1) * ITEMS_PAGE) . ',' . (ITEMS_PAGE);

        try {
            return getService('api.service.opinion')->getList(
                sprintf(
                    'content_type_name = "opinion" and content_status = 1 ' .
                    'and in_litter = 0 and fk_author = %d order by created desc %s ',
                    $id,
                    $limit
                )
            )['items'];
        } catch (GetListException $e) {
            return [];
        }
    }

    /*
    * @url GET /opinions/countauthorsnotinhome/
    */
    public function countAuthorsNotInHome()
    {
        try {
            return getService('api.service.opinion')
                ->getList(
                    'content_type_name = "opinion" and in_home = 0 '
                    . 'and content_status = 1 order by starttime asc'
                )['total'];
        } catch (GetListException $e) {
            return 0;
        }
    }

    /*
    * @url GET /opinions/countauthoropinions/
    */
    public function countAuthorOpinions($id = null)
    {
        try {
            return getService('api.service.opinion')->getList(
                sprintf(
                    'content_type_name = "opinion" and in_home = 0 '
                    . 'and fk_author = %d and content_status = 1',
                    $id
                )
            )['total'];
        } catch (GetListException $e) {
            return 0;
        }
    }

    /*
    * @url GET /opinions/others/:id
    */
    public function others($id = null)
    {
        $this->validateInt($id);

        try {
            $opinion = getService('api.service.opinion')->getItem($id);

            $otherOpinions = getService('api.service.opinion')->getList(
                sprintf(
                    'content_type_name = "opinion" and content_status = 1 and pk_content != %d ' .
                    'and fk_author = %d order by starttime desc',
                    $id,
                    $opinion->fk_author
                )
            )['items'];
        } catch (GetListException $e) {
            return [];
        }

        foreach ($otherOpinions as &$otherOpinion) {
            $otherOpinion->author_name_slug = $opinion->author_name_slug;
            $otherOpinion->externalUri      = 'ext' . get_url($otherOpinion);
        }

        return $otherOpinions;
    }

    private function validateInt($number)
    {
        if (!is_numeric($number)) {
            throw new RestException(400, 'parameter is not a number');
        }
        if (is_infinite($number)) {
            throw new RestException(400, 'parameter is not finite');
        }
    }
}
