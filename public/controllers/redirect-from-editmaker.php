<?php
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Fetch HTTP variables
*/
$contentType = filter_input(INPUT_GET,'content_type',FILTER_SANITIZE_STRING);
if (!(isset($contentType))) { $contentType = null; }

$contentID = filter_input(INPUT_GET,'content_id',FILTER_SANITIZE_STRING);
if (!(isset($contentID))) { $contentID = null; }

/**
 * Helper function to check existance one element in translation_ids table
 */
function getOriginalIDForContentTypeAndID( $content_type, $content_id) {
    
    
    $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=? LIMIT 1';
    
    $_values = array($content_id, $content_type);
    $_sql = $GLOBALS['application']->conn->Prepare($sql);
    $rss = $GLOBALS['application']->conn->Execute($_sql, $_values);

    if (!$rss) {
        $error_msg = $GLOBALS['application']->conn->ErrorMsg();
        $GLOBALS['application']->logger->debug('Error: '.$error_msg);
        $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

        $returnValue = false;
    } else {
        if ($rss->_numOfRows > 0) {
            
            $returnValue =  $rss->fields['pk_content'];
            
        } else {
            $returnValue = false;
        }
    }
    
    return $returnValue;
    
}

function getOriginalIdAndContentTypeFromID( $content_id) {
    
    
    $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? LIMIT 1';
    
    $_values = $content_id;
    $_sql = $GLOBALS['application']->conn->Prepare($sql);
    $rss = $GLOBALS['application']->conn->Execute($_sql, $_values);

    if (!$rss) {
        $error_msg = $GLOBALS['application']->conn->ErrorMsg();
        $GLOBALS['application']->logger->debug('Error: '.$error_msg);
        $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

        $returnValue = false;
    } else {
        if ($rss->_numOfRows > 0) {
            
            $returnValue =  array($rss->fields['type'],
                                  $rss->fields['pk_content']);
            
        } else {
            $returnValue = false;
        }
    }
    
    return $returnValue;
    
}





// All the info is available so lets create url to redirect to
if (!is_null($contentID)) {
    
    $url = SITE_URL;

    /**
     * Instantiate objects we will use
    */
    $cm = new ContentManager();
    $ccm = ContentCategoryManager::get_instance();
    
    switch ($contentType) {
        case 'article':
            
            list($type,$newContentID) = getOriginalIdAndContentTypeFromID($contentID);
            
            if ($type == 'article') {
                $article = new Article($newContentID);
                $article->category_name = $article->catName;
                
                $url .=  Uri::generate( 'article',
                                array(
                                    'id' => $article->id,
                                    'date' => date('Y-m-d', strtotime($article->created)),
                                    'category' => $article->category_name,
                                    'slug' => $article->slug,
                                )
                            );
            } elseif ($type == 'opinion') {
                
                $opinion = new Opinion($newContentID);
                
                
                $url .=  Uri::generate( 'opinion',
                                array(
                                    'id' => $opinion->id,
                                    'date' => date('Y-m-d', strtotime($opinion->created)),
                                    'category' => String_Utils::get_title($opinion->name), //review this
                                    'slug' => $opinion->slug,
                                )
                            );
                    
            }
            
            break;
        
        case 'category':
            
            $newContentID = getOriginalIDForContentTypeAndID( $contentType, $contentID);

            $cc = new ContentCategory($newContentID);
            
            $url .= Uri::generate('section', array('id' => $cc->name));
            
            break;
        
        default:
            break;
    }
} else {
    $url = SITE_URL;
}

if(isset($_REQUEST['stop_redirect'])) { echo $url; die(); }

Header( "HTTP/1.1 301 Moved Permanently" );
Header( "Location: $url" );
?> 