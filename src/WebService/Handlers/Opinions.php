<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

use Luracast\Restler\RestException;

/**
 * Handles REST actions for opinions.
 *
 * @package WebService
 */
class Opinions
{
    public $restler;

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

        // Get author information
        $ur              = getService('user_repository');
        $author          = $ur->find($opinion->fk_author);
        $opinion->author = $author;

        // Get author name slug
        $opinion->author_name_slug = \Onm\StringUtils::generateSlug($opinion->name);

        //Fetch the other opinions for this author
        $opinion->otherOpinions = $this->others($opinion->id);

        // Get external media url
        $opinion->externalMediaUrl = MEDIA_IMG_ABSOLUTE_URL;

        return serialize($opinion);
    }

    /*
    * @url GET /opinions/id/:id
    */
    public function id($id = null)
    {
        $this->validateInt($id);

        $opinion = getService('opinion_repository')->find('Opinion', $id);

        return $opinion;
    }

    /*
    * @url GET /opinions/authorsinhome/
    */
    public function authorsInHome()
    {
        $or = getService('opinion_repository');

        $filters = [
            'in_home'        => [ [ 'value' => 1 ] ],
            'content_status' => [ [ 'value' => 1 ] ],
        ];

        $order = [
            'position' => 'ASC',
            'starttime' => 'DESC'
        ];
        // Fetch all available opinions in home of authors
        $opinions = $or->findBy($filters, $order);

        foreach ($opinions as &$opinion) {
            $opinion->externalUri = 'ext' . get_url($opinion);
        }

        return $opinions;
    }

    /*
    * @url GET /opinions/authorsnotinhome/
    */
    public function authorsNotInHome()
    {
        $or = getService('opinion_repository');

        $filters = [
            'in_home'        => [ [ 'value' => 0 ] ],
            'content_status' => [ [ 'value' => 1 ] ],
        ];

        $order = [
            'position' => 'ASC',
            'starttime' => 'DESC'
        ];
        // Fetch all available opinions in home of authors
        $opinions = $or->findBy($filters, $order);

        foreach ($opinions as &$opinion) {
            $opinion->externalUri = 'ext' . get_url($opinion);
        }

        return $opinions;
    }

    /*
    * @url GET /opinions/authorsnotinhomepaged/:page
    */
    public function authorsNotInHomePaged($page = null)
    {
        $this->validateInt($page);

        $or = getService('opinion_repository');

        $filters = [
            'in_home'        => [ [ 'value' => 0 ] ],
            'content_status' => [ [ 'value' => 1 ] ],
        ];

        $order = [
            'starttime' => 'DESC'
        ];
        // Fetch all available opinions in home of authors
        $opinions = $or->findBy($filters, $order, 20, $page);

        foreach ($opinions as &$opinion) {
            $opinion->externalUri = 'ext' . get_url($opinion);
        }

        return $opinions;
    }

    /*
    * @url GET /opinions/allopinionsauthor/:page/:id
    */
    public function allOpinionsAuthor($page = null, $id = null)
    {
        $this->validateInt($page);

        $cm = new \ContentManager();

        $limit = ' LIMIT ' . (($page - 1) * ITEMS_PAGE ) . ', ' . (ITEMS_PAGE);

        // Get the list articles for this author
        $opinions = $cm->getOpinionArticlesWithAuthorInfo(
            'opinions.fk_author=' . $id .
            ' AND contents.content_status=1',
            'ORDER BY created DESC ' . $limit
        );

        return $opinions;
    }

    /*
    * @url GET /opinions/countauthorsnotinhome/
    */
    public function countAuthorsNotInHome()
    {
        $or = getService('opinion_repository');

        $filters = [
            'in_home'        => [ [ 'value' => 0 ] ],
            'content_status' => [ [ 'value' => 1 ] ],
        ];

        $numOpinions = $or->countBy($filters);

        return $numOpinions;
    }

    /*
    * @url GET /opinions/countauthoropinions/
    */
    public function countAuthorOpinions($id = null)
    {
        $or = getService('opinion_repository');

        $filters = [
            'in_home'        => [ [ 'value' => 0 ] ],
            'fk_author'      => [ [ 'value' => $id ] ],
            'content_status' => [ [ 'value' => 1 ] ],
        ];


        $numOpinions = $or->countBy($filters);

        return $numOpinions;
    }

    /*
    * @url GET /opinions/others/:id
    */
    public function others($id = null)
    {
        $this->validateInt($id);

        $or      = getService('opinion_repository');
        $opinion = $or->find('Opinion', $id);

        $filters                         = [];
        $filters['opinions`.`fk_author'] = [
            [ 'value' => $opinion->fk_author ]
        ];


        $filters['pk_opinion']     = [ [ 'value' => $id, 'operator' => '<>' ] ];
        $filters['content_status'] = [ [ 'value' => 1 ] ];

        $order = [
            'starttime' => 'DESC'
        ];
        // Fetch all available opinions in home of authors
        $otherOpinions = $or->findBy($filters, $order, 9);

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
