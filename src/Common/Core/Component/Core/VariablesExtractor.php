<?php

namespace Common\Core\Component\Core;

use Api\Exception\GetListException;
use Api\Exception\GetItemException;

class VariablesExtractor
{
    /**
     * Initializes the VariablesExtractor.
     *
     * @param GlobalVariables  $globals  The GlobalsVariables service.
     * @param ServiceContainer $container The service container.
     * @param Template         $template The Template service.
     */
    public function __construct($globals, $container, $template)
    {
        $this->globals   = $globals;
        $this->container = $container;
        $this->tpl       = $template;
    }

    /**
     * Get variable information based on type.
     *
     * @param String  $type  The variable type.
     *
     * @return String $value The extracted value based on type.
     */
    public function get($type)
    {
        $value  = null;
        $method = 'get' . ucfirst($type);

        if (method_exists($this, $method)) {
            $value = $this->{$method}();
        }

        return $value;
    }

    /**
     * Get the author id if exists.
     *
     * @return Integer The author id.
     */
    protected function getAuthorId()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        try {
            if (!empty($content->fk_author)) {
                $author = $this->container->get('api.service.author')
                    ->getItem($content->fk_author);
            }
        } catch (GetItemException $e) {
            return null;
        }

        return $author->id ?? null;
    }

    /**
     * Get the author name if exists.
     *
     * @return String The author name.
     */
    protected function getAuthorName()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        try {
            if (!empty($content->fk_author)) {
                $author = $this->container->get('api.service.author')
                    ->getItem($content->fk_author);
            }
        } catch (GetItemException $e) {
            return null;
        }

        return $author->name ?? null;
    }

    /**
     * Get the canonical url.
     *
     * @return Integer The canonical url.
     */
    protected function getCanonicalUrl()
    {
        return $this->tpl->getValue('o_canonical');
    }

    /**
     * Get category id if exists.
     *
     * @return Integer The category id.
     */
    protected function getCategoryId()
    {
        return $this->tpl->getValue('o_category')->id ?? null;
    }

    /**
     * Get category name if exists.
     *
     * @return String The category name.
     */
    protected function getCategoryName()
    {
        return $this->tpl->getValue('o_category')->name ?? null;
    }

    /**
     * Get the content id if exists.
     *
     * @return Integer The content id.
     */
    protected function getContentId()
    {
        return $this->tpl->getValue('o_content')->id ?? null;
    }

    /**
     * Get the device type.
     *
     * @return String The check in javascript.
     */
    protected function getDevice()
    {
        return 'device';
    }

    /**
     * Get the page extension.
     *
     * @return String The page extension.
     */
    protected function getExtension()
    {
        return $this->globals->getExtension();
    }

    /**
     * Get the page format.
     *
     * @return String The page format.
     */
    protected function getFormat()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        return preg_match('@\.amp\.html@', $request->getUri()) ? 'amp' : 'web';
    }

    /**
     * Get the host name.
     *
     * @return String The host name.
     */
    protected function getHostName()
    {
        return $this->container->get('request_stack')
            ->getCurrentRequest()
            ->getHost();
    }

    /**
     * Get the Instance name.
     *
     * @return String The Instance name.
     */
    protected function getInstanceName()
    {
        return $this->globals->getInstance()->internal_name;
    }

    /**
     * Get the Instance language.
     *
     * @return String The Instance language.
     */
    protected function getLanguage()
    {
        return $this->globals->getLocale()->getLocaleShort('frontend');
    }

    /**
     * Get the content last author Id.
     *
     * @return Integer The content last author Id.
     */
    protected function getLastAuthorId()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        try {
            if (!empty($content->fk_user_last_editor)) {
                $author = $this->container->get('api.service.author')
                    ->getItem($content->fk_user_last_editor);
            }
        } catch (GetItemException $e) {
            return null;
        }

        return $author->id ?? null;
    }

    /**
     * Get the content last author name.
     *
     * @return Integer The content last author name.
     */
    protected function getLastAuthorName()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        try {
            if (!empty($content->fk_user_last_editor)) {
                $author = $this->container->get('api.service.author')
                    ->getItem($content->fk_user_last_editor);
            }
        } catch (GetItemException $e) {
            return null;
        }

        return $author->name ?? null;
    }

    /**
     * Get the content asociated media.
     *
     * @return String The content asociated media.
     */
    protected function getMediaType()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        return get_type(get_featured_media($content, 'inner')) ?? null;
    }

    /**
     * Get the content pretitle.
     *
     * @return String The content pretitle.
     */
    protected function getPretitle()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        return $content->pretitle ?? null;
    }

    /**
     * Get the content published date.
     *
     * @return String The content published date.
     */
    protected function getPublishedDate()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        try {
            if (!$content->starttime instanceof \DateTime) {
                $content->starttime = new \DateTime($content->starttime);
            }

            return $content->starttime->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the available keywords slugs.
     *
     * @return String The Instance name.
     */
    protected function getSeoTags()
    {
        $ids = $this->tpl->getValue('o_content')->tags ?? null;

        try {
            $tags = $this->container->get('api.service.tag')->getListByIds($ids)['items'];
        } catch (GetListException $e) {
            return null;
        }

        $tags = array_map(function ($tag) {
            return $tag->slug;
        }, $tags);

        return implode(',', $tags);
    }

    /**
     * Get the content and chack if it's under subscription.
     *
     * @return Boolean True if the content is restricted.
     */
    protected function getSubscription()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        return $this->globals->getSubscription()->isRestricted($content);
    }

    /**
     * Get the available content tags.
     *
     * @return String The content tags.
     */
    protected function getTags()
    {
        $ids = $this->tpl->getValue('o_content')->tags ?? null;

        try {
            $tags = $this->container->get('api.service.tag')->getListByIds($ids)['items'];
        } catch (GetListException $e) {
            return null;
        }

        $tags = array_map(function ($tag) {
            return $tag->name;
        }, $tags);

        return implode(',', $tags);
    }

    /**
     * Get the content last update date.
     *
     * @return String The content last update date.
     */
    protected function getUpdateDate()
    {
        $content = $this->tpl->getValue('o_content') ?? null;

        if (empty($content)) {
            return null;
        }

        try {
            if (!$content->changed instanceof \DateTime) {
                $content->changed = new \DateTime($content->changed);
            }

            return $content->changed->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return null;
        }
    }
}
