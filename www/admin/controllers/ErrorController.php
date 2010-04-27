<?php
// TODO: gestión de informes

class ErrorController extends Onm_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->tpl->assign('message', 'Page not found');
                break;
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->tpl->assign('message', 'Application error');
                break;
        }
        
        $this->tpl->assign('exception', $errors->exception);
        $this->tpl->assign('request', $errors->request);
        
        $this->tpl->display('error.tpl');
    }


}

