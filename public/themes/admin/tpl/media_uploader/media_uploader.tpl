<div class="modal hide tabbable tabs-left" id="media-uploader">

    <ul class=" nav nav-tabs modal-sidebar full-height">
        <li><a href="#upload"  data-toggle="tab">{t}Upload{/t}</a></li>
        <li class="active"><a href="#gallery"  data-toggle="tab">{t}Browse{/t}</a></li>
    </ul>
    <div class="tab-content modal-content full-height">

        <div id="gallery" class="tab-pane full-height active">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 class="modal-title">{t}Pick the item to insert{/t}</h3>
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

            <div id="media-element-show" class="side-body">
                <div class="body"></div>
            </div>
            <div class="modal-footer">
                <div class="image-info pull-left"></div>
                <div class="pull-right buttons">
                    <a class="btn btn-primary yes assign_content" href="#">{t}Insert into article{/t}</a>
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
                                        <i class="icon-plus-sign"></i>
                                        {t}Add files...{/t}
                                        <input type="file" name="files[]" multiple>
                                    </div>
                                </div>
                            </div>
                            <div class="pull-right">
                                <div class="progress progress-striped active fileupload-progressbar fade">
                                    <div class="bar" style="width:0%;"></div>
                                </div>
                            </div>
                        </div>
                        <table class="table condensed">
                            <tbody class="files">
                                <div class="explanation">{t}Drop files anywhere here to upload or click on the "Add Files..." button above.{/t}</div>
                            </tbody>
                        </table>
                    </form>
                </div><!-- end #dropzone -->
            </div>
        </div>

    </div>

</div>

{script_tag src="/jquery/tmpl.min.js"}
{script_tag src="/jquery/load-image.min.js"}
{script_tag src="/jquery/bootstrap-image-gallery.min.js"}
{script_tag src="/jquery/jquery.iframe-transport.js"}
{script_tag src="/jquery/tmpl.min.js" defer=defer}
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
    emptyResult: '{t}Empty file upload result{/t}',
    uploadingMessage: '{t}Uploading files{/t}'
};
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
<h5 class="modal-title">{t}Thumbnail details{/t}</h5>
{literal}
{{#with content}}
<div class="photo-image-information">
    <div class="preview">
        <img src="{{image_path}}" />
    </div>
    <div class="buttons btn-group">
        <a href="{{edit_url}}" target="_blank" class="edit-image-button btn"><i class="icon icon-pencil"></i> {/literal}{t}Edit{/t}{literal}</a>
        <a href="#" class="delete-image-button btn"><i class="icon icon-trash"></i> {/literal}{t}Delete image{/t}{literal}</a>
    </div>
    <div class="image-title">{{title}}</div>
    <div class="info">
        <div>{{width}} × {{height}}</div>
        <div>{{size}} Kb</div>
        <div>{{created}}</div>
    </div>
</div>

<hr>

<div class="photo-insert-form">
    <div class="control-group">
        <label for="caption" class="control-label">{/literal}{t}Caption{/t}{literal}</label>
        <div class="controls">
            <textarea required="required" id="caption" name="caption" rows="2">{{description}}</textarea>
        </div>
    </div>
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
