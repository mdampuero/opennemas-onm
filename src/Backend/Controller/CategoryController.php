<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Request;

class CategoryController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'CATEGORY_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create'    => 'CATEGORY_CREATE',
        'configure' => 'CATEGORY_SETTINGS',
        'list'      => 'CATEGORY_ADMIN',
        'show'      => 'CATEGORY_UPDATE'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'category';

    /**
     * Handles the configuration for the categories manager
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function configureAction(Request $request)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        if ('POST' !== $request->getMethod()) {
            $configurations = $ds->get(['section_settings']);

            return $this->render(
                'category/config.tpl',
                ['configs'   => $configurations]
            );
        }

        try {
            $settings = $request->request->get('section_settings');
            if ($settings['allowLogo'] == 1) {
                $path = MEDIA_PATH . '/sections';
                \Onm\FilesManager::createDirectory($path);
            }

            $ds->set('section_settings', $settings);

            $type    = 'success';
            $message = _('Settings saved successfully.');
        } catch (\Exception $e) {
            $type    = 'error';
            $message = _('Unable to save the settings.');
        }

        $this->get('session')->getFlashBag()->add($type, $message);

        return $this->redirect($this->generateUrl('backend_categories_configure'));
    }
}
