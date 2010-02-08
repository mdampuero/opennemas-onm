/*jslint debug: true, eqeqeq: false, browser: true, on: true, indent: 4, plusplus: false, white: false */
var CommentFormClass = Class.create({    
    initialize: function(options) {
        this.type = options.type || 'default';
        this.box  = $(options.box) || null;
        this.fbApiKey = options.fbApiKey || null;
        
        // Cache image
        var img = new Image();
        img.src = '/themes/xornal/images/ajax-loader.gif';
        
        this.serverUrl = '/planconecta.php?action=#{action}&category_name=conecta';
        
        this._saveComment = this.saveComment.bindAsEventListener(this);
        
        $('btnEnviar').setStyle({display: ''});
        $('btnEnviar').observe('click', this._saveComment);
    },
    
    loginConecta: function(frm) {
        var postParams = $(frm).serialize();
        this.waiting($('auth_message'));
        
        new Ajax.Request(this.serverUrl.interpolate({action: 'ajax-login'}), {
            method: 'post',
            parameters: postParams,
            onSuccess: this.onSuccessLoginConecta.bind(this)
        });
    },
    
    onSuccessLoginConecta: function(transport) {
        var data = transport.headerJSON;
        $('auth_message').update('');
        
        try {
            if(data.nameuser) {
                this.type = 'conecta';
                this.box.update( transport.responseText );
                
                myLightWindow.deactivate();
            } else {
                $('auth_message').update(data.message);
            }
        } catch(e) {
            this.logoutConecta();
        }
    },
    
    logoutConecta: function() {
        this.waiting(this.box);
        
        new Ajax.Request(this.serverUrl.interpolate({action: 'ajax-logout'}), {
            onSuccess: this.onLogoutConecta.bind(this)
        });
    },
    
    onLogoutConecta: function(transport) {
        this.box.update(transport.responseText);
        
        this.type = 'default';
        
        // Parse facebook tags
        FB.XFBML.Host.parseDomTree();
    },
    
    launchFormConecta: function() {
        if(!lightwindow) {
            throw new Exception('Class lightwindow not found.');
        }
        
        // fix problem with style overflow auto
        lightwindow.prototype._defaultfinalWindowAnimationHandler = function(delay) {
            if (this.windowType == 'media' || this._getParameter('lightwindow_loading_animation')) {	
                // Because of major flickering with the overlay we just hide it in this case
                Element.hide('lightwindow_loading');
                this._handleNavigation(this.activeGallery);
                this._setStatus(false);
            } else {
                Effect.Fade('lightwindow_loading', {
                    duration: 0.75,
                    delay: 1.0, 
                    afterFinish: function() {
                        this._handleNavigation(this.activeGallery);
                        this._defaultGalleryAnimationHandler();
                        this._setStatus(false);
                    }.bind(this),
                    queue: {position: 'end', scope: 'lightwindowAnimation'}
                });
            }
        };                        
        
        myLightWindow.activateWindow({
            href: this.serverUrl.interpolate({action: "ajax-login"}), 
            title: '',
            type: 'page',
            width: 300,
            height: 220
        });
    },
    
    initFb: function() {
        FB.init(this.fbApiKey, "/fb/xd_receiver.html", {
            "ifUserConnected": this.updateFbStatus.bind(this)
        });
    },
    
    updateFbStatus: function() {
        var uid = FB.Connect.get_loggedInUser();
        this.type = 'facebook';
        
        FB.Facebook.apiClient.users_getInfo(uid, ['name', 'first_name', 'last_name', 'proxied_email'], this.onUsersGetInfo.bind(this));
    },
    
    onUsersGetInfo: function(info, ex){
        if(info[0]) {
            var content = '<div class="profile"><div class="profile-pic">' +
                '<fb:profile-pic uid="' + info[0].uid + '" size="normal" width="80" facebook-logo="true"></fb:profile-pic></div>' +
                '<div class="profile-info"><fb:name uid="loggedinuser" useyou="false"></fb:name><br />' +
                info[0].first_name + ' ' + info[0].last_name + '<br /></div><div class="clearer"></div>' +
                '<p><br /><fb:prompt-permission perms="email">'+
                '¿Permitir a Xornal.com enviar mensajes a mi cuenta?</fb:prompt-permission></p>' +
                '<div class="clearer"></div>'+
                '<div class="rightSide"><br /><a href="#" onclick="CommentForm.logoutFb(); return false;">' +
                '<img src="/themes/xornal/images/planConecta/sair.gif" border="0" align="absmiddle" /> Cerrar sesión</a>' +
                '</div></div>';
            
            this.box.update(content);
            
            FB.XFBML.Host.parseDomTree();
        }
    },
    
    logoutFb: function() {
        this.waiting(this.box);
        
        FB.Connect.logout(this.logoutConecta.bind(this));        
    },
    
    waiting: function(container) {
        var content = '<div align="center"><img src="/themes/xornal/images/ajax-loader.gif" border="0" /></div>';
        container.update(content);
    },
    
    saveComment: function(evt) {
        var params = $('comentar').serialize();
        
        new Ajax.Request('/save_comment.php?cacheburst=' + (new Date()).getTime(), {
            'method': 'post',
            'parameters': params,
            'onSuccess': function(transport) {
                try {
                    // save these values before reset form
                    var category = $('category').value;
                    var id       = $('id').value;
                    
                    // clean form
                    $('comentar').reset();
                    
                    // update values for these fields
                    $('category').value = category;
                    $('id').value 	    = id;
                    
                    // Reload img captcha
                    var imgKaptcha = $$('.CImagenKaptcha img');
                    if(imgKaptcha && imgKaptcha[0]) {
                        imgKaptcha = imgKaptcha[0];
                        imgKaptchaSrc = imgKaptcha.src.split('?')[0] + '?' + Math.ceil(Math.random() * 100000);
                        imgKaptcha.setAttribute('src', imgKaptchaSrc);
                    }
                } catch(e) {
                    alert('Hubo un error al intentar guardar su comentario.\nInténtelo de nuevo más tarde.\nDisculpas por las molestias.');
                }
                
                // show message
                alert(transport.responseText);
            },
            
            'onFailure': function() {
                alert('Su comentario no ha sido guardado. Asegúrese de escribir correctamente el código de verificación.');
            }
        });
    }

});
