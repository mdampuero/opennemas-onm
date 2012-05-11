<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Onm\Framework\Controller\Controller,
    Onm\Message as m,
    Onm\Settings as s;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class ImporterXmlfileController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        // Initializae the session manager
        require_once './session_bootstrap.php';

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
        if (
            is_null(s::get('xml_file_schema'))
            && $action != 'config'
        ) {
            m::add(_('Please provide XML file schema'));
            return $this->redirect($this->generateUrl('admin_importer_xmlfile_config'));
        }
    }

    /**
     * Description of the action
     *
     * @return void
     **/
    public function defaultAction()
    {
        return $this->render('agency_importer/xml-file/list.tpl');
    }

    /**
     * Handles the action of importing a bunch of XML files or zippped ones
     *
     * @return Response the response object
     **/
    public function importAction()
    {
        if ('POST' != $this->request->getMethod()) {
            m::add(_('Form was sent in the wrong way.'));
            return $this->redirect($this->generateUrl('admin_importer_xmlfile'));
        }

        $numCategories='0';

        if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {

            $dryRun = $this->request->query->filter('dry-run', FILTER_SANITIZE_STRING);
            for($i=0,$j=0;$i<count($_FILES["file"]["name"]);$i++) {

                $nameFile  = $_FILES["file"]["name"][$i];

                $datos     = pathinfo($nameFile);//sacamos info del archivo

                //Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
                $extension = $datos['extension'];
                $t         = gettimeofday(); //Sacamos los microsegundos
                $micro     = intval(substr($t['usec'],0,5)); //Le damos formato de 5digitos a los microsegundos

                $name      = date("YmdHis").$micro.".".$extension;

                if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploaddir.$name)) {

                    $check = !isset($_REQUEST['check_pendientes'][$i])?0:$_REQUEST['check_pendientes'][$i];

                    if ($extension == "zip") {
                        $dataZIP = FilesManager::decompressZIP($uploaddir.$name);

                        @chmod($uploaddir.$name,0775);
                        sort($dataZIP);
                        foreach ($dataZIP as $elementZIP) {
                            @chmod($uploaddir.$elementZIP, 0775);

                            $importer = ImporterXml::getInstance();
                            $eltoXML  = $importer->importXML($uploaddir.$elementZIP);
                            if ($eltoXML) {
                                $XMLFile[$j] = $elementZIP;

                                $values      = $importer->getXMLData($eltoXML);
                                if (!empty($dryRun)) {
                                    $article = new Article();
                                    $article->create($values);
                                }

                                $dataXML[$j] = $values;
                                $j++;
                            } else {
                            //    m::add(_( 'No valid XML format' ));
                            }
                        }
                    } else {
                        $importer    = ImporterXml::getInstance();

                        $eltoXML     = $importer->importXML($uploaddir.$name);

                        $XMLFile[$j] = $nameFile;


                        $values      = $importer->getXMLData($eltoXML);
                        if (!empty($dryRun)) {
                            $article = new Article();
                            $article->create($values);
                        }

                        $dataXML[$j] = $values;
                        $j++;
                    }

                } else {
                    m::add(sprintf(
                        _("There was an error while uploading «%s» - «%s». Check its size before send it."),
                        $uploaddir.$name, $nameFile
                    ));
                }
            }

            $this->view->assign(array(
                'numCategories' => $numCategories,
                'XMLFile' => $XMLFile,
                'dataXML' => $dataXML,
                'action' => "import",
                'total_num' => $j,
            ));


        }
        $tpl->display('agency_importer/xml-file/list.tpl');
    }

    /**
     * Handles the configuration form for the xml file
     *
     * @return Response the response object
     **/
    public function configAction()
    {
        if ('POST' == $this->request->getMethod()) {

            $config = array_map(function($item) {
                return filter_var($item, FILTER_SANITIZE_STRING);
            }, $config);

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
                'urn_source'    => $config['urn_source'],
                'img_footer'    => $config['img_footer'],
                'ignored'       => $config['ignored'],
                'important'     => $config['important'],
            );

            if (s::set('xml_file_schema', $schema) ) {
                m::add(_('Importer XML configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving importer XML configuration'), m::ERROR);
            }

            return $this->redirect($this->generateUrl('admin_importer_xmlfile'));
        } else {
            if ( $config = s::get('xml_file_schema') ) {

                $this->view->assign(array(
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
                    'urn_source'    => $config['urn_source'],
                    'img_footer'    => $config['img_footer'],
                    'ignored'       => $config['ignored'],
                    'important'     => $config['important'],
                ));

            }
            return $this->render('agency_importer/xml-file/config.tpl');
        }
    }
} // END class ImporterXmlfileController