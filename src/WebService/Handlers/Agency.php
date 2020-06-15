<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

use Luracast\Restler\Format\XmlFormat;
use Luracast\Restler\RestException;

/**
 * Handles REST actions for news agency.
 *
 * @package WebService
 */
class Agency
{
    public $restler;

    /**
    * Get instance for contentManager
    * This is used in some actions at lists function
    */
    public function __construct()
    {
        $this->cm = new \ContentManager();
    }

    /**
     * Get an xml with elements containing url to the NewsML content
     *
     * @param int|string $until
     *
     * @return mixed
     */
    protected function export($until = 86400)
    {
        $total = null;
        $page  = null;

        if ($until == 'no_limits' || $until == '') {
            $until = 0;
            $total = 100;
            $page  = 1;
        }

        $this->validateInt($until);

        $er  = getService('entity_repository');
        $tpl = getService('view')->get('backend');

        $timeLimit = date('Y-m-d H:i:s', time() - $until);
        $criteria  = [
            'content_type_name' => [[ 'value' => 'article' ]],
            'fk_content_type'   => [[ 'value' => 1 ]],
            'content_status'    => [[ 'value' => 1 ]],
        ];

        if ($until != 0) {
            $criteria['created'] = [[ 'value' => $timeLimit, 'operator' => '>=' ]];
        }

        $articles = $er->findBy($criteria, 'created DESC', $total, $page);
        $output   = $tpl->fetch(
            'news_agency/newsml_templates/contents_list.tpl',
            [ 'articles' => $articles ]
        );

        XmlFormat::$rootName              = 'contents';
        XmlFormat::$importSettingsFromXml = true;

        $output = simplexml_load_string($output);
        $xml    = new XmlFormat();

        return $xml->read($output);
    }

    /**
     * Get an newsml given a content id.
     *
     * @param integer $id The content id.
     *
     * @return XmlFormat|null
     *
     * @throws RestException
     */
    protected function newsml($id = null)
    {
        $this->validateInt($id);

        $er      = getService('entity_repository');
        $article = $er->find('Article', $id);

        if (empty($article) || is_null($article->id)) {
            throw new RestException(400, 'parameter is not valid');
        }

        $tpl = getService('view')->get('backend');

        if (!empty($article->img1)) {
            $image[] = $er->find('Photo', $article->img1);

            // Load attached and related contents from array
            $article->loadFrontpageImageFromHydratedArray($image);

            if (!mb_check_encoding($article->img1->description)) {
                $article->img1->description = utf8_encode($article->img1->description);
            }
        }

        if (!empty($article->img2)) {
            $image[] = $er->find('Photo', $article->img2);

            // Load attached and related contents from array
            $article->loadInnerImageFromHydratedArray($image);

            if (!mb_check_encoding($article->img2->description)) {
                $article->img2->description = utf8_encode($article->img2->description);
            }
        }

        // Get author obj
        try {
            if (!empty($article->fk_author)) {
                $article->author = getService('api.service.author')
                    ->getItem($article->fk_author);

                if (!empty($article->author->avatar_img_id)) {
                    $article->author->photo = $er->find('Photo', $article->author->avatar_img_id);
                }
            }
        } catch (\Exception $e) {
            getService('application.log')->error(
                'Unable to fetch author with id '
                . $article->fk_author . ' :' . $e->getMessage()
            );
        }

        $locale = getService('core.locale')->getRequestLocale();

        $output = $tpl->fetch('news_agency/newsml_templates/base.tpl', [
            'content'     => $article,
            'photo'       => $article->img1,
            'photoInner'  => $article->img2,
            'tags'        => getService('api.service.tag')
                ->getListByIdsKeyMapped($article->tags, $locale)['items']
        ]);

        XmlFormat::$rootName              = 'NewsML';
        XmlFormat::$importSettingsFromXml = true;

        libxml_use_internal_errors(true);

        $output = simplexml_load_string($output);

        if (!empty(libxml_get_errors())) {
            getService('application.log')->warning(
                'Unable to generate XML for content with id '
                . $article->pk_article
            );

            libxml_clear_errors();

            return null;
        }

        $xml = new XmlFormat();

        return $xml->read($output);
    }

    /**
     * Validates a finite number
     *
     * This is used for checking the int parameters
     */
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
