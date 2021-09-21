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
     * @param string $content    The content that are trying to update.
     *
     * @throws AccessDeniedException If the action can not be executed.
     */
    protected function checkSecurity($extension, $permission = null, $content = null)
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

        if (!empty($content)
            && !$this->get('core.security')->hasPermission($permission)
            && !$content->isOwner($this->getUser()->id)
        ) {
            throw new AccessDeniedException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render($view, array $parameters = [], Response $response = null)
    {
        if (empty($response)) {
            $response = new Response();
        }

        $content = $this->view->render($view, $parameters);
        $response->setContent($content);

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
     * Loads the list of positions and advertisements on renderer service.
     *
     * @param Category $category The category object.
     * @param string   $token    The subscription token.
     */
    protected function getAdvertisements($category = null, $token = null)
    {
        if (!$this->get('core.helper.subscription')->hasAdvertisements($token)) {
            return;
        }

        $categoryId = empty($category) ? 0 : $category->id;
        $action     = $this->get('core.globals')->getAction();
        $group      = $this->getAdvertisementGroup($action);

        $positions = array_merge(
            $this->get('core.helper.advertisement')->getPositionsForGroup($group),
            $this->getAdvertisementPositions($group)
        );

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $categoryId);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
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
            ->findBy('order by name asc');

        $context = $this->get('core.locale')->getContext();
        $this->get('core.locale')->setContext('frontend');

        $categories = array_map(function ($a) {
            // Sometimes category is array. When create & update advertisement
            $a = $this->get('data.manager.filter')->set($a)->filter('localize', [
                'keys'   => $this->get('api.service.category')->getL10nKeys(),
                'locale' => $this->getLocaleData('frontend')['default']
            ])->get();

            return [
                'id'     => (int) $a->id,
                'name'   => $a->title,
                'parent' => (int) $a->parent_id
            ];
        }, $categories);

        $this->get('core.locale')->setContext($context);

        array_unshift(
            $categories,
            [ 'id' => 0, 'name' => _('Home'), 'type' => 0, 'parent' => 0 ]
        );

        return array_values($categories);
    }

    /**
     * Returns the client information for the current instance.
     *
     * @return array The client information.
     */
    protected function getClient()
    {
        if (empty($this->get('core.instance')->getClient())) {
            return null;
        }

        $client = $this->get('orm.manager')
            ->getRepository('Client')
            ->find($this->get('core.instance')->getClient());

        return $this->get('orm.manager')
            ->getConverter('Client')
            ->responsify($client);
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

        $locale = empty($locale) ? $default : $locale;

        if ($translation
            && $this->get('core.security')->hasPermission('es.openhost.module.translation')
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
    protected function getRoute($action, $params = [])
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
