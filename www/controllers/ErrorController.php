<?php

// TODO: gestión de informes
class ErrorController extends Onm_Controller_Action
{
    public $exception = null;
    public $request   = null;
    public $message   = null;

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
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

