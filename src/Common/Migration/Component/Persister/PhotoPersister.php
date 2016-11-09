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

        unset($data['pk_photo']);

        $info                      = pathinfo($data['original_filename']);
        $data['extension']         = $info['extension'];
        $data['original_filename'] = $info['basename'];

        try {
            $photo = $this->find($data);
        } catch (\Exception $e) {
            $photo = new \Photo();

            if (!is_file($data['local_file'])) {
                return 0;
            }

            $photo->createFromLocalFile($data);
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
}
