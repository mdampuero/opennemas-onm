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

class ReplaceUrlFilter extends Filter
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
        $pattern  = $this->getParameter('pattern');
        $absolute = $this->getParameter('absolute', false);
        $tokens   = [];
        $isSlug   = true;

        preg_match_all($pattern, $str, $matches);

        $tokens = array_key_exists('slug', $matches) ? $matches['slug'] : [];

        if (array_key_exists('id', $matches)) {
            $tokens = $matches['id'];
            $isSlug = false;
        }

        $tokens = array_unique($tokens);

        foreach ($tokens as $token) {
            $translation = $this->getTranslation($token, $isSlug);

            if (empty($translation)) {
                continue;
            }

            $content = $this->container->get('entity_repository')
                ->find(\classify($translation['type']), $translation['pk_content']);

            if (empty($content)) {
                continue;
            }

            $url = $this->container->get('core.helper.url_generator')
                ->setInstance(
                    $this->container->get('core.loader')->getInstance()
                )->generate($content, [ 'absolute' => $absolute ]);

            $str = str_replace($token, $url, $str);
        }

        return $str;
    }

    /**
     * Returns a translation for the current token.
     *
     * @param string  $token  The current token.
     * @param boolean $isSlug Whether the token refers to a slug or a content
     *                        id.
     *
     * @return array The translation.
     */
    protected function getTranslation($token, $isSlug = true)
    {
        $instances = $this->getParameter('instances');
        $type      = $this->getParameter('type', null);
        $id        = null;

        if (!$isSlug) {
            $id    = $token;
            $token = null;
        }

        if (empty($instances)) {
            return $this->container->get('core.redirector')
                ->getTranslation($token, $type, $id);
        }

        foreach ($instances as $instance) {
            $this->container->get('core.loader')
                ->loadInstanceFromInternalName($instance);

            $translation = $this->container->get('core.redirector')
                ->getTranslation($token, $type, $id);

            if (!empty($translation)) {
                return $translation;
            }
        }

        return null;
    }
}
