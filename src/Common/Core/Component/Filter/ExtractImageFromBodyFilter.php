<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Filter;

class ExtractImageFromBodyFilter extends Filter
{
    /**
     * Initializes the ExtractImageFromBodyFilter.
     *
     * @param ServiceContainer $container The service container.
     * @param array            $params    The filter parameters.
     */
    public function __construct($container, $params = [])
    {
        parent::__construct($container, $params);
    }

    /**
     * Import all images from a text.
     *
     * @param string $str The text string.
     *
     * @return integer The first image ID.
     */
    public function filter($str)
    {
        $separator   = $this->getParameter('separator', '@@@@');
        $imageRegexp = $this->getParameter('image-regexp', '@<img\s+[^>]*src="([^"]*)"[^>]*>@');

        if (strpos($str, $separator) === false) {
            return $str;
        }

        list($body, $created) = explode($separator, $str);

        $found = preg_match_all($imageRegexp, $body, $matches);

        // If there are no images in import just skip this
        if (!$found) {
            return null;
        }

        return $this->importPhotos($matches[1], $created);
    }

    /**
     * Imports an array of photos into database and returns the first photo id.
     *
     * @param string $files   The array of photos files.
     * @param string $created The created date.
     *
     * @return integer The first photo id.
     */
    public function importPhotos($files, $created)
    {
        $path     = $this->getParameter('path');
        $basename = $this->getParameter('basename', true);

        $ids[0] = null;
        foreach ($files as $key => $file) {
            $localFile = $basename ? $path . basename($file) : $path . $file;
            $photoID   = $this->checkPhotoExists($file);

            if (file_exists($localFile) && is_null($photoID)) {
                // Import photo
                $data = [
                    'local_file'        => $localFile,
                    'original_filename' => $file,
                    'title'             => $file,
                    'description'       => $file,
                    'created'           => $created,
                    'fk_category'       => 0,
                    'category'          => 0,
                    'category_name'     => '',
                    'metadata'          => '',
                ];

                try {
                    $photo   = new \Photo();
                    $photoID = $photo->createFromLocalFile($data);

                    $this->insertPhotoTranslation($photoID, $file);
                } catch (\Exception $e) {
                }
            }

            $ids[$key] = $photoID;
        }

        return $ids[0];
    }

    /**
     * Checks if exists an image in the database and returns its id if exists.
     *
     * @param string $fileName The photo filename.
     *
     * @return integer The photo id or null if it doesnt exists.
     */
    public function checkPhotoExists($fileName)
    {
        $conn = $this->container->get('dbal_connection');

        try {
            $photo = $conn->fetchAssoc(
                "SELECT * FROM `contents` WHERE `content_type_name` = 'photo'"
                ." AND `title`=?",
                [ $fileName ]
            );

            return $photo['pk_content'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Insert photo data in translation_ids table.
     *
     * @param integer $id The photo id.
     *
     * @return mixed True if inserted or null if not.
     */
    public function insertPhotoTranslation($id, $fileName)
    {
        $conn = $this->container->get('dbal_connection');

        try {
            $conn->insert('translation_ids', [
                'pk_content'     => $id,
                'pk_content_old' => 'none',
                'type'           => 'photo',
                'slug'           => $fileName
            ]);

            return true;
        } catch (\Exception $e) {
            return null;
        }
    }
}
