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
 * Handles REST actions for videos.
 *
 * @package WebService
 */
class Videos
{
    /*
    * @url GET /videos/id/:id
    */
    public function id($id)
    {
        $this->validateInt(func_get_args());

        $er = getService('entity_repository');

        // Load video
        $videoInt = $er->find('Video', $id);

        return $videoInt;
    }

    /*
    * @url GET /videos/category/:id
    */
    public function category($id)
    {
        $this->validateInt(func_get_args());

        $cm = new \ContentManager();
        $video =  $cm->find_by_category(
            'Video',
            $id,
            'contents.content_status=1',
            'ORDER BY created LIMIT 1'
        );

        return $video;
    }

    private function validateInt($number)
    {
        foreach ($number as $value) {
            if (!is_numeric($value)) {
                throw new RestException(400, 'parameter is not a number');
            }
            if (is_infinite($value)) {
                throw new RestException(400, 'parameter is not finite');
            }
        }
    }
}
