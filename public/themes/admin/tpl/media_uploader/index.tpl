{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/swfobject.js"}
{/block}

{block name="header-css" append}
{css_tag href="/jquery/colorbox.css" media="screen"}
<style type="text/css">
    div#content-provider .content-provider-block .content-provider-element {
        margin: 5px;
        border: 1px solid #AAA;
        padding: 5px;
        background:
        white;
    }
    .content-provider-element .content-action-buttons,
    .content-provider-element input[type="checkbox"] {
        display:none;
    }
</style>
{/block}

{block name="footer-js" append}

    {include file="media_uploader/media_uploader.tpl"}
    <script>
    $(function() {
        $('#media-uploader').mediaPicker({
            upload_url: "{url name=admin_image_create category=0}",
            browser_url : "{url name=admin_media_uploader_browser}",
            months_url : "{url name=admin_media_uploader_months}",
            initially_shown:  true,
            handlers: {
                'assign_content' : function( event, params ) {
                    var mediapicker = $(this).data('mediapicker');

                    if (params['position'] == 'body') {
                        var element = mediapicker.buildHTMLElement(params);
                        CKEDITOR.instances.body.insertHtml(element);
                    } else {
                        //
                    };

                }
            }
        });
    });
    </script>
{/block}

{block name="content"}

{/block}
