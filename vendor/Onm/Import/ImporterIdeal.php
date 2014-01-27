<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import;

/**
 * Importer for IdealGallego.
 *
 * @package    Onm
 * @subpackage Import
 */
class ImporterIdeal
{
    //TODO:mixer functions - separate in classes
    public static function importXML($xmlFile)
    {
        $xmlStr = @file_get_contents($xmlFile);
        $s = simplexml_load_string($xmlStr);

        return $s;
    }

    public static function checkLabel($label)
    {
        $relation = array(
            "/Antetitulo/"          =>"subtitle",
            "/TextoGeneral/"        =>"body",
            "/Titulo/"              =>"title",
            "/T?tulo Int/"          =>"title_int",
            "/TituloBreveDespiece/" =>"title_int",
            "/Firma/"               => "agency",
            "/Data/"                => "agency",
            "/TextoBandera/"        =>"summary",
            "/Entradilla/"          =>"summary",
            "/Cuadratin/"           =>"summary"
        );
        foreach ($relation as $pattern => $value) {
            if (preg_match($pattern, $label)) {

                return $value;
            }
        }
    }

    public static function checkXMLData($docXML)
    {
        $data =array();

        $data['subtitle']  ="";
        $data['agency']    ="";
        $data['title']     ="";
        $data['title_int'] ="";
        $data['summary']   ="";
        $data['body']      ="";
        $data['pk_author'] = $_SESSION['userid'];

        foreach ($docXML as $nodeXML) {
            foreach ($nodeXML as $eleto) {
                if ($eleto->getname()=='meta') {
                    if ($eleto->attributes()->name =='day') {
                        $day = $eleto->attributes()->content;
                    }
                    if ($eleto->attributes()->name =='month') {
                        $month = $eleto->attributes()->content;
                    }
                    if ($eleto->attributes()->name =='year') {
                        $year = $eleto->attributes()->content;
                    }
                } else {
                    foreach ($eleto->attributes() as $a => $b) {
                        // Tiene los nombres en el atribute class
                        if ($a == 'class') {
                            $field = self::checkLabel($b);
                        }

                        if (!empty($field) && empty($data[$field])) {
                            //El primero que encuentra es con el que se queda
                            $data[$field] = '';
                            foreach ($eleto->p as $span) {
                                foreach ($span as $texto) {
                                    $data[$field] .=$texto;
                                }
                            }
                        }
                        // Algunos son nodos inferiores
                        if (count($eleto->children())>0) {
                            foreach ($eleto->children() as $node) {
                                foreach ($node->attributes() as $c => $d) {
                                    $field = self::checkLabel($d);
                                    if (!empty($field)
                                        && empty($data[$field])
                                    ) {
                                        if ($field =='agency') {
                                            $data[$field] =
                                                $node->children()->span[0]
                                                .$node->children()->span[1];
                                        } else {
                                            $data[$field] = $node->p->span ;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $data['created']   = $year.'-'
            .sprintf("%02d", $month).'-'.sprintf("%02d", $day).' '.'00:00:00';

        $data['metadata']        = "";
        $data['agency_web']      = "";
        $data['img1']            = "";
        $data['img1']            = "";
        $data['img1_footer']     = "";
        $data['img2']            = "";
        $data['img2_footer']     = "";
        $data['with_galery']     = "";
        $data['with_galery_int'] = "";
        $data['with_comment']    = "1";
        $data['columns']         = "1";
        $data['description']     = "";
        $data['fk_video']        = "";
        $data['fk_video2']       = "";
        $data['footer_video2']   = "";
        $data['ordenArti']       = "";
        $data['ordenArtiInt']    = "";

        $metadata = '';//StringUtils::get_title($data['title']);
        $data['metadata'] = str_replace('-', ',', $metadata);

        $data['content_status']=0;
        $data['available']=0;
        $data['frontpage']=0;
        $data['category']=20;
        $data['fk_publisher'] ='';

        if (empty($data['title_int'])) {
            $data['title_int']=$data['title'];
        }

        return $data;
    }

    //De xornal pasar a string utils
    public static function splitBodyInHtmlParagraph($body)
    {
        $bodyiso = mb_convert_encoding($body, "UTF-8", "ISO-8859-1");

        $bodyisoArray = split("â©", $bodyiso);

        $bodyiso = array();
        foreach ($bodyisoArray as $stringArray => $value) {
            $bodyiso[] = "<p>".$value."</p>";
        }

        foreach ($bodyiso as $stringArray => $value) {
            $bodyutf .= mb_convert_encoding($value, "ISO-8859-1", "UTF-8");
        }

        return $bodyutf;
    }
}
