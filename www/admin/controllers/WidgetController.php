<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class WidgetController extends Onm_Controller_Action
{
    
    public function init()
    {
        
    }
    
    
    /**
     * Route: widget-index
     *  /widget/index/*
     */
    public function indexAction()
    {
        $cm = new ContentManager();
        $widgets = $cm->find('Widget', 'fk_content_type=12', 'ORDER BY created DESC ');
        
        $toolbar = Onm_View_Helper_Toolbar::getInstance('toolbar-top');
        
        $toolbar->append(
                new Onm_View_Helper_Toolbar_Link('Link to Google', null, array('href' => 'http://google.com'))
            )->append(
                new Onm_View_Helper_Toolbar_Route('Route', 'add', array('route' => 'widget-create'))
            )->append(
                new Onm_View_Helper_Toolbar_Javascript('Javascript', 'inbox',
                    array(
                        'events' => array(
                            'click' => "alert('Inbox empty')",
                            'mouseout'  => "console.log('[OUT] debug in firebug')",
                            'mouseover' => "console.log('[OVER] debug in firebug')",
                        )
                    )
                )
            )->append(
                new Onm_View_Helper_Toolbar_Button('Botón', 'user',
                    array(
                        'type' => 'button',
                        'events' => array(
                            'click' => "alert('User info')"
                        )
                    )
                )
            )->append(
                new Onm_View_Helper_Toolbar_Button('Submit', 'open', array('type' => 'submit'))
            );
        
        $themeTable = array(
            'openToolbar'  => '<table class="toolbar" id="%s"><tr>',
            'closeToolbar' => '</tr></table>',
            'openItemToolbar'  => '<td>',
            'closeItemToolbar' => '</td>',
        );
        $toolbar->changeTheme($themeTable);
        
        //$button = new Onm_View_Helper_Toolbar_Link('Route', 'add', array('route' => 'widget-create'));
        //$toolbar->append($button);
        
        
        
        /*$buttons = array(
            array('Link', 'add', 'add', array('href' => '#')),
            array('Link', 'apply', 'apply', array('href' => '#')),
            array('Link', 'attach', 'attach', array('href' => '#')),
            array('Link', 'back', 'back', array('href' => '#')),
            array('Link', 'calendar', 'calendar', array('href' => '#')),
            array('Link', 'close', 'close', array('href' => '#')),
            array('Link', 'configure', 'configure', array('href' => '#')),
            array('Link', 'default', 'default', array('href' => '#')),
            array('Link', 'delete', 'delete', array('href' => '#')),
            array('Link', 'edit', 'edit', array('href' => '#')),
            array('Link', 'forward', 'forward', array('href' => '#')),
            array('Link', 'inbox', 'inbox', array('href' => '#')),
            array('Link', 'info', 'info', array('href' => '#')),
            array('Link', 'logout', 'logout', array('href' => '#')),
            array('Link', 'mail', 'mail', array('href' => '#')),
            array('Link', 'new', 'new', array('href' => '#')),
            array('Link', 'open', 'open', array('href' => '#')),
            array('Link', 'redo', 'redo', array('href' => '#')),
            array('Link', 'refresh', 'refresh', array('href' => '#')),
            array('Link', 'restart', 'restart', array('href' => '#')),
            array('Link', 'save_all', 'save_all', array('href' => '#')),
            array('Link', 'saveas', 'saveas', array('href' => '#')),
            array('Link', 'save', 'save', array('href' => '#')),
            array('Link', 'search', 'search', array('href' => '#')),
            array('Link', 'start', 'start', array('href' => '#')),
            array('Link', 'stop', 'stop', array('href' => '#')),
            array('Link', 'trash', 'trash', array('href' => '#')),
            array('Link', 'undo', 'undo', array('href' => '#')),
            array('Link', 'up', 'up', array('href' => '#')),
            array('Link', 'user', 'user', array('href' => '#')),
            array('Link', 'user_properties', 'user_properties', array('href' => '#')),
            array('Link', 'zoom_in', 'zoom_in', array('href' => '#')),
            array('Link', 'zoom_out', 'zoom_out', array('href' => '#')),                        
        );
        Onm_View_Helper_Toolbar::loadFromArray('toolbar-top', $buttons);*/     
        
        // TODO: pagination        
        $this->tpl->assign('widgets', $widgets);
        
        $this->tpl->display('widget/index.tpl');
    }
    
    
    /**
     * Route: widget-read
     *  /widget/read/:id/
     */
    public function readAction()
    {        
        $id = $this->_getParam('id', 0);
        
        $widget = new Widget();
        $widget->read($id);        
        
        $this->tpl->assign('id', $id);
        $this->tpl->assign('widget', $widget);
        
        $this->tpl->display('widget/index.tpl');
    }
    
    
    /**
     * Route: widget-create
     *  /widget/create/
     */    
    public function createAction()
    {
        if($this->getRequest()->isPost()) {
            
            $data = $this->getRequest()->getPost();
            // TODO: Validation
            $widget->create($data);
            
            $this->flashMessenger->addMessage(array('notice' => 'Widget added successfully.'));
            $this->redirector->gotoRoute( array(), 'widget-index' );
            
        } else {
            // Show form
            $this->tpl->display('widget/index.tpl');
        }
    }
    
    
    /**
     * Route: widget-update
     *  /widget/update/:id/
     */
    public function updateAction()
    {
        if($this->getRequest()->isPost()) {            
            $data = $this->getRequest()->getPost();
            $widget->update($data);
            
            $this->flashMessenger->addMessage(array('notice' => 'Widget added successfully.'));
            $this->redirector->gotoRoute( array(), 'widget-index' );            
        } else {
            // Load data
            $id = $this->_getParam('id', 0);
            
            $widget = new Widget();
            $widget->read($id);        
            
            $this->tpl->assign('id', $id);
            $this->tpl->assign('widget', $widget);
            
            // Show form
            $this->redirector->gotoRoute( array(), 'widget-index' );
        }
    }
    
    
    /**
     * Route: widget-delete
     *  /widget/delete/:id/
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id', 0);
        
        $widget = new Widget();
        $widget->delete($id);
        
        $this->redirector->gotoRoute( array(), 'widget-index' );        
    }
    
    
    /**
     * Route: widget-toggle
     *  /widget/toggle/:id/
     */
    public function toggleAction()
    {
        $id = $this->_getParam('id', 0);
        $widget->read($id);
        
        $available = ($widget->available+1) % 2;
        $widget->set_available($available, $_SESSION['userid']);
        
        $request = $this->getRequest();
        
        if( $request->isXmlHttpRequest() ) {
            list($img, $text)  = ($available)? array('g', _('PUBLICADO')): array('r', _('PENDIENTE'));
            
            echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            exit(0);
        }
        
        $this->redirector->gotoRoute( array(), 'widget-index' );
    }
    
}