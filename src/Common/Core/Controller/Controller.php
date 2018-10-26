<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;

/**
 * Controller is a simple implementation of a Controller.
 *
 * It provides methods to common features needed in controllers.
 */
class Controller extends SymfonyController
{
    /**
     * Returns services from the service container.
     *
     * @param string $name The service name.
     *
     * @return mixed The service.
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }

    /**
     * Returns a rendered template.
     *
     * @param string $template   The template name.
     * @param array  $parameters An array of parameters to use in template.
     *
     * @return string The rendered template.
     */
    public function renderView($template, array $parameters = [])
    {
        $cacheId = null;

        if (array_key_exists('cache_id', $parameters)) {
            $cacheId = $parameters['cache_id'];
            unset($parameters['cache_id']);
        }

        if (!empty($parameters)) {
            $this->view->assign($parameters);
        }

        return $this->view->fetch($template, $cacheId);
    }

    /**
     * Returns information about a template
     *
     * @return array
     */
    public function getExpireDate()
    {
        $data = null;

        // If the template is cached, fetch the dates from it
        if ($this->view->caching && $this->view->cache_lifetime) {
            $templateObject = array_shift($this->view->template_objects);

            $creationDate = new \DateTime();
            $creationDate->setTimeStamp($templateObject->cached->timestamp);
            $creationDate->setTimeZone(new \DateTimeZone('UTC'));

            $expires    = $templateObject->cached->timestamp + $this->view->cache_lifetime;
            $expireDate = new \DateTime();
            $expireDate->setTimeStamp($expires);
            $expireDate->setTimeZone(new \DateTimeZone('UTC'));

            $data = [
                'creation_date' => $creationDate,
                'expire_date'   => $expireDate,
                'max_age'       => $expires - time(),
            ];
        }

        return $data;
    }

    /**
     * Renders a template.
     *
     * @param string   $view       The view name.
     * @param array    $parameters An array of parameters to use in template.
     * @param Response $response   A response object.
     *
     * @return Response A Response object.
     */
    public function render($view, array $parameters = [], Response $response = null)
    {
        if (empty($response)) {
            $response = new Response();
        }

        if (array_key_exists('xtags', $parameters)) {
            $parameters['xtags'] .= ',locale-' . $this->get('core.locale')->getRequestLocale();
        }

        $content = $this->renderView($view, $parameters);
        $response->setContent($content);

        if (array_key_exists('x-tags', $parameters)
            && (
                !array_key_exists('x-cacheable', $parameters) ||
                (array_key_exists('x-cacheable', $parameters)
                && $parameters['x-cacheable'] !== false)
            )
        ) {
            $instance = $this->get('core.instance')->internal_name;

            $response->headers->set('x-instance', $instance);

            $response->headers->set('x-tags', 'instance-' . $instance . ',' . $parameters['x-tags']);

            if (array_key_exists('x-cache-for', $parameters)
                && !empty($parameters['x-cache-for'])
            ) {
                $expires = strtotime($parameters['x-cache-for']) - time() . 's';
                $response->headers->set('x-cache-for', $expires);
            }
        }

        return $response;
    }

    /**
     * Returns the list of categories.
     *
     * @return array The list of categories.
     */
    protected function getCategories()
    {
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy(
                'internal_category in [1, 9, 7, 11]'
                . ' order by internal_category asc, title asc'
            );

        $categories = array_map(function ($a) {
            // Sometimes category is array. When create & update advertisement
            $a = $this->get('data.manager.filter')->set($a)->filter('localize', [
                'keys' => \ContentCategory::getL10nKeys(),
                'locale' => $this->getLocaleData('frontend')['default']
            ])->get();

            return [
                'id'     => (int) $a->pk_content_category,
                'name'   => $a->title,
                'type'   => $a->internal_category,
                'parent' => (int) $a->fk_content_category
            ];
        }, $categories);

        array_unshift(
            $categories,
            [ 'id' => 0, 'name' => _('Home'), 'type' => 0, 'parent' => 0 ]
        );

        return array_values($categories);
    }

    /**
     * Get the locale info needed for multiLanguage.
     *
     * @param String    $context    Locale context
     * @param Request   $request    User request.
     *
     * @return array all info related with locale information for the instance and request
     */
    protected function getLocaleData($context = null, $request = null, $translation = false)
    {
        $ls      = $this->get('core.locale');
        $context = $context === 'backend' ? $context : 'frontend';

        $locale      = null;
        $default     = $ls->getLocale($context);
        $translators = [];

        if (!empty($request)) {
            $locale = $request->query->get('locale');
        }

        if ($translation
            && $this->get('core.security')
                ->hasPermission('es.openhost.module.translation')
        ) {
            $translators = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('translators');

            if (empty($translators)) {
                $translators = [];
            }
        }

        $translators = array_map(function ($a) {
            return $a['to'];
        }, array_filter($translators, function ($a) use ($default) {
            return $a['from'] === $default;
        }));

        return [
            'locale'      => $locale,
            'default'     => $default,
            'available'   => $ls->getAvailableLocales($context),
            'translators' => array_unique($translators)
        ];
    }

    /**
     * This method load from the request the metadata fields,
     *
     * @param mixed   $data Data where load the metadata fields.
     * @param Request $postReq Request where the metadata are.
     * @param string  $type type of the extra field
     *
     * @return array
     */
    protected function loadMetaDataFields($data, $postReq, $type)
    {
        if (!$this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            return $data;
        }

        // If I don't have the extension, I don't check the settings
        $groups = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get($type);

        if (!is_array($groups)) {
            return $data;
        }

        foreach ($groups as $group) {
            foreach ($group['fields'] as $field) {
                if ($postReq->get($field['key'], null) == null) {
                    continue;
                }

                $data[$field['key']] = $postReq->get($field['key']);
            }
        }
        return $data;
    }
}
