<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

class Agency
{
    public $restler;

    /**
    * Get intance for contentManager
    * This is used in some actions at lists function
    */
    public function __construct()
    {
        $this->cm = new ContentManager();
    }

    /**
     * Get an xml with elements containig url to the NewsML content
     *
     * @param type $timeLimit the mtime limit for the last content 1 day by default
     *
     * @return $output
     */

    protected function export($until = 86400)
    {
        if ($until == 'no_limits') {
            $until = 604800;
        }

        $this->validateInt($until);

        $timeLimit = date('Y-m-d H:i:s', time() - $until);

        // Get articles by time limit
        $articles = $this->cm->find(
            'Article',
            'fk_content_type=1 AND content_status=1 AND '.
            'created >= \''.$timeLimit.'\'',
            ' ORDER BY created DESC'
        );

        $tpl = new \TemplateAdmin('admin');

        $output = $tpl->fetch('news_agency/newsml_templates/contents_list.tpl', array('articles' => $articles));

        $xml = new \XmlFormat();
        XmlFormat::$root_name = 'contents';

        $output = $xml->toArray($output);

        return $output;
    }

    /**
     * Get an newsml given a content id
     *
     * @param type $id the id of the content
     *
     * @return $output
     */
    protected function newsml($id = null)
    {
        $this->validateInt($id);

        $er = getService('entity_repository');
        $article = $er->find('Article', $id);

        if (is_null($article->id)) {
            throw new RestException(400, 'parameter is not valid');
        }

        $tpl = new \TemplateAdmin('admin');

        // Load category related information
        $article->category_name  = $article->loadCategoryName($article->id);
        $article->category_title = $article->loadCategoryTitle($article->id);

        // Add DateTime with format Y-m-d H:i:s
        $article->created_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $article->created);
        $article->updated_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $article->changed);

        $imageId = $article->img1;
        $imageInnerId = $article->img2;

        if (!empty($imageId)) {
            $image = $er->find('Photo', $imageId);
            // Load attached and related contents from array
            $article->loadFrontpageImageFromHydratedArray($image);
            // Add DateTime with format Y-m-d H:i:s
            $article->img1->created_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $article->img1->created);
            $article->img1->updated_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $article->img1->changed);
            if (!mb_check_encoding($article->img1->description)) {
                $article->img1->description = utf8_encode($article->img1->description);
            }
        }

        if (!empty($imageInnerId)) {
            $image = $er->find('Photo', $imageInnerId);
            // Load attached and related contents from array
            $article->loadInnerImageFromHydratedArray($image);
            // Add DateTime with format Y-m-d H:i:s
            $article->img2->created_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $article->img2->created);
            $article->img2->updated_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $article->img2->changed);
            if (!mb_check_encoding($article->img2->description)) {
                $article->img2->description = utf8_encode($article->img2->description);
            }
        }

        // Get author obj
        $ur = getService('user_repository');
        $article->author = $ur->find($article->fk_author);

        // Get author photo
        $authorPhoto = $er->find('Photo', $article->author->avatar_img_id);
        $article->author->photo = $authorPhoto;
        $article->author = json_encode($article->author);

        $output = $tpl->fetch(
            'news_agency/newsml_templates/base.tpl',
            array(
                'article'     => $article,
                'authorPhoto' => $authorPhoto,
                'photo'       => $article->img1,
                'photoInner'  => $article->img2,
            )
        );

        $xml = new \XmlFormat();

        $output = $xml->toArray($output);

        return $output;
    }

    /**
     * Validates a finite number
     *
     * This is used for checking the int parameters
     *
     * @param type $number the number to validate
     *
     * @return void
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
