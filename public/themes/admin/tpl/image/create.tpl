{extends file="base/admin.tpl"}

{block name="prototype"}
{/block}

{block name="header-js" append}
<script>
var image_uploader ={
    show_url: '{url name=admin_image_show l=a}'
}
</script>
{/block}

{block name="header-css" append}
    <link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.4.0/bootstrap.min.css">
    <!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/Bootstrap-Image-Gallery/bootstrap-ie6.min.css"><![endif]-->
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t 1=$datos_cat[0]->title}Uploading images to "%1"{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}/images?category={$category}&amp;action={$smarty.session.desde}">
                    <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}"><br />{t}Go back{/t}
                </a>
            </li>
            <li class="separator"></li>
            <li style="display:none" class="edit-uploaded" id="edit-uploaded-button">
                <a href="#">
                    <img src="{$params.IMAGE_DIR}save_and_continue.png" alt="{t}Edit uploaded{/t}"><br />{t}Edit uploaded{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}
    <div class="notice edit-uploaded" style="display:none">
        {t}Please click in the "Edit uploaded" button from above to edit latest upload photo's data{/t}
    </div><!-- / -->

    <form id="fileupload" action="{url name=admin_image_create category=$category}" method="POST" enctype="multipart/form-data">

        <div class="fileupload-buttonbar clearfix">
            <button type="reset" class="onm-button blue cancel right">{t}Cancel upload{/t}</button>
            <button type="submit" class="onm-button green start right">{t}Start upload{/t}</button>
            <div class="progressbar fileupload-progressbar fade right"><div style="width:0%;"></div></div>
            <div class="onm-button blue fileinput-button input-hidden left">
                {t}Add files...{/t}
                <input type="file" name="files[]" multiple>
            </div>
            <button type="button" class="onm-button red delete left">{t}Delete selected{/t}</button>
            <input type="checkbox" class="toggle left">
        </div>
        <div id="dropzone" class="fade well">{t}Drop files here{/t}</div>
        <table class="zebra-striped condensed"><tbody class="files"></tbody></table>

        <div class="well">
            <h3>{t}User notes{/t}</h3>
            <ul>
                <li>
                    {t}Include files to upload by:{/t}
                    <ul>
                        <li>{t escape=off}You can <strong>drag &amp; drop</strong> files from your desktop on this webpage with Google Chrome, Mozilla Firefox and Apple Safari.{/t}</li>
                        <li>{t}If you are using Internet Explorer click in "Add Files..." buton and select the files you want to upload.{/t}</li>
                    </ul>
                </li>
                <li>{t escape=off 1=$max_allowed_size}The maximum file size for uploads is <strong>%1 MB</strong>.{/t}</li>
                <li>{t escape=off}Only image files (<strong>JPG, GIF, PNG</strong>) are allowed.{/t}</li>
            </ul>
        </div>
        <!-- gallery-loader is the loading container animation -->
        <div id="gallery-loader"></div>
        <!-- gallery-modal is the modal dialog used for the image gallery -->
<!--         <div id="gallery-modal" class="modal hide fade">
            <div class="modal-header">
                <a href="#" class="close">&times;</a>
                <h3 class="title"></h3>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <a class="btn btn-primary next">Next</a>
                <a class="btn info prev">Previous</a>
                <a class="btn success download" target="_blank">Download</a>
            </div>
        </div> -->
        <input type="hidden" name="category" value="{$category}" />
    </form>
    <div id="upload-helper" data-filecount=0></div>

    </div><!-- /upload-helper -->
</div>
{/block}

{block name="footer-js" append}
    {literal}
    <script>
    var fileUploadErrors = {
        maxFileSize: '{t}File is too big{/t}',
        minFileSize: '{t}File is too small{/t}',
        acceptFileTypes: '{t}Filetype not allowed{/t}',
        maxNumberOfFiles: '{t}Max number of files exceeded{/t}',
        uploadedBytes: '{t}Uploaded bytes exceed file size{/t}',
        emptyResult: '{t}Empty file upload result{/t}'
    };
    </script>
    <script id="template-upload" type="text/html">
    {% for (var i=0, files=o.files, l=files.length, file=files[0]; i<l; file=files[++i]) { %}
        <tr class="template-upload fade">
            <td class="preview"><span class="fade"></span></td>
            <td class="name">{%=file.name%}</td>
            <td class="size">{%=o.formatFileSize(file.size)%}</td>
            {% if (file.error) { %}
                <td class="error" colspan="2"><span class="label important">Error</span> {%=fileUploadErrors[file.error] || file.error%}</td>
            {% } else if (o.files.valid && !i) { %}
                <td class="progress"><div class="progressbar"><div style="width:0%;"></div></div></td>
                <td colspan=2>
                    <span class="start">{% if (!o.options.autoUpload) { %}<button class="onm-button green">{/literal}{t}Iniciar{/t}{literal}</button>{% } %}</span>
                    <span class="cancel">{% if (!i) { %}<button class="onm-button blue">{/literal}{t}Cancel{/t}{literal}</button>{% } %}</span>
                </td>
            {% } else { %}
                <td colspan="3">
                    <span class="cancel">{% if (!i) { %}<button class="onm-button blue">{/literal}{t}Cancel{/t}{literal}</button>{% } %}</span>
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
                <td></td>
            {% } %}
            <td class="delete">
                <input type="checkbox" name="delete" value="1">
                <button class="onm-button red" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">{/literal}{t}Delete{/t}{literal}</button>
            </td>
        </tr>
    {% } %}
    </script>
    {/literal}

    <script>
    var fileUploadErrors = {
        maxFileSize: '{t}File is too big{/t}',
        minFileSize: '{t}File is too small{/t}',
        acceptFileTypes: '{t}Filetype not allowed{/t}',
        maxNumberOfFiles: '{t}Max number of files exceeded{/t}',
        uploadedBytes: '{t}Uploaded bytes exceed file size{/t}',
        emptyResult: '{t}Empty file upload result{/t}'
    };
    </script>

    <!-- The Templates and Load Image plugins are included for the FileUpload user interface -->
    <script src="{$params.JS_DIR}/jquery/tmpl.min.js"></script>
    <!-- The Templates and Load Image plugins are included for the FileUpload user interface -->
    <script src="{$params.JS_DIR}/jquery/load-image.min.js"></script>
    <script src="{$params.JS_DIR}/jquery/bootstrap-image-gallery.min.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="{$params.JS_DIR}/jquery/jquery.iframe-transport.js" defer=defer></script>
    <script src="{$params.JS_DIR}/jquery/jquery.fileupload.js" defer=defer></script>
    <script src="{$params.JS_DIR}/jquery/jquery.fileupload-ui.js" defer=defer></script>
    <script src="{$params.JS_DIR}/image/application.js"></script>
    <!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
    <!--[if gte IE 8]><script src="cors/jquery.xdr-transport.js"></script><![endif]-->
    <!--<script id="template-upload" type="text/html" src="{$params.JS_DIR}/image/template_upload.js"></script>-->
    <!--<script id="template-download" type="text/html" src="{$params.JS_DIR}/image/template_download.js"></script>-->
    {include file="image/modals/_edit_uploaded_files.tpl"}

{/block}

