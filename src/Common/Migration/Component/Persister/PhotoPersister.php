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

/**
 * The PhotoPersister class provides methods to save a Photo from an external
 * data source.
 */
class PhotoPersister extends Persister
{
    /**
     * {@inheritdoc}
     */
    public function persist($data)
    {
        if (is_array($data['local_file'])) {
            $ids = [];

            foreach ($data['local_file'] as $file) {
                $d = array_merge($data, [
                    'local_file'        => $file,
                    'original_filename' => basename($file)
                ]);

                // Concat filename to prevent collisions when searching by title
                $d['title'] = $d['title'] . ' - ' . basename($file);
                $ids[]      = $this->persist($d);
            }

            return $ids;
        }

        unset($data['pk_content']);

        $data = $this->normalize($data);

        try {
            $photo = $this->find($data);
        } catch (\Exception $e) {
            $photo = new \Photo();
            $photo->create($data);
        }

        return $photo->pk_content;
    }

    /**
     * Search for photos in target data source with the same information ready
     * to import.
     *
     * @param array $data The photo information.
     *
     * @return Photo The photo in target data source.
     */
    protected function find($data)
    {
        $oql = sprintf(
            'title = "%s" and content_type_name = "%s"',
            $data['title'],
            'photo'
        );

        return $this->em->getRepository('Content')->findOneBy($oql);
    }

    /**
     * Normalizes information for a photo.
     *
     * @param array $data The photo information.
     *
     * @return array The normalized information.
     */
    protected function normalize($data)
    {
        $date  = new \Datetime();
        $info  = pathinfo($data['original_filename']);
        $t     = gettimeofday();
        $micro = intval(substr($t['usec'], 0, 5));

        if (!empty($data['created'])) {
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data['created']);
        }

        $data['extension']         = $info['extension'];
        $data['original_filename'] = $info['basename'];
        $data['path_file']         = $date->format('/Y/m/d/');

        $data['name'] = $date->format("YmdHis"). $micro . '.' . $data['extension'];

        // Get height and width from file
        if (!array_key_exists('height', $data)
            || empty($data['height'])
            || !array_key_exists('width', $data)
            || empty($data['width'])
        ) {
            list($data['width'], $data['height']) =
                getimagesize($data['local_file']);
        }

        // Get filesize from file
        if (!array_key_exists('size', $data) || empty($data['size'])) {
            $data['size'] = round(stat($data['local_file'])['size']/1024, 2);
        }

        return $data;
    }
}
