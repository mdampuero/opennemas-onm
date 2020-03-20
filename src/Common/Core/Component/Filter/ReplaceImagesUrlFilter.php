<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Filter\Filter;

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
