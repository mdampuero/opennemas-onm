/*jslint debug: true, eqeqeq: false, browser: true, on: true, indent: 4, plusplus: false, white: false */
/**
  * @see http://www.jslint.com/lint.html
  */
(function($){

    $.fn.commentform = function(options) {
        var opts = $.extend({}, $.fn.commentform.defaults, options);        
        $this = $(this);
        opts.elem = $this;
        
        // Singleton
        if($.fn.commentform.__instance__ == null) {
            $.fn.commentform.__instance__ = new CommentFormClass(opts);
        }                
        
        return $.fn.commentform.__instance__;
    };
    
    $.fn.commentform.defaults = {
        url:  '/save_comment.php',
        elem: null
    };
    
    $.fn.commentform.__instance__ = null;
    
    $.fn.commentform.getInstance = function() {
        return $.fn.commentform.__instance__;
    };
    
})(jQuery);


/* Class CommentFormClass */
CommentFormClass = function(options) {
    this.fbAppKey = options.fbAppKey || null;
    this.url      = options.url || null;
    this.elem     = options.elem || null;
    this.form     = options.form || null;
    this.type     = 'static'; // [static | facebook]
    
    this.init();
};

CommentFormClass.prototype = {
    
    init: function() {
        
        // Attach submit event
        $(this.form).submit(
            $.proxy(this, "send")
        );
        
        this.initFb();
    },
    
    validate: function(fields) {                
        var valid = true;
        
        var cssObj = {
            'background-color': '#fee',
            'border': '1px solid #fcc',
            'color' : '#933'
        };
        
        $.each(fields, function() {
            fld = $("#" + this).val();
            
            if(fld.length <= 0) {
                $('#' + this).css(cssObj);
                valid = false;
            }
        });                
        
        return valid;
    },
    
    send: function(event) {
        switch(this.type) {
            case 'static':
                this.sendStatic();
            break;
            
            case 'facebook':
                this.sendFacebook();
            break;
        }
        
        event.preventDefault();
        event.stopPropagation();
    },
    
    sendStatic: function() {
        var fields = ['title', 'textareacomentario', 'nombre', 'email'];
        this.resetStyles(fields);
        
        if( this.validate(fields) ) {
            var params = $('#comentar').serialize();
            
            $.ajax({
                'url': this.url + '?cacheburst=' + (new Date()).getTime(),
                'type': 'POST',
                'data': params,
                
                'context': this,
                'success': this.onSuccessSend,
                'error': function() {
                    this.showMessage('Su comentario <strong>no</strong> ha sido guardado.<br />Asegúrese de cumplimentar correctamente el formulario.');
                }
            });
        }
    },
    
    sendFacebook: function() {
        this.resetStyles(['title', 'textareacomentario']);
        
        if( this.validate(['title', 'textareacomentario']) ) {
            var params = $('#comentar').serialize();
            
            $.ajax({
                'url': this.url + '?cacheburst=' + (new Date()).getTime(),
                'type': 'POST',
                'data': params,
                
                'context': this,
                'success': this.onSuccessSend,
                'error': function() {
                    this.showMessage('Su comentario <strong>no</strong> ha sido guardado.<br />Asegúrese de cumplimentar correctamente el formulario.');
                }
            });
        }
    },    
    
    onSuccessSend: function(data, status, xhr) {
        if(status == '200') {
            // save these values before reset form
            var category = $('#category').val();
            var id       = $('#id').val();
            
            // clean form
            $('#comentar').get(0).reset();
            
            // update values for these fields
            $('#category').val(category);
            $('#id').val(id);
        }
        
        // show message
        this.showMessage(data);
    },
    
    resetStyles: function(fields) {        
        var cssObj = {
            'background-color': '#fff',
            'border': '1px solid #bbb',
            'color' : '#222'
        };
        
        $.each(fields, function() {
            $("#" + this).css(cssObj);
        });
    },
    
    initFb: function() {
        FB.init(this.fbAppKey, "/fb/xd_receiver.html", {
            "ifUserConnected": $.proxy(this, "updateFbStatus")
        });
    },
    
    updateFbStatus: function() {
        var uid = FB.Connect.get_loggedInUser();
        
        FB.Facebook.apiClient.users_getInfo(
            uid,
            ['name', 'first_name', 'last_name', 'proxied_email'],
            $.proxy(this, "onUsersGetInfo")
        );
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
                '<div class="rightSide"><br /><a href="#" onclick="commentForm.logoutFb(); return false;">' +
                '<img src="/themes/lucidity/images/sair.gif" border="0" align="absmiddle" /> Cerrar sesión</a>' +
                '</div></div>';
            
            this.elem.html(content);
            
            this.type = 'facebook';
            
            FB.XFBML.Host.parseDomTree();
        }
    },
    
    logoutFb: function() {
        this.waiting(this.elem);
        
        FB.Connect.logout( $.proxy(this, "logout") );
    },
    
    redrawForm: function() {
        var form = '<div><label for="name">Nombre:</label><input tabindex="3" type="text" name="nombre" id="nombre" /></div>' + 
            '<div><label for="mail">Correo electrónico:</label><input tabindex="4" type="text" name="email" id="email" /></div> ' +
            '<fb:login-button onlogin="commentForm.updateFbStatus();"></fb:login-button>';
        
        this.type = 'static';
        
        // Parse facebook tags
        FB.XFBML.Host.parseDomTree();
    },    
    
    waiting: function(container) {
        var content = '<div align="center"><img src="/themes/lucidity/images/ajax-loader.gif" border="0" /></div>';
        container.html(content);
    },
    
    showMessage: function(msg) {
        $('#form-messages').html(msg).addClass('highlight').fadeOut(5000, function() {
            $(this).html('').removeClass('highlight');
        });
    }
};