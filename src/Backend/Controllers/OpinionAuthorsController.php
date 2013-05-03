<?php
/**
 * Handles the actions for managing opinions
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
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for managing opinions
 *
 * @package Backend_Controllers
 **/
class OpinionAuthorsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->checkAclOrForward('AUTHOR_ADMIN');
    }

    /**
     * Lists all the opinion authors
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $this->checkAclOrForward('AUTHOR_ADMIN');

        $page = $request->query->getDigits('page', 1);
        $name = $request->query->filter('name', null, FILTER_SANITIZE_STRING);

        $itemsPerPage = s::get('items_per_page') ?: 20;

        if (isset($name) && !empty($name)) {
            $filter = 'MATCH(`name`) AGAINST ("'.$name.'" IN BOOLEAN MODE)';
        } else {
            $filter = null;
        }

        $cm          = new \ContentManager();
        $author      = new \Author();
        $authors     = $author->list_authors($filter, 'ORDER BY name ASC');

        $authorsPage = array_slice($authors, ($page-1)*$itemsPerPage, $itemsPerPage);

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($authors),
                'fileName'    => $this->generateUrl('admin_opinion_authors').'?page=%d',
            )
        );

        return $this->render(
            'opinion/authors/list.tpl',
            array(
                'name'          => $name,
                'authors'       => $authorsPage,
                'pagination'    => $pagination,
            )
        );
    }

    /**
     * Shows the information for an opinion author given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {

        $this->checkAclOrForward('AUTHOR_UPDATE');

        $id = $request->query->getDigits('id', null);
        $page = $request->query->getDigits('page', 1);

        $author = new \Author($id);
        if (is_null($author->id)) {
            m::add(sprintf(_('Unable to find the author with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_opinion_authors', array('page' => $page)));
        }

        $photos = $author->get_author_photos($id);

        if (count($photos) < 1) {
            m::add(sprintf(_('This author doesn\'t have an image associated please select one.')));
        }

        return $this->render(
            'opinion/authors/new.tpl',
            array(
                'author'  => $author,
                'photos' => $photos,
            )
        );
    }

    /**
     * Creates a new author given its information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {

        $this->checkAclOrForward('AUTHOR_CREATE');

        if ('POST' == $request->getMethod()) {

            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            $author = new \Author();

            $data = array(
                'name'          => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
                'condition'     => $request->request->filter('condition', '', FILTER_SANITIZE_STRING),
                'politics'      => $request->request->filter('politics', '', FILTER_SANITIZE_STRING),
                'blog'          => $request->request->filter('blog', '', FILTER_SANITIZE_STRING),
                'fk_author_img' => $request->request->filter('fk_author_img', '', FILTER_SANITIZE_STRING),
                'params'        => $request->request->get('params'),
                'photos'        => $request->request->get('photos', array()),
            );

            $file = $request->files->get('photo-file');

            try {
                if (!is_null($file)) {
                    $photoId = $this->uploadAuthorPhoto($file, $data['name']);

                    $data['new_photo_id'] = $photoId;
                }

                if ($author->create($data)) {
                    m::add(_('Author successfully created.'), m::SUCCESS);
                } else {
                    m::add(_('Unable to create the author.'), m::ERROR);
                }
            } catch (FileException $e) {

                m::add(_('Unable to upload the new author image.'), m::ERROR);
            }

            if ($continue) {
                return $this->redirect(
                    $this->generateUrl('admin_opinion_author_show', array('id' => $author->pk_author))
                );
            } else {
                return $this->redirect($this->generateUrl('admin_opinion_authors'));
            }

        } else {
            return $this->render('opinion/authors/new.tpl');
        }

    }

    /**
     * Updates the author information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $this->checkAclOrForward('AUTHOR_UPDATE');

        $id = $request->query->getDigits('id');

        $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
        $author = new \Author($id);

        if ($author->id != null) {
            // Check empty data
            if (count($request->request) < 1) {
                m::add(_("Author data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_opinion_author_show', array('id' => $id)));
            }

            $data = array(
                'id'            => $id,
                'name'          => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
                'condition'     => $request->request->filter('condition', '', FILTER_SANITIZE_STRING),
                'politics'      => $request->request->filter('politics', '', FILTER_SANITIZE_STRING),
                'blog'          => $request->request->filter('blog', '', FILTER_SANITIZE_STRING),
                'fk_author_img' => $request->request->filter('fk_author_img', '', FILTER_SANITIZE_STRING),
                'params'        => $request->request->get('params'),
                'photos'        => $request->request->get('photos', array()),
            );

            $file = $request->files->get('photo-file');

            try {
                if (!is_null($file)) {
                    $photoId = $this->uploadAuthorPhoto($file, $data['name']);

                    $data['new_photo_id'] = $photoId;
                }

                if ($author->update($data)) {
                    m::add(_('Author successfully updated.'), m::SUCCESS);
                } else {
                    m::add(_('Unable to update the author.'), m::ERROR);
                }
            } catch (FileException $e) {

                m::add(_('Unable to upload the new author image.'), m::ERROR);
            }

            if ($continue) {
                return $this->redirect(
                    $this->generateUrl(
                        'admin_opinion_author_show',
                        array('id' => $author->id)
                    )
                );
            } else {
                return $this->redirect($this->generateUrl('admin_opinion_authors'));
            }
        }
    }

    /**
     * Delete an author given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('AUTHOR_DELETE');

        $id       = $request->query->getDigits('id');
        $page     = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $author = new \Author($id);

            $author->delete($id, $_SESSION['userid']);
            m::add(_("Author deleted successfully."), m::SUCCESS);
        } else {
            m::add(_('You must give an id for delete an opinion author.'), m::ERROR);
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl('admin_opinion_authors', array('page' => $page))
            );
        } else {
            return new Response('Ok', 200);
        }
    }

    /**
     * Process an uploaded photo author
     *
     * @param Symfony\Component\HttpFoundation\File\UploadedFile $file the uploaded file
     * @param string $authorName the author name
     *
     * @return Response the response object
     **/
    public function uploadAuthorPhoto($file, $authorName)
    {
        $authorName = \Onm\StringUtils::normalize_name($authorName);

        $relativeAuthorImagePath ="/authors/".$authorName;
        $uploadDirectory =  MEDIA_IMG_PATH .$relativeAuthorImagePath;

        $nameFile = $file->getClientOriginalName();    //Nombre del archivo a subir
        $originalFileData    = pathinfo($nameFile);                  //sacamos inofr del archivo

        $extension = $originalFileData['extension'];
        $t         = gettimeofday();
        $micro     = intval(substr($t['usec'], 0, 5));

        $name      = date("YmdHis").$micro.".".$extension;

        if (!is_dir($uploadDirectory)) {
            \FilesManager::createDirectory($uploadDirectory);
        }

        $file->move($uploadDirectory, $name);

        //Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
        $extension = strtolower($originalFileData['extension']);

        $data = array(
            'title'       => $nameFile,
            'name'        => $name,
            'path_file'   => $relativeAuthorImagePath,
            'nameCat'     => $authorName,
            'category'    => '',
        );

        $infor               = new \MediaItem($uploadDirectory.'/'.$name);

        $data['created']     = $infor->atime;
        $data['changed']     = $infor->mtime;
        $data['date']        = $infor->mtime;
        $data['size']        = round($infor->size/1024, 2);
        $data['width']       = $infor->width;
        $data['height']      = $infor->height;
        $data['type']        = $infor->type;
        $data['type_img']    = $extension;
        $data['media_type']  = 'image';
        $data['author_name'] = '';

        $photo = new \Photo();
        $elid = $photo->create($data);

        return $elid;
    }

    /**
     * Set the published flag for contents in batch
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('AUTHOR_DELETE');

        $selected = $request->query->get('selected_fld', null);
        $page     = $request->query->getDigits('page', 1);

        if (is_array($selected)
            && count($selected) > 0
        ) {
            foreach ($selected as $id) {
                $author = new \Author($id);

                if ($author->id != null && $author->delete($id)) {
                    m::add(sprintf(_('Deleted the author successfully.'), $id), m::SUCCESS);
                } else {
                    m::add(sprintf(_('Unable to delete the author with the id %s.'), $id), m::ERROR);
                }
            }
        }

        return $this->redirect(
            $this->generateUrl('admin_opinion_authors', array('page' => $page,))
        );
    }
}
