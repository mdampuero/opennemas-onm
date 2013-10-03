/*
 * Onm media uploader handler
 * Author: @frandieguez
 */
;(function($, window, document, undefined) {

    // Contents array, used from all the module to share contents information
    var contents = [];

    // The module that allows to browse images and perform searches through them
    var Browser = function(elem, options) {
        this.$browser = $(elem);
        this.config = options;
    };

    Browser.prototype = {
        init : function() {
            var _this = this;

            this.init_months();
            // Load contents to fill the browser
            this.load_browser();

            $(this.$browser).find('.modal-body').on('scroll', function() {
                if (_this.browser_needs_load()) {
                    _this.load_browser(false);
                }
            });

            // When changing the month or perform a search with the search input
            // load the browser with the searched contents
            $(this.$browser).on('change', '.gallery-search .month', function(e, ui) {
                $('.gallery-search').trigger('submit');
            }).on('submit', '.gallery-search', function(e, ui) {
                e.preventDefault();

                // Reset actual page
                $(_this.$browser).find('.gallery-search .page').val(1);
                _this.load_browser(true);
            });

            // Attach events to the images in browser
            $(this.$browser).on('mouseenter', '.attachment img', function(e, ui) {
                var element = $(this).closest('.attachment');

                var template = Handlebars.compile($('#tmpl-attachment-short-info').html());

                content = contents[element.data('id')];

                html_content = template({
                    "content": content,
                });
                $('.image-info').append(html_content);
            }).on('mouseout', '.attachment img', function(e, ui) {
                $('.image-info').html('');
            }).on('click', '.attachment', function(e, ui) {
                e.preventDefault();
                var element = $(this).closest('.attachment');
                content = contents[element.data('id')];

                $(_this.mediapicker.elementUI).trigger('show', content);

                return false;
            });

            return this;
        },

        init_months : function() {
            var months_input = this.$browser.find('.gallery-search .month');
            $.ajax({
                url: this.config.months_url,
                success: function(contents_json) {
                    var template = Handlebars.compile($('#tmpl-browser-months').html());
                    content = template({
                        "months": contents_json,
                    });

                    months_input.append(content);
                }
            });
        },

        // Function that fills the browser with images
        load_browser: function (replace) {
            var browser = this.$browser;
            var _this = this;

            var is_loading = browser.data('loading');
            if (is_loading) {
                return;
            };

            var data = $('.gallery-search').serialize();

            $.ajax({
                url: this.config.browser_url,
                data: data,
                beforeSend: function() {
                    browser.find('.loading').removeClass('hidden');
                    browser.data('loading', true);
                },
                success: function(contents_json) {
                    var template = Handlebars.compile($('#tmpl-attachment').html());
                    var final_content = '';
                    $.each(contents_json, function(index, element) {
                        contents[element.id] = element
                        content = template({
                            "thumbnail_url": element.thumbnail_url,
                            "id": element.id,
                        });
                        final_content += content;
                    });

                    if (replace) {
                        $(browser).find('.attachments').html(final_content);
                    } else {
                        $(browser).find('.attachments').append(final_content);
                    }

                    var page_el = $(browser).find('.gallery-search .page');
                    page_el.val(parseInt(page_el.val()) + 1);
                },
                complete: function(xhr, status) {
                    var contents = $.parseJSON(xhr.responseText);

                    browser.find('.loading').toggleClass('hidden');
                    browser.data('loading', false);

                    // Load next page if there are more contents to load and the
                    // browser windows can fit more contents
                    if (contents.length > 0 && _this.browser_needs_load()) {
                        _this.load_browser(false);
                    }
                }
            });
        },

        // Function to know if the user has scrolled to the botton of the container
        // and it needs to load more contents
        browser_needs_load: function() {
            var container = $(this.$browser).find('.modal-body');
            var scrollPosition = container.scrollTop() + container.outerHeight();
            var divTotalHeight = container[0].scrollHeight - 150;
            var isShown = $(this.$browser).find('.modal-body').is(':visible');

            return (scrollPosition >= divTotalHeight) && isShown;
        },

        setParent: function(parent) {
            this.mediapicker = parent;
        }
    };

    // Module that handles file uploads
    var Uploader = function(elem, options) {
        this.$uploader = $(elem);
        this.config = options;
    };

    Uploader.prototype = {
        init : function() {
            var _this = this;

            // Enable iframe cross-domain access via redirect page:
            var redirectPage = window.location.href.replace(
                /\/[^\/]*$/,
                '/cors/result.html?%s'
            );

            // Initialize the jQuery File Upload widget:
            $('#fileupload').fileupload();
            $('#fileupload').fileupload('option', {
                maxFileSize: 5000000,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png|swf)$/i,
                autoUpload : true,
            }).bind('fileuploadadd', function(e, data) {
                $('.explanation').hide();
                $('#fileupload .messages').show();
            }).bind('fileuploadsend', function (e, data) {
                if (data.dataType.substr(0, 6) === 'iframe') {
                    var target = $('<a/>').prop('href', data.url)[0];
                    if (window.location.host !== target.host) {
                        data.formData.push({
                            name: 'redirect',
                            value: redirectPage
                        });
                    }
                }
            }).bind('fileuploaddone', function (e, data){
                // Things to do after all files were uploaded.
                $('#fileupload .messages').hide();
                _this.mediapicker.browser.load_browser(true);
            });

            return this;
        },

        setParent: function(parent) {
            this.mediapicker = parent;
        }
    };

    // Module that handles the element showing page
    var ElementUI = function(elem, options) {
        this.$element = $(elem);
        this.config = options;
    };

    ElementUI.prototype = {
        init : function() {
            var _this = this;

            $(this.$element).on('click', '.back-to-browse', function(e, ui) {
                $('#media-uploader a[href="#gallery"]').tab('show');
            });

            $(this).on('show', function(event, content) {
                $(this.$element).find('.edit-image-button').attr('href', '/admin/images/show?id[]='+content.id);

                var template = Handlebars.compile($('#tmpl-show-element').html());
                html_content = template({
                    "content": content,
                });

                $('#media-element-show .body').html(html_content);

                $('#media-uploader a[href="#media-element-show"]').tab('show');
            });


            $(this.$element).find('.assign_content').on('click', function(e, ui) {
                e.preventDefault();

                var params = {};
                params['description'] = _this.$element.find('#caption').val();
                params['alignment'] = _this.$element.find('.alignment').val();

                _this.assignImage(content, params);

                return false;
            }
)
            return this;
        },

        assignImage: function(content, params) {
            var position = this.mediapicker.get('position');

            var params = $.extend({}, params, { 'position': position, 'content' : content});

            this.mediapicker.$elem.trigger('assign_content', params);
        },

        setParent: function(parent) {
            this.mediapicker = parent;
        }
    };

    // our plugin constructor
    var MediaPicker = function(elem, options) {
        this.elem = elem;
        this.$elem = $(elem);
        this.options = options;
        this.metadata = this.$elem.data('mediapicker');
    };

    // the plugin prototype
    MediaPicker.prototype = {
        defaults: {
            initially_shown: false,
            uploader_el: '#upload',
            browser_el: '#gallery',
            media_element_el: '#media-element-show',
        },

        init: function() {
            var _this = this;

            // Introduce defaults that can be extended either
            // globally or using an object literal.
            this.config = $.extend({}, this.defaults, this.options, this.metadata);

            // Init components
            this.initUploader();
            this.initBrowser();
            this.initShowElement();

            // Load the UI
            this.initModal();

            this.$elem.data('mediapicker', this);

            this.initHandlers();

            return this;
        },

        initModal: function() {
            var _this = this;

            var gallery = $('#media-uploader a[href="#gallery"]');
            this.modal = this.$elem.modal({
                backdrop: 'static', //Show a grey back drop
                keyboard: true, //Can close on escape
                show: this.config.initially_shown,
            })
            // .on('shown', function() {
            //     $('#media-uploader a[href="#gallery"]').tab('show');
            // })
        },

        initHandlers: function() {
            var _this = this;

            $('[data-toggle="modal"]').on('click', function() {
                _this.position = $(this).data('position');
            })

            // If it was passed handlers for actions register them
            $.each(this.config.handlers, function(key, handler) {
                _this.$elem.on(key, handler);
            })

            // Register the default assign_content handler, hides the modal
            this.$elem.on('assign_content', function(event, params) {
                _this.modal.modal('hide');
            });
        },

        get: function(name) {
            return this[name];
        },

        initUploader: function() {
            element       = this.$elem.find(this.config.uploader_el);
            this.uploader = new Uploader(element, this.config).init();
            this.uploader.setParent(this);
        },

        initBrowser: function() {
            element      = this.$elem.find(this.config.browser_el);
            this.browser = new Browser(element, this.config).init();
            this.browser.setParent(this);
        },

        initShowElement: function() {
            element        = this.$elem.find(this.config.media_element_el);
            this.elementUI = new ElementUI(element, this.config).init();
            this.elementUI.setParent(this);
        },

        buildHTMLElement: function(params) {
            var html = '';

            var align = '';
            if (params.hasOwnProperty('position') && params['position'] !== undefined) {
                align = 'align="'+params['position']+'"'
            };

            var description = '';
            if (params.hasOwnProperty('description') && params['description'] !== undefined) {
                description = 'alt="'+params['description']+'"'
            };

            var src = 'src="'+params.content.image_path+'"';

            html = '<img '+src+' '+align+' '+description+' class="image" />';

            return html;
        }
    }

    MediaPicker.defaults = MediaPicker.prototype.defaults;

    $.fn.mediaPicker = function(options) {
        return this.each(function() {
            new MediaPicker(this, options).init();
        });
    };

    //optional: window.MediaPicker = MediaPicker;

})(jQuery, window, document);
