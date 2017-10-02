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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
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

            $data = array(
                'creation_date' => $creationDate,
                'expire_date'   => $expireDate,
                'max_age'       => $expires - time(),
            );
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
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if (empty($response)) {
            $response = new Response();
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
     * Get the locale info needed for multiLanguage.
     *
     * @param String    $context    Locale context
     * @param Request   $request    User request.
     *
     * @return Array all info related with locale information for the instance and request
     */
    protected function getLocaleData($context = null, $request = null, $translation = false)
    {
        $locale  = null;
        $ls      = $this->get('core.locale')->setContext(($context === 'backend') ? $context : 'frontend');
        $default = $ls->getLocale();
        if ($request != null) {
            $locale = $request->query->filter('locale', null, FILTER_SANITIZE_STRING);
        }

        $translators = (
            $translation &&
            $this->get('core.security')->hasPermission('es.openhost.module.translation')
        ) ?
            $this->get('setting_repository')->get('translators') :
            [];

        // get all automatic translators for the main language
        $translatorToMap = [];
        if (count($translators) > 0) {
            $translatorToMap = [];

            foreach ($translators as $translator) {
                if ($translator['from'] == $default) {
                    $translatorToMap[] = $translator['to'];
                }
            }
        }

        return [
            'locale'      => $locale,
            'default'     => $default,
            'all'         => $ls->getAvailableLocales(),
            'translators' => $translatorToMap
        ];
    }
}
