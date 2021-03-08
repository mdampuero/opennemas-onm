<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Filter\Filter;

class ExtractImageFromBodyFilter extends Filter
{
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
        $ids      = [];
        $created  = new \Datetime($created);
        $ps       = $this->container->get('api.service.photo');

        foreach ($files as $file) {
            $filepath = $basename ? $path . basename($file) : $path . $file;
            $id       = $this->checkPhotoExists($file);

            if (empty($id) && file_exists($filepath)) {
                try {
                    $photo = $ps->createItem([
                        'created'     => $created->format('Y-m-d H:i:s'),
                        'description' => $file,
                        'path_file'   => $created->format('/Y/m/d/'),
                        'title'       => $file
                    ], new \SplFileInfo($filepath), true);

                    $this->insertPhotoTranslation($photo->pk_content, $file);

                    $id = $photo->pk_content;
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (!empty($id)) {
                $ids[] = $id;
            }
        }

        return array_pop($ids);
    }

    /**
     * Checks if exists an image in the database and returns its id if exists.
     *
     * @param string $filename The photo filename.
     *
     * @return integer The photo id or null if it doesnt exists.
     */
    protected function checkPhotoExists($filename)
    {
        $conn = $this->container->get('dbal_connection');

        try {
            $photo = $conn->fetchAssoc(
                "SELECT `pk_content` FROM `contents` WHERE `content_type_name` = 'photo'"
                . " AND `title` = ?",
                [ $filename ]
            );

            return $photo['pk_content'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Insert photo data in url table.
     *
     * @param integer $id       The photo id.
     * @param string  $filename The photo original filename.
     *
     * @return mixed True if inserted or null if not.
     */
    protected function insertPhotoTranslation($id, $filename)
    {
        $this->container->get('dbal_connection')->insert('url', [
            'content_type' => 'photo',
            'source'       => $filename,
            'target'       => $id,
            'type'         => 1,
            'redirection'  => 1,
            'enabled'      => 1
        ]);
    }
}
