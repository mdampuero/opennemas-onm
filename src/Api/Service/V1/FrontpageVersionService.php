<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

use Common\ORM\Entity\FrontpageVersion;
use Api\Exception\CreateItemException;

class FrontpageVersionService extends OrmService
{
    /**
     * The number of versions to keep
     *
     * @var int
     **/
    const MAX_NUMBER_OF_VERSIONS = 10;

    /**
     * Initializes the BaseService.
     *
     * @param ServiceContainer $container The service container.
     * @param string           $entity    The entity fully qualified class name.
     * @param string           $entity    The validator service name.
     */
    public function __construct($container, $entity, $validator = null)
    {
        parent::__construct($container, $entity, $validator);

        $this->contentPositionService = $this->container->get('api.service.contentposition');
        $this->entityRepository       = $this->container->get('entity_repository');
        $this->ormManager             = $this->container->get('orm.manager');
        $this->locale                 = $this->container->get('core.locale');
        $this->dispatcher             = $this->container->get('core.dispatcher');
        $this->cache                  = $this->container->get('cache');
        $this->filterManager          = $this->container->geT('data.manager.filter');

        $this->frontpageVersionsRepository = $this->ormManager
            ->getRepository($this->entity, $this->origin);
    }

    /**
     * Checks if a given date string matches the latest version
     * for a given categoy and frontpage version ids
     *
     * @param int $categoryId the category id to search for
     * @param int $frontpageVersionId the frontpage version id
     *
     * @return boolean
     **/
    public function checkLastSaved($categoryId, $frontpageVersionId, $date)
    {
        if (empty($date)) {
            return false;
        }

        return ($this->getLastSaved($categoryId, $frontpageVersionId) != $date);
    }

    /**
     * Returns the contents positions, contents, invalidationtime and last saved time
     * for the current frontpage given a category id
     *
     * @param int $categoryId the category id to
     *
     * @return void
     **/
    public function getPublicFrontpageData($categoryId)
    {
        $categoryIdAux = empty($categoryId) ? 0 : $categoryId;

        list($frontpageVersion, $contentPositions, $contents) =
            $this->getContentsInCurrentVersionforCategory($categoryIdAux);

        $invalidationDt = $this->getInvalidationTime($contents, $categoryIdAux);
        $contents       = $this->filterPublishedContents($contents);
        $lastSaved      = $this->getLastSaved(
            $categoryIdAux,
            $frontpageVersion == null ? null : $frontpageVersion->id
        );

        return [ $contentPositions, $contents, $invalidationDt, $lastSaved ];
    }

    /**
     * Returns the contents positions, contents, invalidationtime and last saved time
     * for the current frontpage version given a category id
     *
     * @param int $categoryId the category id to
     *
     * @return void
     **/
    public function getContentsInCurrentVersionforCategory($categoryId)
    {
        $categoryIdAux      = empty($categoryId) ? 0 : $categoryId;
        $frontpageVersionId = $this->getCurrentVersionFromDB($categoryIdAux);
        $frontpageVersion   = null;

        if (!empty($frontpageVersionId)) {
            $frontpageVersion = $this->getItem($frontpageVersionId);
            $frontpageVersion = $this->changeToUTC($frontpageVersion);
        }

        list($contentPositions, $contents) =
            $this->getContentPositionsAndContents(
                $categoryIdAux,
                empty($frontpageVersionId) ? null : $frontpageVersion->id
            );

        $filteredContents = [];

        foreach ($contents as $content) {
            if ($content->content_status === 1) {
                $filteredContents[$content->id] = $content;
            }
        }

        return [$frontpageVersion, $contentPositions, $filteredContents];
    }

    /**
     * Returns the data (frontpages, vesrions, content positions, contents and vesrion id)
     * used to render a frontpage given a category id and frontpage version id
     *
     * @param int $categoryId the category id to get contents from
     * @param int $frontpageVersionId the category id to get contents from
     *
     * @return array
     **/
    public function getFrontpageData($categoryId, $frontpageVersionId)
    {
        list($frontpages, $versions) =
            $this->getFrontpageWithCategory($categoryId);

        $version = null;
        if (!is_array($versions) && frontpageVersionId != null) {
            $version = $versions;
        } elseif (empty($frontpageVersionId)) {
            $version = $this->getCurrentVersion($versions);
        } else {
            foreach ($versions as $versionAux) {
                if ($versionAux->id == $frontpageVersionId) {
                    $version = $versionAux;
                    break;
                }
            }
        }

        $versionId = empty($version) ? null : $version->id;

        list($contentPositions, $contents) =
            $this->getContentPositionsAndContents($categoryId, $versionId);

        return [$frontpages, $versions, $contentPositions, $contents, $versionId];
    }

