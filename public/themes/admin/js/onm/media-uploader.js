/*
 * Onm media uploader handler
 * Author: @frandieguez
 */
;(function($, window, document, undefined) {

    var Browser = function(elem, options) {
        this.$browser = $(elem);
        this.config = options;
    };

    Browser.prototype = {
        init : function() {
            var self = this;

            this.load_browser();

            $(this.$browser).find('.modal-body').on('scroll', function() {
                if (self.browser_needs_load()) {
                    self.load_browser(false);
                }
            });

            $(this.$browser).on('change', '.gallery-search .month', function(e, ui) {
                $('.gallery-search').trigger('submit');
            })
            $(this.$browser).on('submit', '.gallery-search', function(e, ui) {
                e.preventDefault();

                // Reset actual page
                $(self.$browser).find('.gallery-search .page').val(1);
                self.load_browser(true);
            });

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
            }).on('click', '.attachment img', function(e, ui) {
                var element = $(this).closest('.attachment');

                var template = Handlebars.compile($('#tmpl-show-element').html());

                content = contents[element.data('id')];

                html_content = template({
                    "content": content,
                });
                $('#media-element-show .body').html(html_content);

                $('#media-uploader a[href="#media-element-show"]').tab('show');
            });
        },
        load_browser: function (replace) {
            var browser = this.$browser;
            var self = this;

            var is_loading = browser.data('loading');
            if (is_loading) {
                return;
            };

            var data = $('.gallery-search').serialize();

            browser.find('.loading').toggleClass('hidden');
            browser.data('loading', true);

            $.ajax({
                url: this.config.browser_url,
                data: data,
                // async: false,
                beforeSend: function() {
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
                complete: function() {
                    browser.find('.loading').toggleClass('hidden');
                    browser.data('loading', false);

                    if (self.browser_needs_load()) {
                        self.load_browser(false);
                    }
                }
            });
        },
        browser_needs_load: function() {
            var container = $(this.$browser).find('.modal-body');
            var scrollPosition = container.scrollTop() + container.outerHeight();
            var divTotalHeight = container[0].scrollHeight - 150;

            return scrollPosition >= divTotalHeight;
        }
    };

    var Uploader = function(elem, options) {
        this.$uploader = $(elem);
        this.config = options;
    };

    Uploader.prototype = {
        init : function() {
            // Add click handler for upload button
            $(this.$uploader).on('click', '.load-files-button', function(e, ui) {
                $(self.$uploader, '#upload input#files').trigger('click');
            });
        }
    };

    // our plugin constructor
    var MediaPicker = function(elem, options) {
        this.elem = elem;
        this.$elem = $(elem);
        this.options = options;
        this.metadata = this.$elem.data('mediapicker');
        var self = this;
    };

    // the plugin prototype
    MediaPicker.prototype = {
        defaults: {
            initially_shown: false,
        },

        init: function() {
            // Introduce defaults that can be extended either
            // globally or using an object literal.
            this.config = $.extend({}, this.defaults, this.options,
                this.metadata);

            this.initModal();

            this.initUploader();
            this.initBrowser();
            this.initShowElement();

            return this;
        },

        initModal: function() {
            jQuery(this.$elem).modal({
                backdrop: 'static', //Show a grey back drop
                keyboard: true, //Can close on escape
                show: this.config.initially_shown,
            })
        },

        initUploader: function() {
            element = this.$elem.find('#upload');
            this.uploader = new Uploader(element, this.config).init();
        },

        initBrowser: function() {
            var self = this;

            element = this.$elem.find('#gallery');
            this.browser = new Browser(element, this.config).init();
        },

        initShowElement: function() {
            $(this.$elem).on('click', '.back-to-browse', function(e, ui) {
                $('#media-uploader a[href="#gallery"]').tab('show');
            });
        }
    }

    MediaPicker.defaults = MediaPicker.prototype.defaults;

    $.fn.mediaPicker = function(options) {
        return this.each(function() {
            new MediaPicker(this, options).init();
        });
    };

    //optional: window.Plugin = Plugin;

})(jQuery, window, document);