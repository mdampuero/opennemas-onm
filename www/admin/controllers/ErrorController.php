<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2010 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at                              |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Antonio Jozzolino <info@sgd.com.br>                         |
// +----------------------------------------------------------------------+
//
// $Id: ErrorController.php, v 0.01 Wed Aug 18 2010 19:36:27 GMT+0200 (CEST) Antonio Jozzolino $
//

/**
* Short desc
*
* Long description first sentence starts here
* and continues on this line for a while
* finally concluding here at the end of
* this paragraph
*
* @package    ABHO | SSCF | SGD
* @subpackage
* @author     Antonio Jozzolino <info@sgd.com.br>
* @version    $Id: ErrorController.php, v 0.01 Wed Aug 18 2010 19:36:27 GMT+0200 (CEST) Antonio Jozzolino $
* @since      Wed Aug 18 2010 19:36:24 GMT+0200 (CEST)
* @access     public
* @see        http://www.sgd.com.br
* @uses       file.ext|elementname|class::methodname()|class::$variablename|functionname()|function functionname  description of how the element is used
* @example    relativepath/to/example.php  description
*/

// TODO: gestión de informes
class ErrorController extends Onm_Controller_Action
{
    public $exception = null;
    public $request   = null;
    public $message   = null;
    public $enterprisey = null;
    
    public function preDispatch()
    {
        // TODO: Implement this, if a customer buys the enterprise edition stack
        // traces should be more verbosed and acurated.
        $this->enterprisey = true;
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(500);
                $this->message = 'Page not found';
                break;
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->message = 'Application error';
                break;
        }
        $this->exception = $errors->exception;
        $this->request   = $errors->request;
        
    }


}