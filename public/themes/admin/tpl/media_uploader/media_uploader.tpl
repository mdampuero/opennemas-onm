{extends file="base/admin.tpl"}


{block name="content"}
<div class="modal hide tabbable tabs-left" id="media-uploader">

    <ul class=" nav nav-tabs modal-sidebar full-height">
        <li><a href="#upload"  data-toggle="tab">{t}Upload{/t}</a></li>
        <li class="active"><a href="#gallery"  data-toggle="tab">{t}Browse elements{/t}</a></li>
        <li><a href="#media-element-show" class="hidden" data-toggle="tab">{t}Show element info{/t}</a></li>
    </ul>
    <div class="tab-content modal-content full-height">

        <div id="gallery" class="tab-pane full-height active">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 class="modal-title">{t}Media gallery{/t}</h3>
            </div>
            <div class="modal-body full-height">
                <form action="#" class="gallery-search">
                <div class="toolbar clearfix">
                    <div class="pull-left">
                        <select name="month" id="">
                            <option value="">{t}Month{/t}</option>
                            {html_options options=$months}
                        </select>
                    </div>
                    <div class="pull-right">
                        <input type="search" name="search_string" placeholder="{t}Search{/t}">
                    </div>
                </div>
                <div class="modal-body-content">
                    <ul class="attachments ui-sortable ui-sortable-disabled"></ul>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="image-info pull-left"></div>
                <!-- <div class="buttons pull-right"></div> -->
            </div>
        </div>

        <div id="media-element-show" class="tab-pane full-height">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title">{t}Insert image{/t}</h3>
            </div>
            <div class="modal-body full-height">
                <div class="toolbar clearfix">
                    <div class="pull-right btn-toolbar">
                        <div class="btn-group">
                            <a href="#" class="btn"><i class="icon icon-pencil"></i> {t}Edit{/t}</a>
                        </div>
                        <div class="btn-group">
                            <a href="#" class="btn"><i class="icon icon-share"></i></a>
                            <a href="#" class="btn"><i class="icon icon-wrench"></i></a>
                        </div>
                    </div>
                    <div class="buttons pull-left">
                        <a href="#" class="back-to-browse btn">{t}Back to list{/t}</a>
                    </div>
                </div>
                <div class="body"></div>
            </div>
            <div class="modal-footer">
                <div class="pull-right buttons">
                    <a class="btn btn-primary yes" href="#">{t}Insert into article{/t}</a>
                </div>
            </div>
        </div>

        <div id="upload" class="tab-pane full-height">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 class="modal-title">{t}Upload new media{/t}</h3>
            </div>
            <div class="modal-body full-height">
                <div class="upload-content">
                    <h3 class="upload-instructions drop-instructions">{t}Drop files anywhere to upload{/t}</h3>
                    <a href="#" class="btn btn-large load-files-button">{t}Select Files{/t}</a>
                    <input type="file" name="files" multiple id="files" class="hidden">
                </div>
            </div>
        </div>

    </div>

</div>
{/block}


{block name="footer-js" append}
{script_tag src="/libs/handlebars.js" common=1}
<script>
var contents = [];
function load_browser (data, page, replace) {
    $.get(
        '{url name="admin_media_uploader_browser"}?'+data
    ).success(function(data) {
        var template = Handlebars.compile($('#tmpl-attachment').html());
        var final_content = '';
        $.each(data, function(index, element) {
            contents[element.id] = element
            content = template({
                "thumbnail_url" : element.thumbnail_url,
                "id" : element.id,
            });
            final_content += content;
        });
        if (replace) {
            $('.attachments').html(final_content);
        } else {
            $('.attachments').append(final_content);
        }
    });
}
jQuery(document).ready(function($) {
    jQuery("#media-uploader").modal({
        backdrop: 'static', //Show a grey back drop
        keyboard: true, //Can close on escape
        show: true,
    });
    load_browser ('', 1, 1, true);

    $('#gallery').on('mouseenter', '.attachment img', function(e, ui) {
        var element = $(this).closest('.attachment');

        var template = Handlebars.compile($('#tmpl-attachment-short-info').html());

        content = contents[element.data('id')];

        html_content = template({
            "content" : content,
        });
        $('.image-info').append(html_content);

    }).on('mouseout', '.attachment img', function(e, ui){
        $('.image-info').html('');
    }).on('click', '.attachment img', function(e, ui){
        var element = $(this).closest('.attachment');

        var template = Handlebars.compile($('#tmpl-show-element').html());

        content = contents[element.data('id')];

        html_content = template({
            "content" : content,
        });
        $('#media-element-show .body').html(html_content);

        $('#media-uploader a[href="#media-element-show"]').tab('show');
    });

    $('.back-to-browse').on('click', function(e, ui){
        $('#media-uploader a[href="#gallery"]').tab('show');
    });
    $('.gallery-search').on('submit', function(e, ui) {
        e.preventDefault();
        var data = $(this).serialize();
        load_browser(data, 1, true);
    });
    $('#media-uploader #upload .load-files-button').on('click', function(e, ui){
        $('#media-uploader #upload input#files').trigger('click');
    });

});
</script>

<script type="text/html" id="tmpl-attachment">
{literal}
    <li class="attachment save-ready" data-id="{{id}}">
        <div class="attachment-preview type-image subtype-png landscape">
            <div class="thumbnail">
                <div class="centered">
                    <img src="{{thumbnail_url}}" draggable="false">
                </div>
            </div>
            <a class="check" href="#" title="Deselect"><div class="media-modal-icon"></div></a>
        </div>
    </li>
{/literal}
</script>

