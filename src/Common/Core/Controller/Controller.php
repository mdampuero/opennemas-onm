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

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller is a simple implementation of a Controller.
 *
 * It provides methods to common features needed in controllers.
 */
class Controller extends SymfonyController
{
    /**
     * The extension name to work with @Security annotation when using
     * [extension] placeholder in permissions.
     *
     * @var string
     */
    protected $extension = null;

    /**
     * The list of advertisements groups per action.
     *
     * @var array
     */
    protected $groups = [];

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [];

    /**
     * The list of parameters to generate responses with.
     *
     * @var array
     */
    protected $params = [];

    /**
     * The list of advertisements positions per action.
     *
     * @var array
     */
    protected $positions = [];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = null;

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [];

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
     * Returns the permission basing on the action name.
     *
     * @param string $action The action name.
     *
     * @return mixed The permission name, if present. Null otherwise.
     */
    protected function getActionPermission($action)
    {
        return array_key_exists($action, $this->permissions) ?
            $this->permissions[$action] : null;
    }

    /**
     * Returns the controller extension.
     *
     * @return string The controller extension.
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Checks if the action can be executed basing on the extension and action
     * to execute.
     *
     * @param string $extension  The required extension.
     * @param string $permission The required permission.
     *
     * @throws AccessDeniedException If the action can not be executed.
     */
    protected function checkSecurity($extension, $permission = null)
    {
        if (!empty($extension)
            && !$this->get('core.security')->hasExtension($extension)
        ) {
            throw new AccessDeniedException();
        }

        if (!empty($permission)
            && !$this->get('core.security')->hasPermission($permission)
        ) {
            throw new AccessDeniedException();
        }
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
     * Returns the advertisement group basing on the current action.
     *
     * @param string $action The current action.
     *
     * @return string The advertisement group.
     */
    protected function getAdvertisementGroup($action)
    {
        return array_key_exists($action, $this->groups)
            ? $this->groups[$action]
            : null;
    }

    /**
     * Returns the list of positions for the current action.
     *
     * @param string $action The current action.
     *
     * @return array The list of positions.
     */
    protected function getAdvertisementPositions($action)
    {
        return array_key_exists($action, $this->positions)
            ? $this->positions[$action]
            : [];
    }

    /**
     * Returns an array with the list of positions and advertisements.
     *
     * @param Category $category The category object.
     *
     * @return array The list of positions and advertisements.
     */
    protected function getAdvertisements($category = null)
    {
        $categoryId = empty($category) ? 0 : $category->pk_content_category;
        $action     = $this->get('core.globals')->getAction();
        $group      = $this->getAdvertisementGroup($action);

        $positions = array_merge(
            $this->get('core.helper.advertisement')->getPositionsForGroup($group),
            $this->getAdvertisementPositions($group)
        );

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $categoryId);

        return [ $positions, $advertisements ];
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
     * Returns the defined route name for the provided action.
     *
     * @param string $action The action name.
     *
     * @return string The route name.
     */
    protected function getRoute($action)
    {
        $endpoint  = $this->get('core.globals')->getEndpoint();
        $extension = $this->get('core.globals')->getExtension();

        return array_key_exists($action, $this->routes)
            ? $this->routes[$action]
            : $endpoint . '_' . $extension . '_' . $action;
    }

    /**
     * Returns the path to the Smarty template.
     *
     * @param string $action The action name.
     *
     * @return string The path to the Smarty template.
     */
    protected function getTemplate($action = null)
    {
        $extension = $this->get('core.globals')->getExtension();

        return array_key_exists($action, $this->templates)
            ? $this->templates[$action]
            : "{$extension}/{$extension}.tpl";
    }
}
