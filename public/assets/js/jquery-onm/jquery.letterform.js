
/**
 * Letter form jQuery plugin
 *
 * @param options {form: HTMLFormElement}
 * @returns LetterFormClass    instance of LetterFormClass
 */
(function($){

    $.fn.letterform = function(options) {
        var opts = $.extend({}, $.fn.letterform.defaults, options);
        $this = $(this);
        opts.elem = $this;

        // Singleton
        if($.fn.letterform.__instance__ == null) {
            $.fn.letterform.__instance__ = new LetterFormClass(opts);
        }

        return $.fn.letterform.__instance__;
    };

    $.fn.letterform.defaults = {

        elem: null
    };

    $.fn.letterform.__instance__ = null;

    $.fn.letterform.getInstance = function() {
        return $.fn.letterform.__instance__;
    };

})(jQuery);


LetterFormClass = function(options) {
    this.url      = options.url || null;
    this.elem     = options.elem || null;
    this.form     = options.form || null;

    this.init();
};

LetterFormClass.prototype = {
    init: function() {
        // Attach submit event to form
        $(this.form).submit(
            $.proxy(this, "send")
        );
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
        var fields = ['subject', 'lettertext', 'name', 'mail'];

        this.resetStyles(fields);

        if( this.validate(fields) ) {
          /*  var params = $('#send_letter').serialize();

            $.ajax({
                'url': '/cartas-al-director/save/?cacheburst=' + (new Date()).getTime(),
                'type': 'POST',
                'data': params,

                'context': this,
                'success': this.onSuccessSend,
                'error': function() {
                    this.showMessage('Su Carta al Director <strong>no</strong> ha sido guardada.<br />' +
                                     'Asegúrese de cumplimentar correctamente el formulario.', 'error');
                }
            });
        */
        } else {
            this.showMessage('Por favor, cumplimente correctamente los campos del formulario.', 'error');
        }

      //  event.preventDefault();
        this.onSuccessSend();
        event.stopPropagation();
    },

    onSuccessSend: function(data, status, xhr) {
        if(status == 'success') {
            // clean form
            $('#send_letter').get(0).reset();
        }

        // show message
        this.showMessage(data, 'notice');

       // location.href= '/cartas-al-director';
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



    waiting: function(container) {
        var content = '<div align="center"><img src="/themes/lucidity/images/ajax-loader.gif" border="0" /></div>';
        container.html(content);
    },

    showMessage: function(msg, level) {
        $('#form-messages').html(msg)
                           .addClass(level)
                           .show()
                           .animate({opacity: 1.0}, 5000)
                           .fadeOut(2000, function() {
                                $(this).html('').removeClass();
                           });
    }
};
