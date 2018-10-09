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

    const MAX_NUMBER_OF_VERSIONS = 10;

    public function getPublicFrontpageData($categoryId)
    {
        $categoryIdAux = empty($categoryId) ? 0 : $categoryId;

        list($frontpageVersion, $contentPositions, $contents) =
            $this->getPublicContentsForFrontpageData($categoryIdAux);

        $invalidationDt = $this->getInvalidationTime($contents, $categoryIdAux);
        $contents       = $this->getOnlyPublishContents($contents);
        $lastSaved      = $this->getLastSaved(
            $categoryIdAux,
            $frontpageVersion == null ? null : $frontpageVersion->id
        );
        return [$contentPositions, $contents, $invalidationDt, $lastSaved];
    }

    public function getPublicContentsForFrontpageData($categoryId)
    {
        $categoryIdAux      = empty($categoryId) ? 0 : $categoryId;
        $frontpageVersionId = $this->getCurrentVersionDB($categoryIdAux);
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

    public function getInvalidationTime($contents, $categoryId)
    {
        $invalidationTime   = null;
        $frontpageVersionId = $this->getNextVerForCat($categoryId);
        $tz                 =
            $this->container->get('core.locale')->getTimeZone();
        $systemDateTz       = new \DateTime(null, $tz);
        $systemDateTz       = $systemDateTz->format('Y-m-d H:i:s');
        if (empty($frontpageVersionId)) {
            $invalidationTime = new \DateTime(null, $tz);
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

        $invalidationTime =
            \DateTime::createFromFormat('Y-m-d H:i:s', $invalidationTime, $tz);
        $invalidationTime->setTimeZone(new \DateTimeZone('UTC'));
        return $invalidationTime;
    }

    public function getOnlyPublishContents($contents)
    {
        $tz           = $this->container->get('core.locale')->getTimeZone();
        $systemDateTz = new \DateTime(null, $tz);
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

    public function getContentPositionsAndContents($categoryId, $versionId)
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
        $contentsAux =
            $this->container->get('entity_repository')->findMulti($contentsMap);

        $contents = [];
        foreach ($contentsAux as $content) {
            $contents[$content->id] = $content;
        }

        return [$contentPositions, $contents];
    }

    public function getContentPositions($categoryId, $versionId)
    {
        $contentPositions = $this->getFrontpageDataFromCache($categoryId, $versionId);

        if (is_null($contentPositions)) {
            $contentPositions =
                $this->container->get('api.service.contentposition')
                    ->getContentPositions($categoryId, $versionId);

            $this->setFrontpageDataFromCache($categoryId, $versionId, $contentPositions);
        }

        return $contentPositions;
    }

    public function getContentIds($categoryId, $versionId, $contentType = null)
    {
        $versionIdAux = $versionId === null ?
            $this->getCurrentVersionDB($categoryId) :
            $versionId;

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
        $catWithFrontpage   = $this->container
            ->get('api.service.contentposition')
            ->getCategoriesWithManualFrontpage();
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
                $name = $this->container->get('data.manager.filter')
                    ->set($category->title)->filter('localize')->get();

                $frontpagesAut[$category->id] = [
                    'id'     => $category->id,
                    'name'   => $name,
                    'manual' => in_array($category->id, $catWithFrontpage)
                ];
            }
        }
        $frontpages = array_merge($frontpages, $frontpagesAut);

        $oql = 'category_id = ' . $categoryIdAux .
            ' order by publish_date desc';

        $versions = $this->getList(
            $oql
        )['items'];

        $versions = $this->changeToUTC($versions);

        return [$frontpages, $versions];
    }

    public function getCatFrontpagesRel()
    {
        return $this->container->get('orm.manager')
            ->getRepository($this->entity, $this->origin)->getCatFrontpageRel();
    }

    public function getCurrentVersionDB($categoryId)
    {
        return $this->container->get('orm.manager')
            ->getRepository($this->entity, $this->origin)->getCurrentVerForCat($categoryId);
    }

    public function getNextVerForCat($categoryId)
    {
        return $this->container->get('orm.manager')
            ->getRepository($this->entity, $this->origin)->getNextVerForCat($categoryId);
    }

    public function getDefaultNameFV($timestamp)
    {
        $dt = new \DateTime();
        $dt->setTimezone(new \DateTimeZone($this->container->get('core.locale')
            ->getTimeZone()->getName()));
        $dt->setTimestamp(empty($timestamp) ? time() : $timestamp);
        return $dt->format('Y-m-d H:i');
    }

    public function saveFrontPageVersion($frontpageVersion)
    {
        $fvc = null;
        if (empty($frontpageVersion['id'])) {
            $repository       = $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin);
            $numberOfVersions = $repository->countBy(
                'frontpage_id = \'' . $frontpageVersion['frontpage_id'] . '\''
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
        $this->invalidationMethod($fvc->category_id, $fvc->id);
        return $fvc;
    }

    public function deleteVersionItem($categoryId, $versionId)
    {
        \ContentManager::clearContentPositionsForHomePageOfCategory(
            $categoryId,
            $versionId
        );

        if (!empty($versionId)) {
            $this->deleteItem($versionId);
        }
        $this->invalidationMethod($categoryId, $versionId);
    }

    public function getLastSaved($categoryId, $frontpageVersionId)
    {
        $lastSavedCacheId = 'frontpage_last_saved_' . $categoryId;
        if (!empty($frontpageVersionId)) {
            $lastSavedCacheId .= '_' . $frontpageVersionId;
        }

        $cache     = $this->container->get('cache');
        $lastSaved = $cache->fetch($lastSavedCacheId);
        if ($lastSaved == false) {
            // Save the actual date for
            $date      = new \Datetime("now");
            $dateForDB = $date->format(\DateTime::ISO8601);
            $cache->save($lastSavedCacheId, $dateForDB);
            $lastSaved = $dateForDB;
        }
        return $lastSaved;
    }

    public function checkLastSaved($categoryId, $frontpageVersionId, $date)
    {
        $newVersionAvailable = false;
        if (!empty($date)) {
            $lastSaved           =
                $this->getLastSaved($categoryId, $frontpageVersionId);
            $newVersionAvailable = $lastSaved != $date;
        }

        return $newVersionAvailable;
    }

    private function getFrontpageDataFromCache($categoryId, $frontpageVersionId)
    {
        $cacheId = empty($frontpageVersionId) ?
            'frontpage_elements_map_' . $categoryId :
            'frontpage_elements_map_' . $categoryId . '_' . $frontpageVersionId;

        $contents = $this->container->get('cache')->fetch($cacheId);
    }

    private function setFrontpageDataFromCache($categoryId, $frontpageVersionId, $frontpageVersion)
    {
        $cacheId = empty($versionId) ?
            'frontpage_elements_map_' . $categoryId :
            'frontpage_elements_map_' . $categoryId . '_' . $frontpageVersionId;
        $cache   = $this->container->get('cache');

        return $cache->save($cacheId, $frontpageVersion);
    }

    private function invalidationMethod($categoryId, $frontpageId)
    {
        $this->container->get('core.dispatcher')->dispatch(
            'frontpage.save_position',
            [ 'category' => $categoryId, 'frontpageId' => $frontpageId ]
        );

        $lastSavedCacheId = 'frontpage_last_saved_' . $categoryId;
        if (!empty($lastSavedCacheId)) {
            $lastSavedCacheId .= '_' . $frontpageId;
        }
        $date      = new \Datetime("now");
        $dateForDB = $date->format(\DateTime::ISO8601);
        $this->container->get('cache')->save($lastSavedCacheId, $dateForDB);
    }

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
}
