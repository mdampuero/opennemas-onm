<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Persister;

use \Panorama\Video as PanoramaVideo;

/**
 * The VideoPersister class provides methods to save a Video from an
 * extenal data source.
 */
class VideoPersister extends Persister
{
    /**
     * {@inheritdoc}
     */
    public function persist($data)
    {
        unset($data['pk_video']);

        try {
            $video = $this->find($data);
        } catch (\Exception $e) {
            if (array_key_exists('video_url', $data)) {
                $params = $this->em->getContainer()->getParameter('panorama');

                try {
                    $info = new PanoramaVideo($data['video_url'], $params);
                    $info = $info->getVideoDetails();

                    $data['information'] = $info;

                    $info = array_intersect_key($data, $info);
                    $data = array_merge($data, $info);
                } catch (\Exception $e) {
                }
            }

            $video = new \Video();
            $video->create($data);
        }

        return $video->pk_video;
    }

    /**
     * Search for videos in target data source with the same information ready
     * to import.
     *
     * @param array $data The video information.
     *
     * @return Video The video in target data source.
     */
    protected function find($data)
    {
        $oql = sprintf(
            '(description = "%s" or title = "%s") and content_type_name = "%s"',
            $data['title'],
            $data['description'],
            'video'
        );

        return $this->em->getRepository('Content')->findOneBy($oql);
    }
}