    public function getContentPositions($categoryId, $versionId)
    {
        $contentPositions = $this->getFrontpageDataFromCache($categoryId, $versionId);

        if (empty($contentPositions)) {
            $contentPositions = $this->contentPositionService
                ->getContentPositions($categoryId, $versionId);

            $this->setFrontpageDataFromCache($categoryId, $versionId, $contentPositions);
        }

        return $contentPositions;
    }

    public function getContentIds($categoryId, $versionId, $contentType = null)
    {
        $versionIdAux = $versionId === null
            ? $this->getCurrentVersionFromDB($categoryId)
            : $versionId;

        $contentPositions = $this->getContentPositions($categoryId, $versionIdAux);
        $contentsIds      = [];
        foreach ($contentPositions as $contentpositionOfPosition) {
            foreach ($contentpositionOfPosition as $contentposition) {
                if ($contentType === null ||
                    $contentType === $contentposition->content_type
                ) {
                    $contentsIds[$contentposition->pk_fk_content] =
                        $contentposition->pk_fk_content;
                }
            }
        }
        return array_unique($contentsIds);
    }

    public function getCurrentVersion($versions)
    {
        $currentVersion = null;
        $time           = time();
        $auxTime        = null;
        foreach ($versions as $version) {
            if (empty($version->publish_date)) {
                continue;
            }

            $auxTime = $version->publish_date->getTimestamp();
            if ($time < $auxTime) {
                continue;
            }

            $currentVersion = $version;
            break;
        }

        return $currentVersion;
    }

    public function getFrontpageWithCategory($categoryId)
    {
        $categoryIdAux      = empty($categoryId) ? 0 : $categoryId;
        $ccm                = \ContentCategoryManager::get_instance();
        $categories         = $ccm->findAll();
        $catFrontpagesRel   = $this->getCatFrontpagesRel();
        $catWithFrontpage   = $this->contentPositionService->getCategoriesWithManualFrontpage();
        $frontpages         = null;
        $existMainFrontPage = array_key_exists(0, $catFrontpagesRel);
        $mainFrontpage      = [
            'id'        => 0,
            'name'      => _('Frontpage'),
            'manual'    => $existMainFrontPage
        ];

        $frontpages    = $existMainFrontPage ? [$mainFrontpage] : [];
        $frontpagesAut = !$existMainFrontPage ? [$mainFrontpage] : [];

        foreach ($categories as $category) {
            if ($category->internal_category === 0) {
                continue;
            }

            if (array_key_exists($category->id, $catFrontpagesRel)) {
                $frontpages[$category->id] = [
                    'id'           => $category->id,
                    'name'         => $category->name,
                    'frontpage_id' => $catFrontpagesRel[$category->id],
                    'manual'       => true
                ];
            } else {
                $name = $this->filterManager
                    ->set($category->title)
                    ->filter('localize')->get();

                $frontpagesAut[$category->id] = [
                    'id'     => $category->id,
                    'name'   => $name,
                    'manual' => in_array($category->id, $catWithFrontpage)
                ];
            }
        }
        $frontpages = array_merge($frontpages, $frontpagesAut);

        $oql = 'category_id = ' . $categoryIdAux . ' order by publish_date desc';

        $versions = $this->getList($oql)['items'];
        $versions = $this->changeToUTC($versions);

        return [$frontpages, $versions];
    }

    public function getCatFrontpagesRel()
    {
        return $this->frontpageVersionsRepository
            ->getCatFrontpageRel();
    }

    /**
     * Returns the id of the next frontpage version for a given category
     *
     * @param int $categoryId the category id to search for
     *
     * @return int
     **/
    public function getCurrentVersionFromDB($categoryId)
    {
        return $this->frontpageVersionsRepository
            ->getCurrentVersionForCategory($categoryId);
    }

    /**
     * Returns the id of the next frontpage version for a given category
     *
     * @param int $categoryId the category id to search for
     *
     * @return int
     **/
    public function getNextVersionForCategory($categoryId)
    {
        return $this->frontpageVersionsRepository
            ->getNextVersionForCategory($categoryId);
    }

    /**
     * Returns a frontpage version name that will be used as default value.
     *
     * It uses the current instance timezone
     *
     * @param int $timestamp the timestamp to use
     *
     * @return void
     **/
    public function getDefaultNameFV($timestamp)
    {
        $dt = new \DateTime();
        $dt->setTimezone(new \DateTimeZone($this->locale->getTimeZone()->getName()));
        $dt->setTimestamp(empty($timestamp) ? time() : $timestamp);

        return $dt->format('Y-m-d H:i');
    }

