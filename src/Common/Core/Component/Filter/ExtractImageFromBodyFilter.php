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
     * Initializes the SlugFilter.
     *
     * @param ServiceContainer $container The service container.
     * @param array            $params    The filter parameters.
     */
    public function __construct($container, $params = [])
    {
        parent::__construct($container, $params);
    }

    /**
     * Converts a string to a comma-separated string of tags.
     *
     * @param string $str    The string to convert.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        $separator = $this->getParameter('separator', '@@@@');

        if (strpos($str, $separator) === false) {
            return $str;
        }

        list($body, $created) = explode($separator, $str);

        $found = preg_match('@<img\s+[^>]*src="([^"]*)"[^>]*>@', $body, $matches);

        // If there are no images in import just skip this
        if (!$found) {
            return null;
        }

        $fileName  = $matches[1];

        return $this->importPhoto($fileName, $created);
    }

    /**
     * Imports a photo into database and returns its id
     *
     * If the photo is already imported the function will directly return the
     * photo id instead of importing it again
     *
     * @param  string $fileName The photo filename
     * @param  string $created The created date
     * @return int the photo id
     **/
    public function importPhoto($fileName, $created)
    {
        $localFile = $this->getParameter('path').basename($fileName);

        if (!file_exists($localFile)) {
            return null;
        }

        $photoID = $this->checkPhotoExists($fileName);

        if ($photoID !== null) {
            return $photoID;
        } else {
            $data = [
                'local_file'        => $localFile,
                'original_filename' => $fileName,
                'title'             => $fileName,
                'description'       => $fileName,
                'created'           => $created,
                'fk_category'       => 0,
                'category'          => 0,
                'category_name'     => '',
                'metadata'          => '',
            ];

            try {
                $photo = new \Photo();
                return  $photo->createFromLocalFile($data);
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    /**
     * Checks if exists an image in the database and returns its id if exists
     *
     * @param string $fileName the photo filename
     * @return int the photo id or null if it doesnt exists
     **/
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
}
