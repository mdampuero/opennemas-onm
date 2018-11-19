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
        $instance = $this->container->get('core.instance')->internal_name;
        $tokens   = [];

        preg_match_all($pattern, $str, $matches);

        $tokens = array_key_exists('slug', $matches) ? $matches['slug'] : [];

        if (array_key_exists('id', $matches)) {
            $tokens = $matches['id'];
        }

        $tokens = array_unique($tokens);

        foreach ($tokens as $token) {
            list($translation, $foundAt) =
                $this->getTranslation($token);

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
                )->generate($content, [
                    'absolute' => $instance !== $foundAt
                ]);

            $str = str_replace($token, $url, $str);
        }

        return $str;
    }

    /**
     * Returns a translation for the current token.
     *
     * @param string $source The source value.
     *
     * @return array The translation.
     */
    protected function getTranslation($source)
    {
        $instances = $this->getParameter('instances');
        $type      = $this->getParameter('type', null);

        if (empty($instances)) {
            return [
                $this->container->get('core.redirector')
                    ->getUrl($source, $type),
                $this->container->get('core.instance')->internal_name
            ];
        }

        foreach ($instances as $instance) {
            $this->container->get('core.loader')
                ->loadInstanceFromInternalName($instance);

            $url = $this->container->get('core.redirector')
                ->getUrl($source, $type);

            if (!empty($url)) {
                return [ $url, $instance ];
            }
        }

        return [ null, null ];
    }
}
