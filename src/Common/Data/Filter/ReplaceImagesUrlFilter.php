<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Filter;

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
        $pattern = $this->getParameter('pattern');
        $path    = $this->getParameter('path');

        preg_match_all($pattern, $str, $matches);

        foreach ($matches['slug'] as $slug) {
            $translation = $this->container->get('core.redirector')
                ->getTranslation($slug, 'photo');

            if ($translation) {
                $photo = $this->container->get('entity_repository')
                    ->find('Photo', $translation['pk_content']);

                $str = str_replace($slug, $path . $photo->path_img, $str);
            }
        }

        return $str;
    }
}
