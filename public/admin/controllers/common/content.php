<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

// Fetching HTTP vars
$action = $request->query->filter('action', '', FILTER_SANITIZE_STRING);
$id     = $request->query->getInt('id');
switch ($action) {

    // Marks a content as pending
    case 'set-draft':

        if (is_int($id)) {
            $content = new Content($id);
            $content->setDraft();
        }
        // Drop from any frontpage
        $content->dropFromAllHomePages();

        break;

    // Marks a content as available
    case 'set-available':

        if (is_int($id)) {
            $content = new Content($id);
            $content->setAvailable();
        }

        break;

    case 'set-unavailable':

        // poner como no disponible
        // y saca de todas las portadas

        // Drop fron any frontpage

        break;

    // Toggles availability of a ontent
    case 'toggle-available':

        if (is_int($id)) {
            $content = new Content($id);
            $availability = ($content->availability == 1);
            $content->toggleAvailable();
            if ($availability) {
                // Drop from any frontpage
            }
        }

        break;

    case 'archive':
        $ids = $request->request->get('ids');
        $error = array();

        foreach ($ids as $id) {
            $id = (int) $id;
            $content = new Content($id);

            if ($content->id !== null) {
                try {
                    $content->setArchived();
                } catch (\Exception $e) {
                    $error = sprintf(_('Unable to arquive content with id %s: %s'), $id, $e->getMessage());
                }
                $content->dropFromAllHomePages();
            } else {
                $error = sprintf('Content with id %s no valid', $id);
            }
        }

        if (count($error) > 0) {
            echo json_encode(array('error' => implode("<br>", $error)));
        } else {
            echo json_encode(array('done'));
        }
        break;


        break;

    case 'set-favorite':

        # code...

        break;

    case 'unset-favorite':

        # code...

        break;

    case 'set-suggested-to-frontpage':
        // suggestToHomepage
        # code...
        //frontpage=1

        break;

    case 'unset-suggested-to-frontpage':

        # code...

        break;

    case 'toggle-suggested':
        $ids = $request->request->get('ids');
        $error = array();

        foreach ($ids as $id) {
            $content = new Content($id);
            if ($content->id !== null) {
                try {
                    $content->toggleSuggested();
                } catch (\Exception $e) {
                    $error .= sprintf('Unable to set suggested to frontpage state to content with id %s: %s<br>', $id, $e->getMessage());
                }
            } else {
                $error .= sprintf('Content with id %s no valid<br>', $id);
            }
        }

        if (count($error) > 0) {
            echo json_encode(array('error' => $error));
        } else {
            echo json_encode(array('done'));
        }
        break;

    case 'send-to-trash':
        $content = new Content($id);

        if ($content->id !== null) {
            if ($content->setTrashed()) {
                $content->dropFromAllHomePages();
            } else {
                $error = sprintf('Unable to set trashed state to content with id %s', $id);
            }
        } else {
            $error = sprintf('Content with id %s no valid', $id);
        }

        if (isset($error)) {
            echo json_encode(array('error' => $error));
        } else {
            echo json_encode(array('done'));
        }

        break;

    case 'recover-from-trash':

        // pone como disponible

        break;

    case 'get-info':
        $content = new Content($id);
        if ($content->id !== null) {
            $output = $content->getQuickInfo();
        } else {
            $error = sprintf('Content with id %s no valid', $id);
        }
        if (isset($error)) {
            $output = json_encode(array('error' => $error));
        } else {
            $output = json_encode($output);
        }
        // var_dump($content);die();

        header('Content-type: application/json');
        echo $output;

        break;

    case 'calculate-tags':
        $tags = $request->query->filter('data', '', FILTER_SANITIZE_STRING);

        $tags = StringUtils::get_tags($tags);
        Application::ajaxOut($tags);
        break;



}