<?php
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');
require_once(SITE_LIBS_PATH.'utils.functions.php');

function getCategoryList()
{
    $xml_categories = new SimpleXMLElement("<categories></categories>");

    //Obtengo las categorias disponibles para hacer subidas
    $ccm = new ContentCategoryManager();
    list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

    $num_cats = count($parentCategories);
    $xml_categories->addAttribute("num", $num_cats+2);

    for ($i=0;$i<$num_cats;$i++) {
        $xml_category = $xml_categories->addChild('category');
        $xml_category->addAttribute('pk_content_category', $parentCategories[$i]->pk_content_category);
        $xml_category->addChild('title', $parentCategories[$i]->title);
        $xml_category->addChild('name', $parentCategories[$i]->name);
        $xml_category->addChild('inmenu', $parentCategories[$i]->inmenu);
        $xml_category->addChild('posmenu', $parentCategories[$i]->posmenu);
        $xml_category->addChild('internalcategory', $parentCategories[$i]->internal_category);
        $xml_category->addChild('fk_content_category', $parentCategories[$i]->fk_content_category);

        if (!empty($subcat[$i])) {
            $num_subcats = count($subcat[$i]);
            $xml_subcategories = $xml_category->addChild('subcategories');
            $xml_subcategories->addAttribute("num", $num_subcats);
            foreach ($subcat[$i] as $scat) {
                $xml_subcategory = $xml_subcategories->addChild('subcategory');
                $xml_subcategory->addAttribute('pk_content_category', $scat->pk_content_category);
                $xml_subcategory->addChild('title', $scat->title);
                $xml_subcategory->addChild('name', $scat->name);
                $xml_subcategory->addChild('inmenu', $scat->inmenu);
                $xml_subcategory->addChild('posmenu', $scat->posmenu);
                $xml_subcategory->addChild('internalcategory', $scat->internal_category);
                $xml_subcategory->addChild('fk_content_category', $scat->fk_content_category);
            }
        } else {
            $xml_subcategories = $xml_category->addChild('subcategories');
            $xml_subcategories->addAttribute("num", 0);
        }
    }

    $responsePayloadString = $xml_categories->asXML();
    $returnMessage = new WSMessage($responsePayloadString);

    return $returnMessage;
}

function addMediaImages($message)
{
    $cid2stringMap = $message->attachments;
    $cid2contentMap = $message->cid2contentType;
    $response = "";

    $xml = simplexml_load_string($message->str, 'SimpleXMLElement');

    $j=0;
    foreach ($cid2stringMap as $i=>$image_data) {
        $f = $cid2stringMap[$i];
        $contentType = $cid2contentMap[$i];

        $data['category']=$data['fk_category']=(string) $xml->image[$j]->fk_category;

        $ccm = ContentCategoryManager::get_instance();

        $nameCat= $ccm->categories[$data['category']]->name;
        $relative_path = "/".$nameCat ."/".date("Ymd")."/";
        $uploaddir = MEDIA_IMG_PATH . $relative_path;
        if (!is_dir($uploaddir)) {
            mkdir($uploaddir, 0775);
            @chmod($uploaddir,0775); //Permisos de lectura y escritura del fichero
        }

        $tmp_image = '/tmp/'.$xml->image[$j]->title;
        file_put_contents($tmp_image, $f);
        $datos=pathinfo($tmp_image);     //sacamos infor del archivo
        unlink($tmp_image);

        //Preparamos el nuevo nombre YYYYMMDDHHMMSSmmmmmm
        $extension=$datos['extension'];
        $t=gettimeofday(); //Sacamos los microsegundos
        $micro=intval(substr($t['usec'],0,5)); //Le damos formato de 5digitos a los microsegundos
        $name= date("YmdHis").$micro.".".$extension;

        if (file_put_contents($uploaddir.$name, $f)) {
            @chmod($uploaddir.$name,0777); //Permisos de lectura y escritura del fichero
            $data['title']=$xml->image[$j]->title;
            $data['description']=$xml->image[$j]->description;
            $data['metadata']=$xml->image[$j]->metadata;
            $data['name']=$name;
            $data['path_file']= $relative_path;

            $data['nameCat']=$nameCat; //nombre de la category

            $infor  = new MediaItem( $uploaddir.$name ); 	//Para sacar todos los datos de la imag

            $data['created']=$infor->atime;
            $data['changed']=$infor->mtime;
            $data['date']=$infor->mtime;
            $data['size']=round($infor->size/1024,2) ;
            $data['width']=$infor->width;
            $data['height']=$infor->height;
            $data['type_img']=$extension;
            $data['media_type']="image";

            $foto = new Photo();
            $elid = $foto->create($data);

            if ( $elid) {
                if ($extension=='jpeg' || $extension=='jpg'  || $extension=='png' || $extension=='gif' ) {
                     //   miniatura
                        $thumb=new Imagick($uploaddir.$name);
                        $thumb->thumbnailImage(140,100,true);
                        //Write the new image to a file
                        $thumb->writeImage($uploaddir.'140x100-'.$name);
              }
            }
            $response .= '<image><title>'.$data['title'].'</title><save>1</save></image>';
         } else {
            $response .= '<image><title>'.$data['title'].'</title><save>0</save></image>';
        }

        $j++;
    }



    if (!isset($fallos)) {
        $responsePayload = '<addMediaImages>'.$response.'</addMediaImages>';
    } else {
        $responsePayload = '<response>0</response>';
    }

    $returnMessage = new WSMessage($responsePayload);

    return $returnMessage;
}

