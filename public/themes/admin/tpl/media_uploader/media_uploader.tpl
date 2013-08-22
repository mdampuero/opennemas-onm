{extends file="base/admin.tpl"}


{block name="content"}
<div class="wrapper-content" style="margin-top:100px">
    <a href="#media-uploader" data-keyboard="true" data-toggle="modal" class="btn btn-primary">Show modal</a>
</div>
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
            <div class="modal-body">
                <form action="#" class="gallery-search">
                <input type="hidden" class="page" name="page" value="1">
                <div class="toolbar clearfix">
                    <div class="pull-left">
                        <select name="month" class="month">
                            <option value="">{t}All months{/t}</option>

                        </select>
                    </div>
                    <div class="pull-right">
                        <input type="search" name="search_string" placeholder="{t}Search{/t}">
                    </div>
                    <div class="loading pull-right hidden">{t}Loading...{/t}</div>
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
            <div class="modal-body">
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
            <div class="modal-body">
                <div id="dropzone">
                    <form id="fileupload" action="{url name=admin_image_create}" method="POST" enctype="multipart/form-data">
                        <div class="toolbar clearfix">
                            <div class="fileupload-buttonbar pull-left">
                                <div class="btn-group">
                                    <div class="btn fileinput-button input-hidden">
                                        {t}Select Files{/t}
                                        <input type="file" name="files[]" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table condensed">
                            <tbody class="files">
                                <div class="explanation">{t}Drop files anywhere here to upload or click on the "Select Files" button above.{/t}</div>
                            </tbody>
                        </table>
                    </form>
                </div><!-- end #dropzone -->
            </div>
        </div>

    </div>

</div>
{/block}


{block name="footer-js" append}
<!-- The Templates and Load Image plugins are included for the FileUpload user interface -->
<script src="{$params.JS_DIR}/jquery/tmpl.min.js"></script>
<!-- The Templates and Load Image plugins are included for the FileUpload user interface -->
<script src="{$params.JS_DIR}/jquery/load-image.min.js"></script>
<script src="{$params.JS_DIR}/jquery/bootstrap-image-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="{$params.JS_DIR}/jquery/jquery.iframe-transport.js" defer=defer></script>
{script_tag src="/jquery/jquery.fileupload.js" common=1 defer=defer}
{script_tag src="/jquery/jquery.fileupload-ui.js" common=1 defer=defer}

{script_tag src="/libs/handlebars.js" common=1}
{script_tag src="/onm/media-uploader.js"}
<script>
    var fileUploadErrors = {
        maxFileSize: '{t}File is too big{/t}',
        minFileSize: '{t}File is too small{/t}',
        acceptFileTypes: '{t}Filetype not allowed{/t}',
        maxNumberOfFiles: '{t}Max number of files exceeded{/t}',
        uploadedBytes: '{t}Uploaded bytes exceed file size{/t}',
        emptyResult: '{t}Empty file upload result{/t}'
    };
    $(function() {
        $('#media-uploader').mediaPicker({
            upload_url: "{url name=admin_image_create category=0}",
            browser_url : "{url name=admin_media_uploader_browser}",
            months_url : "{url name=admin_media_uploader_months}",
            initially_shown:  true,
        });
    });
</script>

<script type="text/html" id="tmpl-browser-months">
{literal}
    {{#each months}}
    <option value="{{value}}">{{name}}</option>
    {{/each}}
{/literal}
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
            <div><strong>{/literal}{t}Created:{/t}{literal}</strong> {{created}}</div>
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

{literal}
<script id="template-upload" type="text/html">
{% for (var i=0, files=o.files, l=files.length, file=files[0]; i<l; file=files[++i]) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name">{%=file.name%}</td>
        <td class="size">{%=o.formatFileSize(file.size)%}</td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label important">Error</span> {%=fileUploadErrors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td class="progress progress-striped active"><div class="bar" style="width:0%;"></div></td>
            <td colspan=2>
                <span class="start">{% if (!o.options.autoUpload) { %}<button class="btn btn-success">{/literal}{t}Start{/t}{literal}</button>{% } %}</span>
                <span class="cancel">{% if (!i) { %}<button class="btn btn-danger">{/literal}{t}Cancel{/t}{literal}</button>{% } %}</span>
            </td>
        {% } else { %}
            <td colspan="3">
                <span class="cancel">{% if (!i) { %}<button class="btn">{/literal}{t}Cancel{/t}{literal}</button>{% } %}</span>
            </td>
        {% } %}
    </tr>
{% } %}
</script>
<script id="template-download" type="text/html">
{% for (var i=0, files=o.files, l=files.length, file=files[0]; i<l; file=files[++i]) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td class="name">{%=file.name%}</td>
            <td class="size">{%=o.formatFileSize(file.size)%}</td>
            <td class="error" colspan="2"><span class="label important">{/literal}{t}Error{/t}{literal}</span> {%=fileUploadErrors[file.error] || file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}">{%=file.name%}</a>
                <input type="hidden" name="id[]" value="{%=file.id%}" class="file-id">
            </td>
            <td class="size">{%=o.formatFileSize(file.size)%}</td>
            <td>{/literal}{t}Uploaded{/t}{literal}</td>
        {% } %}
    </tr>
{% } %}
</script>
{/literal}

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
        top: 48px;
        bottom: 61px;
        max-height:100%;
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
    #media-uploader .upload-content .alert-success,
    #media-uploader .upload-content .alert-error {
        max-width:300px;
        margin:10px auto;
        display:none;
    }
    #media-uploader .upload-content .loading {

    }

    #media-uploader #dropzone {
        background:none;
        font-weight:normal;
    }
    #media-uploader #dropzone .explanation {
        width: 25%;
        margin: 0 auto;
        top: 50%;
        position: absolute;
        right: 50%;
        margin-right: -10%;
        font-weight: bold;
    }
</style>
{/block}