    /**
     * Saves a frontpage version given an array with its properties
     *     - frontpage_id
     *     - id
     *     - category_id
     *     - MORE PROPERTIES TO COMPLETE HERE
     *
     * @param array $frontpageVersion the frontpage data to save
     *
     * @return \Common\ORM\Entity\FrontpageVersion|null
     **/
    public function saveFrontPageVersion($frontpageVersion)
    {
        $fvc = null;
        if (empty($frontpageVersion['id'])) {
            $numberOfVersions = $this->frontpageVersionsRepository->countBy(
                "frontpage_id = '{$frontpageVersion['frontpage_id']}'"
            );

            //TODO This shouldn't be here, when the frontpage part is done it should be changed to that driver
            if (empty($frontpageVersion['frontpage_id'])) {
                $frontpage = ['name' => _('Frontpage')];
                if ($frontpageVersion['category_id'] != '0') {
                    $ccm               = \ContentCategoryManager::get_instance();
                    $category          =
                        $ccm->findById($frontpageVersion['category_id']);
                    $frontpage['name'] = $category->name;
                }
                $frontpage                        = $this->container
                    ->get('api.service.frontpage')->createItem($frontpage);
                $frontpageVersion['frontpage_id'] = $frontpage->id;
            }

            if ($numberOfVersions >= self::MAX_NUMBER_OF_VERSIONS) {
                throw new CreateItemException(
                    _('You have exceeded the version limit. Delete one to create a new one'),
                    400
                );
            }
            $frontpageVersion['created'] = new \DateTime();

            $fvc = $this->createItem($frontpageVersion);
        } else {
            $this->updateItem($frontpageVersion['id'], $frontpageVersion);
            $fvc = new FrontpageVersion($frontpageVersion);
        }

        $this->purgeCacheForCategoryIdAndVersionId($fvc->category_id, $fvc->id);

        return $fvc;
    }

    /**
     * Removes an specific version (contents and item) and invalidates its cache
     *
     * @param int $categoryId the category id to search for
     * @param int $versionId the frontpage version id
     *
     * @return void
     **/
    public function deleteVersionItem($categoryId, $versionId)
    {
        $this->contentPositionService->clearContentPositionsForHomePageOfCategory(
            $categoryId,
            $versionId
        );

        if (!empty($versionId)) {
            $this->deleteItem($versionId);
        }

        return $this->purgeCacheForCategoryIdAndVersionId($categoryId, $versionId);
    }

    /**
     * Returns the datetime string of the latest version
     * for a given category and frontpage version id
     *
     * @param int $categoryId the category id to search for
     * @param int $frontpageVersionId the frontpage version id
     *
     * @return string
     **/
    public function getLastSaved($categoryId, $frontpageVersionId)
    {
        $lastSavedCacheId = 'frontpage_last_saved_' . $categoryId;
        if (!empty($frontpageVersionId)) {
            $lastSavedCacheId .= '_' . $frontpageVersionId;
        }

        $lastSaved = $this->cache->fetch($lastSavedCacheId);
        if ($lastSaved == false) {
            $date      = new \Datetime("now");
            $lastSaved = $date->format(\DateTime::ISO8601);

            $this->cache->save($lastSavedCacheId, $dateForDB);
        }

        return $lastSaved;
    }

    /**
     * Returns from cache the list of contents for a given category and frontpage ids
     *
     * @param int $categoryId the category id to search for
     * @param int $frontpageVersionId the frontpage version id
     *
     * @return array
     **/
    private function getFrontpageDataFromCache($categoryId, $frontpageVersionId)
    {
        $cacheId = empty($frontpageVersionId) ?
            'frontpage_elements_map_' . $categoryId :
            'frontpage_elements_map_' . $categoryId . '_' . $frontpageVersionId;

        return $this->container->get('cache')->fetch($cacheId);
    }

    /**
     * Saves into cache the list of contents given a category and frontpage version id
     *
     * @param int $categoryId the category id to search for
     * @param int $frontpageVersionId the frontpage version id
     * @param int $frontpageVersion the contents of the frontpage version
     *
     * @return boolean
     **/
    private function setFrontpageDataFromCache($categoryId, $frontpageVersionId, $frontpageVersion)
    {
        $cacheId = empty($frontpageVersionId) ?
            'frontpage_elements_map_' . $categoryId :
            'frontpage_elements_map_' . $categoryId . '_' . $frontpageVersionId;

        return $this->cache->save($cacheId, $frontpageVersion);
    }

    /**
     * Invalidates the cache for a given category and frontpage version id
     *
     * @param int $categoryId the category id to search for
     * @param int $frontpageId the frontpage id
     *
     * @return boolean
     **/
    private function purgeCacheForCategoryIdAndVersionId($categoryId, $frontpageId)
    {
        $this->dispatcher->dispatch(
            'frontpage.save_position',
            [ 'category' => $categoryId, 'frontpageId' => $frontpageId ]
        );

        $lastSavedCacheId = 'frontpage_last_saved_' . $categoryId . '_' . $frontpageId;

        $date = new \Datetime("now");

        return $this->cache->save($lastSavedCacheId, $date->format(\DateTime::ISO8601));
    }