function autenticateUsers($username)
{
    $user = new User();
    $pwd = $user->getPwd($username);

    if (isset($pwd)) {
        return $pwd;
    } else return NULL;
}


$operations = array(
    "addMediaImages" => "addMediaImages",
    "getCategoryList" => "getCategoryList"
);
$actions = array(
    "http://xornal.com/webservice/getCategoryList" => "getCategoryList",
    "http://xornal.com/webservice/addMediaImages" => "addMediaImages"
);
$security_options = array("useUsernameToken" => TRUE);

$policy = new WSPolicy(array("security"=>$security_options));
$security_token = new WSSecurityToken(array("passwordCallback" => "autenticateUsers",
                                            "passwordType" => "Digest"
                                        ));

$service = new WSService(array("operations" => $operations,
                               "policy" => $policy,
                               "actions" => $actions,
                               "securityToken" => $security_token,
                               "requestXOP" => TRUE
                           ));

$service->reply();

//$requestPayloadString = <<<XML
//<addMediaImages>
//   <image>
//       <title>test.jpg</title>
//       <description>s</description>
//       <metadata>s</metadata>
//       <fk_category>10</fk_category>
//       <file xmlmime:contentType="image/jpeg" xmlns:xmlmime="http://www.w3.org/2004/06/xmlmime">
//          <xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:myid1"></xop:Include>
//       </file>
//   </image>
//   <image>
//       <title>test2.jpg</title>
//       <description>s</description>
//       <metadata>s</metadata>
//       <fk_category>10</fk_category>
//       <file xmlmime:contentType="image/jpeg" xmlns:xmlmime="http://www.w3.org/2004/06/xmlmime">
//          <xop:Include xmlns:xop="http://www.w3.org/2004/08/xop/include" href="cid:myid2"></xop:Include>
//       </file>
//   </image>
//</addMediaImages>
//XML;
//
//    $f = file_get_contents("./images.jpg");
//    $g = file_get_contents("./luis-bassat.jpg");
//
//    $reqMessage = new WSMessage($requestPayloadString,
//                                array("to" => "http://webdev-xornal.es/ws.php",
//                                      "action" => "http://xornal.com/webservice/addMediaImages",
//                                      "attachments" => array("myid1" => $f,"myid2" => $g)));
//
//var_dump(addMediaImages($reqMessage));
