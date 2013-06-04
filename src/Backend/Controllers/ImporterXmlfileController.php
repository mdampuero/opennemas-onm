<?php
/**
 * Handles the actions for the system information
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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class ImporterXmlfileController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('PAPER_IMPORT');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
        if (is_null(s::get('xml_file_schema'))
            && $action != 'config'
        ) {
            m::add(_('Please provide XML file schema'));

            return $this->redirect($this->generateUrl('admin_importer_xmlfile_config'));
        }
    }

    /**
     * Shows the upload form for importing XML files
     *
     * @param Request $request the request object
     *
     * @return void
     **/
    public function defaultAction(Request $request)
    {
        return $this->render('agency_importer/xml-file/list.tpl');
    }

    /**
     * Handles the action of importing a bunch of XML files or zippped ones
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function importAction(Request $request)
    {

        if ('POST' != $this->request->getMethod()) {
            m::add(_('Form was sent in the wrong way.'));

            return $this->redirect($this->generateUrl('admin_importer_xmlfile'));
        }

        $uploaddir = APPLICATION_PATH .DS.'tmp'.DS.'instances'.DS.INSTANCE_UNIQUE_NAME.DS.'xml'.DS;

        if (!file_exists($uploaddir)) {
            mkdir($uploaddir, 0775);
        }

        $numCategories='0';

        if (count($_FILES["file"]["name"]) >= 1
            && !empty($_FILES["file"]["name"][0])
        ) {

            $dryRun = $this->request->query->filter('dry-run', FILTER_SANITIZE_STRING);
            for ($i=0, $j=0; $i<count($_FILES["file"]["name"]); $i++) {

                $nameFile  = $_FILES["file"]["name"][$i];

                $datos     = pathinfo($nameFile);//sacamos info del archivo
                // Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
                $extension = $datos['extension'];
                $t         = gettimeofday();
                $micro     = intval(substr($t['usec'], 0, 5));

                $name      = date("YmdHis").$micro.".".$extension;

                if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploaddir.$name)) {

                    $check = !isset($_REQUEST['check_pendientes'][$i])
                        ? 0 : $_REQUEST['check_pendientes'][$i];

                    if ($extension == "zip") {
                        $dataZIP = \FilesManager::decompressZIP($uploaddir.$name);

                        @chmod($uploaddir.$name, 0775);
                        sort($dataZIP);
                        foreach ($dataZIP as $elementZIP) {
                            @chmod($uploaddir.$elementZIP, 0775);

                            $importer = \ImporterXml::getInstance();
                            $eltoXML  = $importer->importXML($uploaddir.$elementZIP);
                            if ($eltoXML) {
                                $XMLFile[$j] = $elementZIP;

                                $values      = $importer->getXMLData($eltoXML);
                                if (!empty($dryRun)) {
                                    $article = new \Article();
                                    $article->create($values);
                                    $photo = new \Photo($values['img1']);
                                    $values['photo'] =
                                        INSTANCE_MEDIA.IMG_DIR.
                                        $photo->path_file.$photo->name;
                                    $dataXML[$j] = $values;
                                }

                                $dataXML[$j] = $values;
                                $j++;
                            } else {
                                //  m::add(_( 'No valid XML format' ));
                            }
                        }
                    } else {
                        $importer    = \ImporterXml::getInstance();

                        $eltoXML     = $importer->importXML($uploaddir.$name);

                        $XMLFile[$j] = $nameFile;

                        $values      = $importer->getXMLData($eltoXML);
                        if (!empty($dryRun)) {
                            $article = new \Article();
                            $article->create($values);
                            $photo = new \Photo($values['img1']);
                            $values['photo'] =
                                INSTANCE_MEDIA.IMG_DIR.
                                $photo->path_file.$photo->name;
                            $dataXML[$j] = $values;
                        }

                        $dataXML[$j] = $values;

                        $j++;
                    }

                } else {
                    m::add(
                        sprintf(
                            _("There was an error while uploading «%s» - «%s». Check its size before send it."),
                            $uploaddir.$name,
                            $nameFile
                        )
                    );
                }
            }


        }

        return $this->render(
            'agency_importer/xml-file/list.tpl',
            array(
                'numCategories' => $numCategories,
                'XMLFile' => $XMLFile,
                'dataXML' => $dataXML,
                'action' => "import",
                'total_num' => $j,
            )
        );
    }

    /**
     * Handles the configuration form for the xml file
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        if ('POST' == $this->request->getMethod()) {
            $config =$request->request->get('config');

            $config = array_map(
                function ($item) {
                    return filter_var($item, FILTER_SANITIZE_STRING);
                },
                $config
            );

            $schema =  array(
                'title'         => $config['title'],
                'title_int'     => $config['title_int'],
                'subtitle'      => $config['subtitle'],
                'summary'       => $config['summary'],
                'agency'        => $config['agency'],
                'created'       => $config['created'],
                'body'          => $config['body'],
                'metadata'      => $config['metadata'],
                'description'   => $config['description'],
                'category_name' => $config['category_name'],
                'body'          => $config['body'],
                'img'           => $config['img'],
                'img_footer'    => $config['img_footer'],
                'ignored'       => $config['ignored'],
                'important'     => $config['important'],
            );

            if (s::set('xml_file_schema', $schema)) {
                m::add(_('Importer XML configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving importer XML configuration'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_importer_xmlfile_config'));
        } else {
            if ($config = s::get('xml_file_schema')) {

                $this->view->assign(
                    array(
                        'title'         => $config['title'],
                        'title_int'     => $config['title_int'],
                        'subtitle'      => $config['subtitle'],
                        'summary'       => $config['summary'],
                        'agency'        => $config['agency'],
                        'created'       => $config['created'],
                        'body'          => $config['body'],
                        'metadata'      => $config['metadata'],
                        'description'   => $config['description'],
                        'category_name' => $config['category_name'],
                        'body'          => $config['body'],
                        'img'           => $config['img'],
                        'img_footer'    => $config['img_footer'],
                        'ignored'       => $config['ignored'],
                        'important'     => $config['important'],
                    )
                );

            }

            return $this->render('agency_importer/xml-file/config.tpl');
        }
    }
}