    /**
     * Changes the publish_date property to UTC on each item in a given list of frontpage versions
     *
     * @param array $versions the list of frontpage versions
     *
     * @return array
     **/
    private function changeToUTC($versions)
    {
        if (empty($versions)) {
            return $versions;
        }

        $versionsAux = $versions;
        if (!is_array($versionsAux)) {
            $versionsAux = [$versionsAux];
        }

        foreach ($versionsAux as $versionAux) {
            if (!empty($versionAux->publish_date)) {
                $versionAux->publish_date->setTimezone(new \DateTimeZone('UTC'));
            }

            $versionAux->created->setTimezone(new \DateTimeZone('UTC'));
        }
        return is_array($versions) ? $versionsAux : $versionsAux[0];
    }

    /**
     * Removes contents out of time from an array of contents
     *
     * @param array $contents the lit of contents to filter
     *
     * @return array
     **/
    private function filterPublishedContents($contents)
    {
        $systemDateTz = new \DateTime(null, $this->locale->getTimeZone());
        $systemDateTz = $systemDateTz->format('Y-m-d H:i:s');

        $filteredContents = [];
        foreach ($contents as $key => $content) {
            if (!empty($content->starttime) && $content->starttime > $systemDateTz) {
                continue;
            }

            if (!empty($content->endtime) && $content->endtime < $systemDateTz) {
                continue;
            }

            $filteredContents[$key] = $content;
        }

        return $filteredContents;
    }

    /**
     * Returns the invalidation time for a frontpage given the category id and a
     * list of contents.
     * The category id is used to check the next version invalidation time
     * The contetns are used to get the min value invalidation time form them
     *
     * So the result will be the minor datetime among them
     *
     * @param array $contents the list of contents
     * @param int $categoryId the category id
     *
     * @return \DateTime
     **/
    private function getInvalidationTime($contents, $categoryId)
    {
        $invalidationTime = null;

        $frontpageVersionId = $this->getNextVersionForCategory($categoryId);

        $systemDateTz = (new \DateTime(null, $this->locale->getTimeZone()))->format('Y-m-d H:i:s');
        if (empty($frontpageVersionId)) {
            $invalidationTime = new \DateTime(null, $this->locale->getTimeZone());
            // Add 1 year to the current timestamp
            $timestamp = $invalidationTime->getTimestamp() + 31536000;
            $invalidationTime->setTimestamp($timestamp);
            $invalidationTime = $invalidationTime->format('Y-m-d H:i:s');
        } else {
            $frontpageVersion = $this->getItem($frontpageVersionId);
            if (!empty($frontpageVersion->publish_date)) {
                $invalidationTime = $frontpageVersion->publish_date
                    ->format('Y-m-d H:i:s');
            }
        }

        foreach ($contents as $content) {
            if (!empty($content->starttime) &&
                $content->starttime > $systemDateTz &&
                $content->starttime < $invalidationTime
            ) {
                $invalidationTime = $content->starttime;
                continue;
            }

            if (!empty($content->endtime) &&
                $content->endtime > $systemDateTz &&
                $content->endtime < $invalidationTime
            ) {
                $invalidationTime = $content->endtime;
                continue;
            }
        }

        $invalidationTime = \DateTime::createFromFormat('Y-m-d H:i:s', $invalidationTime, $this->locale->getTimeZone());
        $invalidationTime->setTimeZone(new \DateTimeZone('UTC'));

        return $invalidationTime;
    }

    /**
     * Returns an array where the first element is the list of content positions
     * and the second is the list of contents for a given frontpage ()
     *
     * @param int $categoryId the category id to search for
     * @param int $versionId the frontpage version id
     *
     * @return array
     **/
    private function getContentPositionsAndContents($categoryId, $versionId)
    {
        $contentPositions = $this->getContentPositions($categoryId, $versionId);
        $contentsMap      = [];
        foreach ($contentPositions as $contentpositionOfPosition) {
            foreach ($contentpositionOfPosition as $contentposition) {
                if (array_key_exists($contentposition->pk_fk_content, $contentsMap)) {
                    continue;
                }
                $contentsMap[$contentposition->pk_fk_content] =
                    [$contentposition->content_type, $contentposition->pk_fk_content];
            }
        }
        $contentsAux = $this->entityRepository->findMulti($contentsMap);

        $contents = [];
        foreach ($contentsAux as $content) {
            $contents[$content->id] = $content;
        }

        return [$contentPositions, $contents];
    }
}
