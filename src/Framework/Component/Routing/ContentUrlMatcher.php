<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Routing;

use Repository\EntityManager;

class ContentUrlMatcher
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Initializes the ContentUrlMatcher
     *
     * @param EntityManager $entityManager The entity manager.
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Clean id and search if exist in content table.
     *
     * @param string $type     The content type name.
     * @param string $dirtyId  The content id with date.
     * @param string $slug     The content slug.
     * @param string $category The content category name.
     *
     * @return int id in table content or forward to 404
     *
     */
    public function matchContentUrl($type, $dirtyId, $slug = null, $category = null)
    {
        if (empty($dirtyId)) {
            return false;
        }

        // Check for valid Id
        preg_match("@(?P<date>\d{14})(?P<id>\d{6,})@", $dirtyId, $matches);

        // Get real content id and date from url
        $id = $date = 0;
        if (array_key_exists('id', $matches)
            && array_key_exists('date', $matches)
            && (substr($matches['id'], 0, -6) === ''
                || substr((int) $matches['id'], 0, -6) > 0)
        ) {
            $id   = (int) $matches['id'];
            $date = $matches['date'];
        }

        // Get content from id, contentType, category, slug and date
        $now = date('Y-m-d H:i:s');
        $criteria = [
            'in_litter'         => [ [ 'value' => 0 ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'pk_content'        => [ [ 'value' => $id ] ],
            'created'           => [ [ 'value' => $date ] ],
            'content_type_name' => [ [ 'value' => $type ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => $now, 'operator' => '<' ],
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
            ],
            'endtime'           => [
                'union'   => 'OR',
                [ 'value' => $now, 'operator' => '>' ],
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
            ],
        ];

        // Check slug and category before add on criteria
        if (!is_null($slug)) {
            $criteria['slug'] = [ [ 'value' => $slug ] ];
        }

        if (!is_null($category)) {
            $criteria['category_name'] = [ [ 'value' => $category ] ];
        }

        // Fetch content if exist
        return $this->em->findOneBy($criteria, null, null, null);
    }
}
