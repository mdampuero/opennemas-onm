<?php
/**
 * Handles common actions for contents.
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles common actions for contents.
 *
 * @package Backend_Controllers
 **/
class ContentController extends Controller
{
    /**
     * Sets the available state for a content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function setAvailableAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id', null);

        if ($id > 0) {
            $content = new \Content($id);
            if ($content->id != null) {
                $content->setAvailable();
                $code = 200;
                $message = 'Done';
            } else {
                $code = 404;
                $message = 'Content not available';
            }
        } else {
            $code = 400;
            $message = 'Please specify an content id';
        }

        return new Response(
            $message,
            $code,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Sets the draft state for a content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function setDraftAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id', null);

        if ($id > 0) {
            $content = new \Content($id);
            if ($content->id != null) {
                $content->setDraft();

                // Drop from any frontpage
                $content->dropFromAllHomePages();

                $code = 200;
                $message = 'Done';
            } else {
                $code = 404;
                $message = 'Content not available';
            }
        } else {
            $code = 400;
            $message = 'Please specify an content id';
        }

        return new Response(
            $message,
            $code,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Sets the draft state for a content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleAvailableAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id', null);

        if ($id > 0) {
            $content = new \Content($id);
            if ($content->id != null) {
                $availability = ($content->content_status == 1);
                $content->toggleAvailable();

                if (!$availability) {
                    // Drop from any frontpage
                    $content->dropFromAllHomePages();
                }

                $code = 200;
                $message = 'Done';
            } else {
                $code = 404;
                $message = 'Content not available';
            }
        } else {
            $code = 400;
            $message = 'Please specify an content id';
        }

        return new Response(
            $message,
            $code,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Archives contents given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function setArchivedAction(Request $request)
    {
        $ids = $request->query->get('ids');

        $code = 200;
        $error = array();

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $id = (int) $id;
                $content = new \Content($id);

                if ($content->id !== null) {
                    try {
                        $content->setArchived();
                    } catch (\Exception $e) {
                        $error []= sprintf(_('Unable to arquive content with id %s: %s'), $id, $e->getMessage());
                    }
                    $content->dropFromAllHomePages();
                } else {
                    $error []= sprintf('Content with id %s no valid', $id);
                }
            }
        } else {
            $error []= 'Please specify the ids to set the archive flag on.';
        }

        if (count($error) > 0) {
            $code = 400;
        } else {
            $error = _('Contents arquived');
        }

        return new Response(
            json_encode($error),
            $code,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Toggles the suggested state for contents given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toggleSuggestedAction(Request $request)
    {
        $ids = $request->query->get('ids');

        $code = 200;
        $error = array();

        if (is_array($ids)) {
            foreach ($ids as $id) {
                $id = (int) $id;
                $content = new \Content($id);

                if ($content->id !== null) {
                    try {
                        $content->toggleSuggested();
                    } catch (\Exception $e) {
                        $error []= sprintf(
                            _('Unable to set suggested to frontpage state to content with id %s: %s'),
                            $id,
                            $e->getMessage()
                        );
                    }
                } else {
                    $error []= sprintf('Content id «%s» is no valid', $id);
                }
            }
        } else {
            $error []= 'Please specify the ids to set the archive flag on.';
        }

        if (count($error) > 0) {
            $code = 400;
        } else {
            $error = 'Done';
        }

        return new Response(
            json_encode($error),
            $code,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Returns basic information from one content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function quickInfoAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id', null);

        if ($id > 0) {
            $content = new \Content($id);
            if ($content->id != null) {
                $code = 200;
                $message = $content->getQuickInfo();
            } else {
                $code = 404;
                $error = 'Content does not exists';
                $message = array('error' => $error);
            }
        } else {
            $code = 400;
            $message = 'Please specify an content id';
        }

        return new Response(
            json_encode($message),
            $code,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Sends to trash one content given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function sendToTrashAction(Request $request)
    {
        $id = (int) $request->query->getDigits('id', null);

        if ($id > 0) {
            $content = new \Content($id);
            if ($content->id != null) {
                $code = 200;
                if ($content->setTrashed()) {
                    $content->dropFromAllHomePages();
                    $message = 'Done';
                } else {
                    $message = sprintf('Unable to set trashed state to content with id %s', $id);
                }

            } else {
                $code = 404;
                $message = sprintf(_('Content not available'), $id);
            }
        } else {
            $code = 400;
            $message = 'Please specify an content id';
        }

        return new Response(
            json_encode($message),
            $code,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * Changed background color
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updatePropertyAction(Request $request)
    {
        $id = (int) $request->request->getDigits('id', null);

        if ($id > 0) {
            $content = new \Content($id);
            $properties = $request->request->get('properties', null);

            if ($content->id != null && $properties != null) {
                foreach ($properties as $name => $value) {
                    if (!empty($value)) {
                        $content->setProperty($name, $value);
                    } else {
                        $content->clearProperty($name);
                    }
                }
                $code = 200;
                $message = "Done {$id}:". serialize($properties)." \n";
            } else {
                $code = 404;
                $message = sprintf(_('Content or property not valid'), $id);
            }
        } else {
            $code = 400;
            $message = 'Please specify an content id';
        }

        return new Response(
            json_encode($message),
            $code,
            array('Content-Type' => 'application/json')
        );
    }
}