<script type="text/html" id="tmpl-attachment-short-info">
{literal}
    {{#with content}}
        {{#if description}}
        <strong>{{description}}</strong>
        {{else}}
        <strong>{/literal}{t}No description{/t}{literal}</strong>
        {{/if}} <br>
        {{width}} x {{height}}, {{size}} kb
    {{/with}}
{/literal}
</script>

<script type="text/html" id="tmpl-show-element">
{literal}
{{#with content}}
<div class="photo-image-information pull-left clearfix">
    <div class="thumbnail">
        <img src="{{image_path}}" />
    </div>

    <br>

    <div class="well well-small">
        <div><strong>{/literal}{t}Original filename:{/t}{literal}</strong> {{title}}</div>
        <div><strong>{/literal}{t}Resolution:{/t}{literal}</strong> {{width}} × {{height}}</div>
        <div><strong>{/literal}{t}Size:{/t}{literal}</strong> {{size}} Kb</div>
    </div>
</div>

<div class="photo-insert-form pull-left">
    <h5>Attachment details</h5>
    <div class="control-group">
        <label for="caption" class="control-label">{/literal}{t}Caption{/t}{literal}</label>
        <div class="controls">
            <textarea required="required" id="caption" name="caption"  class="input-xlarge"
                rows="2">{{description}}</textarea>
        </div>
    </div>
    <hr>
    <h5>Attachment display settings</h5>
    <div class="control-group">
        <label for="alignment" class="control-label">{/literal}{t}Alignment{/t}{literal}</label>
        <div class="controls">
            <select class="alignment" data-setting="align" data-user-setting="align">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
                <option value="none" selected="">None</option>
            </select>
        </div>
    </div>
</div><!-- /basic -->
{{/with}}
{/literal}
</script>
{/block}

{block name="header-css" append}
<style>
    #media-uploader .full-height {
        min-height:100%;height:100%;
    }
    #media-uploader {
        width:96% !important;
        height:90% !important;
        top:5%;
        left:2%;
        right:2%;
        margin-left:0;
    }
    #media-uploader .modal-content {
        margin-left:200px;
        position:relative;
        overflow: hidden;
    }
    #media-uploader .modal-footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 13px 0;
        height: 30px;
    }
    #media-uploader .modal-footer .buttons {
        margin-right:10px;
    }
    #media-uploader .modal-body {
        padding: 0;
        position: absolute;
        right: 0px;
        left: 0px;
        max-height: 69%;
        min-height: 83%;
    }
    #media-uploader .modal-sidebar {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 199px;
        border-right: 1px solid #eee;
    }
    #media-uploader .modal-body-content {
        max-height:100%;
        height:100%;
        position:relative;
        margin:10px;
    }
    #media-uploader .modal-body-content-wrapper {
        overflow-y:visible;
        position:absolute;
        right:0px;
    }
    #media-uploader .toolbar {
        border-bottom:1px solid #eee;
        padding:10px;
    }
    #media-uploader .toolbar .btn-toolbar {
        margin:0;
    }
    #media-uploader .hidden {
        display:none;
    }
    #media-uploader .modal-sidebar ul {
        margin:20px 0 10px 20px;
        list-style:none;
    }
    #media-uploader .modal-sidebar li {
        list-style:none;
        margin-bottom:10px;
    }

    #media-uploader .modal-sidebar li.active a {
        color:Black;
    }

    #media-uploader #gallery .attachments {
        margin:20px 0 10px 20px;
        list-style:none;
        margin:0;
    }
    #media-uploader #gallery .attachments .attachment {
        float: left;
        list-style:none;
    }
    #media-uploader #gallery .attachments .attachment-preview {
        display:inline-block;
        list-style:none;
        margin-bottom:10px;
    }
    #media-uploader #media-element-show .body {
        margin:10px;
    }
    #media-uploader .modal-sidebar li:first-child {
        margin-top:30px;
    }
    #media-uploader .modal-sidebar a {
        padding: 4px 0 4px 25px;
        margin: 0;
        line-height: 18px;
        font-size: 14px;
        color: #21759b;
        text-shadow: 0 1px 0 #fff;
        text-decoration: none;
        background:none;
        border:none;
    }
    #media-uploader .modal-footer .image-info {
        margin-left: 10px;
        text-align: left;
        font-size:10px;
        line-height:12px;
    }
    #media-uploader .modal-footer .image-info strong {
        font-size:12px;
    }

    #media-uploader #gallery .thumbnail {
        display:inline-block;
        width:120px;
        height:120px;
        margin: 0 7px 2px 0;
        padding:0;
        border-radius:0;
    }


    #media-uploader #gallery .thumbnail {
        -moz-transition: none;
        -webkit-transition: none;
        -o-transition: color 0 ease-in;
        transition: none;
    }
    #media-uploader #gallery .thumbnail:active {
        box-shadow: 0 0 0 0px #fff,0 0 0 5px #1e8cbe;
        /*margin:-5px;*/
    }

    #media-uploader .upload-content {
        text-align: center;
        margin-top: 10%;
    }
    #media-uploader .upload-content h3 {
        font-size:1.1em;
    }
    #media-uploader .upload-content a {
        padding: 13px 26px;
        font-size: 1.1em;
    }
    #media-uploader .photo-insert-form {
    }
    #media-uploader .photo-image-information {
        border-right:1px solid #eee;
        max-width:300px;
        margin-right: 10px;
        padding-right:10px;
    }
    #media-uploader #media-element-show h5 {
        margin-top:0;
    }
</style>
{/block}