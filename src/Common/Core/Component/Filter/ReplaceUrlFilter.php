<?php

namespace Common\Core\Component\Filter;

use Opennemas\Data\Filter\Filter;

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
        $prefix   = $this->getParameter('prefix');
        $sufix    = $this->getParameter('sufix');
        $useUrl   = $this->getParameter('useUrl');
        $instance = $this->container->get('core.instance')->internal_name;
        $tokens   = [];

        preg_match_all($pattern, $str, $matches);

        $tokens   = $matches['id'] ?? $matches['slug'] ?? [];
        $oldurls  = $matches['0'] ?? [];
        $combined = [];

        foreach ($tokens as $key => $value) {
            if (isset($oldurls[$key])) {
                $combined[$oldurls[$key]] = $value;
            }
        }

        foreach ($combined as $oldurl => $token) {
            list($translation, $foundAt) =
                $this->getTranslation($token);

            if (empty($translation)) {
                continue;
            }

            $content = $this->container->get('entity_repository')
                ->find(\classify($translation->content_type), $translation->target);

            if (empty($content)) {
                continue;
            }

            $url = $this->container->get('core.helper.url_generator')
                ->setInstance(
                    $this->container->get('core.loader.instance')->getInstance()
                )->generate($content, [
                    'absolute' => $instance !== $foundAt
                ]);

            $url = $this->container->get('core.decorator.url')->prefixUrl($url);

            $str = $useUrl
                ? str_replace($oldurl, $url, $str)
                : str_replace($prefix . $token . $sufix, $url, $str);
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
            $this->container->get('core.loader')->load($instance);

            $url = $this->container->get('core.redirector')
                ->getUrl($source, $type);

            if (!empty($url)) {
                return [ $url, $instance ];
            }
        }

        return [ null, null ];
    }
}
