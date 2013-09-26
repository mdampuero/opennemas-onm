<div class="modal hide tabbable tabs-left" id="media-uploader" role="dialog" aria-labelledby="media-uploader" aria-hidden="true">

    <ul class=" nav nav-tabs modal-sidebar full-height">
        <li><a href="#uploader"  data-toggle="tab">{t}Upload{/t}</a></li>
        <li class="active"><a href="#browser"  data-toggle="tab">{t}Browse{/t}</a></li>
    </ul>
    <div class="tab-content modal-content full-height">

        <div id="browser" class="tab-pane full-height active">
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
                        <div class="loading pull-right hidden"><div class="spinner"></div></div>
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
                <div class="pull-left" id="selections"></div>
                <div class="pull-right buttons">
                    <a class="btn btn-primary assign_content disabled" href="#">{t}Insert{/t}</a>
                </div>
            </div>
        </div>

        <div id="uploader" class="tab-pane full-height">
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
                        <div class="explanation">{t}Drop files anywhere here to upload or click on the "Add Files..." button above.{/t}</div>
                        <table class="table condensed">
                            <tbody class="files">
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

{script_tag src="/swfobject.js"}
{script_tag src="/jquery/tmpl.min.js"}
{script_tag src="/jquery/load-image.min.js"}
{script_tag src="/jquery/bootstrap-image-gallery.min.js"}
{script_tag src="/jquery/jquery.iframe-transport.js"}
{script_tag src="/jquery/tmpl.min.js"}
{script_tag src="/jquery/jquery.fileupload.js" common=1}
{script_tag src="/jquery/jquery.fileupload-ui.js" common=1}
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
    {{#each years}}
        <optgroup label="{{name}}">
        {{#each months}}
            <option value="{{value}}">{{name}}</option>
        {{/each}}
        </optgroup>
    {{/each}}
{/literal}
</script>

<script type="text/html" id="tmpl-attachment">
{literal}
    <li class="attachment save-ready" data-id="{{id}}">
        <div class="attachment-preview type-image subtype-{{content.type_img}} landscape">
            <div class="thumbnail">
                <div class="centered">
                    {{#if is_swf}}
                        SWF_CALLER
                        <div class="attch-overlay"></div>
                    {{else}}
                        <img src="{{thumbnail_url}}" draggable="false">
                    {{/if}}
                </div>
            </div>
            <a class="check" href="#" title="Deselect"><div class="icon icon-ok"></div><div class="icon icon-minus"></div></a>
        </div>
    </li>
{/literal}
</script>

<script type="text/html" id="tmpl-attachment-short-info">
{literal}
{{#if contents}}
<div class="media-selection">
    <div class="selection-info">
        <span class="count">{/literal}{t}{ldelim}{ldelim}count{rdelim}{rdelim} selected{/t}{literal}</span>
            <a class="clear-selection" href="#">{/literal}{t}Clear{/t}{literal}</a>
    </div>
    <div class="selection-view">
        <ul class="attachments">
        {{#each contents}}
            <li class="attachment selection save-ready" data-id="{{id}}"  style="width:30px; height:30px">
                <div class="attachment-preview">
                    <div class="thumbnail" >
                        <div class="centered">
                            <img src="{{crop_thumbnail_url}}" width="30"  />
                        </div>
                    </div>
                </div>
            </li>
        {{/each}}
        </ul>
    </div>
</div>
{{/if}}
{/literal}
</script>

<script type="text/html" id="tmpl-show-element">
<h5 class="modal-title">{t}Thumbnail details{/t}</h5>
{literal}
{{#with content}}
<div class="photo-image-information">
    <div class="preview">
        {{#if is_swf}}
            SWF_CALLER
            <div class="attch-overlay"></div>
        {{else}}
            <img src="{{thumbnail_url}}" draggable="false">
        {{/if}}
    </div>
    <div class="image-title">{{title}}</div>
    <div class="info">
        <div>{{width}} × {{height}}</div>
        <div>{{size}} Kb</div>
        <div>{{created}}</div>
    </div>
    <div class="buttons btn-group">
        <a href="{{edit_url}}" target="_blank" class="edit-image-button btn btn-mini"><i class="icon icon-pencil"></i> {/literal}{t}Edit image{/t}{literal}</a>
        <!-- <a href="#" class="delete-image-button btn"><i class="icon icon-trash"></i> {/literal}{t}Delete image{/t}{literal}</a> -->
    </div>
</div>

<hr>
<div class="messages">
    <div class="saving muted">{/literal}{t}Saving...{/t}{literal}</div>
    <div class="saved text-success">{/literal}{t}Saved{/t}{literal}</div>
    <div class="error-saving text-error">{/literal}{t}Error saving{/t}{literal}</div>
</div>
<div class="photo-insert-form">

    <div class="control-group">
        <label for="caption" class="control-label">{/literal}{t}Description{/t}{literal}</label>
        <div class="controls">
            <textarea required="required" id="caption" name="caption" rows="2">{{description}}</textarea>
        </div>
    </div>
    <div class="control-group">
        <label for="alignment" class="control-label">{/literal}{t}Alignment{/t}{literal}</label>
        <div class="controls">
            <select class="alignment" data-setting="align" data-user-setting="align">
                <option value="left">{/literal}{t}Left{/t}{literal}</option>
                <option value="right">{/literal}{t}Right{/t}{literal}</option>
                <option value="none" selected="">{/literal}{t}None{/t}{literal}</option>
            </select>
        </div>
    </div>
    <input type="hidden" class="content_id" name="id" value="{{id}}" placeholder="">
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
