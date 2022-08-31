<?php

namespace Common\Core\Component\Helper;

use Api\Exception\GetItemException;
use Api\Service\V1\CategoryService;
use Common\Core\Component\Template\Template;
use Common\Model\Entity\Instance;

/**
 * Helper class to retrieve category data.
 */
class CategoryHelper
{
    /**
     * The services container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The category service.
     *
     * @var CategoryService
     */
    protected $service;

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $tpl;

    /**
     * The url generator helper.
     *
     * @var UrlGeneratorHelper
     */
    protected $ugh;

    /**
     * Initializes the CategoryHelper.
     *
     * @param ServiceContainer   $container The service container.
     * @param Instance           $instance  The current instance.
     * @param Template           $tpl       The frontend template.
     * @param UrlGeneratorHelper $ugh       The url generator helper.
     */
    public function __construct($container, Instance $instance, Template $tpl, UrlGeneratorHelper $ugh)
    {
        $this->container = $container;
        $this->service   = $this->container->get('api.service.category');
        $this->instance  = $instance;
        $this->tpl       = $tpl;
        $this->ugh       = $ugh;
    }

    /**
     * Returns the category of the item passed as parameter.
     *
     * @param $item The item to get the category from.
     *
     * @return \Common\Model\Entity\Category The category.
     */
    public function getCategory($item = null)
    {
        $item = $item ?? $this->tpl->getValue('item');

        if (empty($item)) {
            return null;
        }

        if (!is_object($item) && is_numeric($item)) {
            try {
                return $this->service->getItem($item);
            } catch (GetItemException $e) {
                return null;
            }
        }

        if (($item instanceof \Content && !empty($item->category_id))
            || ($item instanceof \Common\Model\Entity\Content && !empty($item->categories))) {
            try {
                $category = $item instanceof \Content
                    ? $this->service->getItem($item->category_id)
                    : $this->service->getItem($item->categories[0]);

                return $category;
            } catch (\Exception $e) {
                return null;
            }
        }

        return $item instanceof \Common\Model\Entity\Category
            ? $item
            : null;
    }

    /**
     * Returns the category color for the provided item.
     *
     * @param Content $item The item to get category color for.
     *                      If not provided, the function will try to search
     *                      the item in the template.
     *
     * @return ?string The category color if present. Null otherwise.
     */
    public function getCategoryColor($item = null) : ?string
    {
        $category = $this->getCategory($item);

        return !empty($category) ? $category->color : null;
    }

    /**
     * Returns the category description for the provided item.
     *
     * @param Content $item The item to get category description for. If not
     *                      provided, the function will try to search the item in
     *                      the template.
     *
     * @return ?string The category id if present. Null otherwise.
     */
    public function getCategoryDescription($item = null) : ?string
    {
        $category = $this->getCategory($item);

        return !empty($category) ? $category->description : null;
    }

    /**
     * Returns the category id for the provided item.
     *
     * @param Content $item The item to get category id for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return ?int The category id if present. Null otherwise.
     */
    public function getCategoryId($item = null) : ?int
    {
        $category = $this->getCategory($item);

        return !empty($category) ? $category->id : null;
    }

    /**
     * Returns the path to category logo for the provided item.
     *
     * @param Content $item   The item to get logo path for. If not provided, the
     *                        function will try to search the item in the template.
     *
     * @return Content $photo The photo content for category logo. Null otherwise.
     */
    public function getCategoryLogo($item = null)
    {
        $category = $this->getCategory($item);

        if (empty($category->logo_id)) {
            return null;
        }

        try {
            $photo = $this->container->get('api.service.photo')
                ->getItem($category->logo_id);

            return $photo;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the path to category cover for the provided item.
     *
     * @param Content $item   The item to get cover path for. If not provided, the
     *                        function will try to search the item in the template.
     *
     * @return Content $photo The photo content for category cover. Null otherwise.
     */
    public function getCategoryCover($item = null)
    {
        $category = $this->getCategory($item);

        if (empty($category->cover_id)) {
            return null;
        }

        try {
            $photo = $this->container->get('api.service.photo')
                ->getItem($category->cover_id);

            return $photo;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the category name for the provided item.
     *
     * @param Content $item The item to get category name for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return ?string The category name if present. Null otherwise.
     */
    public function getCategoryName($item = null) : ?string
    {
        $category = $this->getCategory($item);

        return !empty($category) ? $category->title : null;
    }

    /**
     * Returns the category slug for the provided item.
     *
     * @param Content $item The item to get category slug for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return ?string The category slug. Null otherwise.
     */
    public function getCategorySlug($item = null) : ?string
    {
        $category = $this->getCategory($item);

        return !empty($category) ? $category->name : null;
    }


    /**
     * Returns the relative URL to the automatic frontpage of the category for the
     * provided item.
     *
     * @param Content $item The item to get URL for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return ?string The relative URL to the automatic frontpage of the category
     *                 if the category is present. Null otherwise.
     */
    public function getCategoryUrl($item = null) : ?string
    {
        $category = $this->getCategory($item);

        return !empty($category)
            ? $this->ugh->generate($category)
            : null;
    }

    /**
     * Checks if the category has a description.
     *
     * @param Content $item The item to check category description for. If not
     *                      provided, the function will try to search the item in
     *                      the template.
     *
     * @return bool True if the category has a logo. False otherwise.
     */
    public function hasCategoryDescription($item = null) : bool
    {
        return !empty($this->getCategoryDescription($item));
    }

    /**
     * Checks if the category has a logo.
     *
     * @param Content $item The item to check category logo for. If not provided,
     *                      the function will try to search the item in the
     *                      template.
     *
     * @return bool True if the category has a logo. False otherwise.
     */
    public function hasCategoryLogo($item = null) : bool
    {
        return !empty($this->getCategoryLogo($item));
    }

    /**
     * Checks if the category has a cover.
     *
     * @param Content $item The item to check category cover for. If not provided,
     *                      the function will try to search the item in the
     *                      template.
     *
     * @return bool True if the category has a cover. False otherwise.
     */
    public function hasCategoryCover($item = null) : bool
    {
        return !empty($this->getCategoryCover($item));
    }
}
