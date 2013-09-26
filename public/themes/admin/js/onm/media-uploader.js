/*
 * Onm media uploader handler
 * Author: @frandieguez
 */
;(function($, window, document, undefined) {

    // Contents array, used from all the module to share contents information
    var contents = [];

    var SelectionHandler = function(element, options, parent) {
        this.$elem      = element;
        this.options    = options;
        this.parent     = parent;

        this.selections = {};
    }

    SelectionHandler.prototype = {
        init: function() {
            var _this = this;
            this.$elem.on('click', '.clear-selection', function(e, ui) {
                _this.clear();
            });

            return this;
        },

        add: function(content) {
            if (content.hasOwnProperty('id')) {
                this.selections[content.id] = content;
                this.updateHTML();
            };

            return this;
        },
        toggle: function(content) {
            if (this.selections.hasOwnProperty(content.id)) {
                this.selections.remove(content);
            } else {
                this.selections[content.id] = content;
            };

            this.updateHTML();
        },

        remove: function(content) {
            delete this.selections[content.id];
            this.updateHTML();

            return this;
        },

        clear: function() {
            this.parent.get('browser').reset_selection();
            this.selections = {};
            this.updateHTML();

            return this;
        },

        updateElement: function(content) {

            return this;
        },

        updateHTML: function() {
            var _this = this;

            var template = Handlebars.compile($('#tmpl-attachment-short-info').html());

            var count = 0;
            $.each(this.selections, function(elem) {
                count++;
            });

            html_content = template({
                "count" : this.getCount(),
                "contents": this.getSelections()
            });

            this.$elem.html(html_content);

            return this;
        },

        getCount: function() {
            var count = 0;
            for (elem in this.selections) {
                if (this.selections.hasOwnProperty(elem)) {
                    count++;
                };
            }

            return count;
        },

        getSelections: function() {
            var selections = [];
            for (elem in this.selections) {
                if (this.selections.hasOwnProperty(elem)) {
                    selections.push(this.selections[elem]);
                };
            }

            return selections;
        },

        reset: function() {
            this.clear();
        }
    }

    // The module that allows to browse images and perform searches through them
    var Browser = function(elem, options, parent) {
        this.$browser = $(elem);
        this.config   = options;
        this.parent   = parent;
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
            $(this.$browser).on('click', '.modal-body .attachment', function(e, ui) {
                e.preventDefault();

                var element = $(this).closest('.attachment');
                var content = contents[element.data('id')];

                if (_this.config.multiselect === true && e.ctrlKey) {

                    if (element.hasClass('selected')) {
                        element.removeClass('selected');
                        _this.parent.selection_handler.remove(content);
                    } else {
                        element.addClass('selected');
                        _this.parent.selection_handler.add(content);
                    };

                } else {
                    _this.parent.selection_handler.clear();
                    _this.parent.selection_handler.add(content);

                    _this.reset_selection();
                    element.addClass('selected');
                };

                $(_this.parent.elementUI).trigger('show', content);

                return false;
            });

            $(this.$browser).on('click', '.modal-body .attachment .check', function(e, ui) {
                e.preventDefault();

                var element = $(this).closest('.attachment');
                var content = contents[element.data('id')];

                if (_this.config.multiselect === true) {

                    if (element.hasClass('selected')) {
                        element.removeClass('selected');
                        _this.parent.selection_handler.remove(content);
                    } else {
                        element.addClass('selected');
                        _this.parent.selection_handler.add(content);
                    };

                }

                $(_this.parent.elementUI).trigger('show', content);

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
                        "years": contents_json,
                    });

                    months_input.append(content);
                }
            });
        },

        // Function that fills the browser with images
        load_browser: function (replace, enable_cache) {
            if (typeof replace == "undefined"){
               replace = false;
            }
            if (typeof enable_cache == "undefined"){
               enable_cache = true;
            }

            var browser = this.$browser;
            var _this = this;

            var is_loading = browser.data('loading');
            if (is_loading && !replace) {
                return;
            };

            if (!enable_cache) {
                browser.find('.gallery-search').find('.page').val(1);
            };

            var data = browser.find('.gallery-search').serialize();

            $.ajax({
                url: this.config.browser_url,
                data: data,
                cache : enable_cache,
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
                            "thumbnail_url": element.crop_thumbnail_url,
                            "id": element.id,
                            "is_swf": (element.type_img === 'swf')
                        });
                        content = content.replace('SWF_CALLER', _this.parent.getHTMLforSWF(element, 120, 120))
                        final_content += content;
                    });

                    if (replace) {
                        $(browser).find('.modal-body .attachments').html(final_content);
                    } else {
                        $(browser).find('.modal-body .attachments').append(final_content);
                    }

                    var page_el = $(browser).find('.gallery-search .page');
                    page_el.val(parseInt(page_el.val()) + 1);
                },
                complete: function(xhr, status) {
                    response_contents = $.parseJSON(xhr.responseText);

                    browser.find('.loading').toggleClass('hidden');
                    browser.data('loading', false);

                    // Load next page if there are more contents to load and the
                    // browser windows can fit more contents
                    if (response_contents.length > 0 && _this.browser_needs_load()) {
                        _this.load_browser();
                    }
                }
            });
        },

        reset: function() {
            this.reset_selection();
            this.load_browser();
        },

        reset_selection: function() {
            this.$browser.find('.attachment').removeClass('selected');
        },

        // Function to know if the user has scrolled to the bottom of the container
        // and it needs to load more contents
        browser_needs_load: function() {
            var container = $(this.$browser).find('.modal-body');
            var scrollPosition = container.scrollTop() + container.outerHeight();
            var divTotalHeight = container[0].scrollHeight - 150;
            var isShown = $(this.$browser).find('.modal-body').is(':visible');

            return (scrollPosition >= divTotalHeight) && isShown;
        }
    };

    // Module that handles file uploads
    var Uploader = function(elem, options, parent) {
        this.$uploader = $(elem);
        this.config = options;
        this.parent = parent;
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
            var uploader = $('#fileupload').fileupload()
            .fileupload('option', {
                maxFileSize: _this.parent.maxFileSize,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png|swf)$/i,
                autoUpload : true,
            }).bind('fileuploadadd', function(e, data) {
                _this.$uploader.find('.explanation').hide();
                _this.$uploader.find('#fileupload .messages').show();
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
                _this.reset();
                _this.parent.elementUI.reset();
                _this.parent.show('browser');
                _this.parent.browser.load_browser(true, false);
            });

            return this;
        },
        reset : function () {
            this.$uploader.find('#fileupload .explanation').show();
            this.$uploader.find('.dropzone .files').html('');
        }
    };

    // Module that handles the element showing page
    var ElementUI = function(elem, options, parent) {
        this.$element = $(elem);
        this.config = options;
        this.parent = parent;
    };

    ElementUI.prototype = {
        init : function() {
            var _this = this;

            $(this).on('show', function(event, content) {
                _this.content = content;
                content.edit_url = '/admin/images/show?id[]='+content.id;
                content.is_swf = (content.type_img === 'swf');

                var template = Handlebars.compile($('#tmpl-show-element').html());
                html_content = template({
                    "content": content
                });

                // If the element is a flash object we have to do this hack
                // as Handlebars escapes html in variables
                if (content.is_swf) {
                    html_content = html_content.replace('SWF_CALLER', _this.parent.getHTMLforSWF(content))
                };

                _this.$element.find('.body').html(html_content);
                _this.parent.$elem.find('.assign_content').removeClass('disabled');
            });

            return this;
        },

        reset: function() {
            this.$element.find('.body').html('');
            this.parent.$elem.find('.assign_content').addClass('disabled');
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
            uploader_el: '#uploader',
            browser_el: '#browser',
            media_element_el: '#media-element-show',
            selections_el: '#selections',
            maxFileSize: 5000000,
            multiselect: false
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
            this.initSelectionHandler();

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
                backdrop: true, //Show a grey back drop
                keyboard: true, //Can close on escape
                show: this.config.initially_shown,
            })
            this.modal.on('show', function(e, ui) {
                _this.get('browser').reset();
                _this.get('uploader').reset();
                _this.get('elementUI').reset();
                _this.get('selection_handler').reset();
            })
        },

        initHandlers: function() {
            var _this = this;

            $('[data-toggle="modal"]').on('click', function() {
                _this.position = $(this).data('position');
                _this.config.multiselect = !!($(this).data('multiselect'));
            })

            // If it was passed handlers for actions register them
            $.each(this.config.handlers, function(key, handler) {
                _this.$elem.on(key, handler);
            })

            // Register the default assign_content handler, hides the modal
            this.$elem.on('assign_content', function(event, params) {
                _this.modal.modal('hide');
            });

            _this.$elem.find('.assign_content').on('click', function(e, ui) {
                console.log('clicked');
                e.preventDefault();

                var params = {};

                var sel_handler = _this.get('selection_handler');

                // If multiselect is activated return an array othewise
                // return only one element
                if (_this.config.multiselect) {
                    var selection = sel_handler.getSelections();
                } else {
                    var selection = sel_handler.getSelections()[0];
                }

                _this.assignImage(selection, params);

                return false;
            });
        },

        get: function(name) {
            return this[name];
        },

        show:  function(component) {
            this.$elem.find('a[href="#'+component+'"]').tab('show');
        },

        initUploader: function() {
            element       = this.$elem.find(this.config.uploader_el);
            this.uploader = new Uploader(element, this.config, this).init();
        },

        initBrowser: function() {
            element      = this.$elem.find(this.config.browser_el);
            this.browser = new Browser(element, this.config, this).init();
        },

        initShowElement: function() {
            element        = this.$elem.find(this.config.media_element_el);
            this.elementUI = new ElementUI(element, this.config, this).init();
        },

        initSelectionHandler: function() {
            element = this.$elem.find(this.config.selections_el);
            this.selection_handler = new SelectionHandler(element, this.config, this).init();
        },

        assignImage: function(content, params) {
            var position = this.get('position');

            var params = $.extend({}, params, { 'position': position, 'content' : content});

            console.log(params)
            this.$elem.trigger('assign_content', params);
        },

        buildHTMLElement: function(params, use_thumbnail) {
            var html = '';

            if (typeof(use_thumbnail) == undefined) {
                use_thumbnail = false;
            };

            var align = '';
            if (params.hasOwnProperty('position') && params['position'] !== undefined) {
                align = 'align="'+params['alignment']+'"'
            };

            var description = '';
            if (params.hasOwnProperty('description') && params['description'] !== undefined) {
                description = 'alt="'+params['description']+'"'
            };

            var class_image = ''
            if (params.hasOwnProperty('class_image') && params.class_image !== false) {
                class_image = 'class="image"';
            };

            if (use_thumbnail) {
                var src = 'src="'+params.content.thumbnail_url+'"';
            } else{
                var src = 'src="'+params.content.image_path+'"';
            };
            html = '<img '+src+' '+align+' '+description+' '+class_image+' />';

            return html;
        },

        getHTMLforSWF: function(element, width, height) {
            if (typeof width == 'undefined') {
                width = '100%';
            };

            if (typeof height == 'undefined') {
                height = '100%';
            };

            var string = '<div id="flash-container-replace'+element.id+'"></div>'+
                '<scr' + 'ipt>'+
                    'var flashvars = {};'+
                    'var params = { wmode: "opaque" };'+
                    'var attributes = { };'+
                    'swfobject.embedSWF("'+element.image_path+'", "flash-container-replace'+element.id+'", "'+width+'", "'+height+'", "9.0.0", false, flashvars, params, attributes);'+
                '</sc'+'ript>';
            return string;
        },
    }

    MediaPicker.defaults = MediaPicker.prototype.defaults;

    $.fn.mediaPicker = function(options) {
        return this.each(function() {
            new MediaPicker(this, options).init();
        });
    };

    //optional: window.MediaPicker = MediaPicker;

})(jQuery, window, document);
