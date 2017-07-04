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

class ReplaceImagesUrlFilter extends Filter
{
    /**
     * Converts an old image url into onm format with translation.
     *
     * @param string $str The string to filter.
     *
     * @return string The converted string.
     */
    public function filter($str)
    {
        $imgPattern = $this->getParameter('img_pattern');
        $mediaPath  = $this->getParameter('media_path');

        preg_match_all($imgPattern, $str, $matches);

        foreach ($matches[1] as $value) {
            $filename = $value;
            list($type, $id) =
                \ContentManager::getOriginalIdAndContentTypeFromSlug($filename);

            $photo = new \Photo($id);
            $photoUri = $mediaPath . $photo->path_img;
            $str = str_replace($value, $photoUri, $str);
        }

        return $str;
    }
}
