{extends file="base/admin.tpl"}

{block name="header-css" append}
    {stylesheets src="@Common/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}" media="screen">
    {/stylesheets}
{/block}

{block name="footer-js" append}
    {javascripts src="@Common/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
<script>
jQuery(document).ready(function($) {
    $('#letter-edit').tabs();

    $('#created').datetimepicker({
      format: 'YYYY-MM-D HH:mm:ss'
    });

    $('#title').on('change', function(e, ui) {
        fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
    });

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });
});
</script>
{/block}

{block name="content"}
<form action="{if isset($letter->id)}{url name=admin_letter_update id=$letter->id}{else}{url name=admin_letter_create}{/if}" method="POST" name="formulario" id="formulario">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-envelope"></i>
                            {t}Letters to the Editor{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>
                            {if isset($letter->id)}
                                {t}Editing letter{/t}
                            {else}
                                {t}Creating letter{/t}
                            {/if}
                        </h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_letters}" title="{t}Go back{/t}">
                                <span class="fa fa-reply"></span>
                            </a>
                        </li>
                        <li class="quicklinks"><span class="h-seperate"></span></li>
                        <li class="quicklinks">
                            <button type="submit" class="btn btn-primary">
                                <span class="fa fa-save"></span>
                                {t}Save{/t}
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        {render_messages}
        <div class="row">
            <div class="col-md-8">
                <div class="grid simple">
                    <div class="grid-body">
                        <div class="form-group">
                            <label for="title" class="form-label">{t}Title{/t}</label>
                            <div class="controls">
                                <input type="text" id="title" name="title" value="{$letter->title|clearslash|escape:"html"}" required="required" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{t}Author information{/t}</label>
                            <div class="controls">
                                <div class="form-inline-block">
                                    <div class="form-group">
                                        <label for="author" class="form-label">{t}Nickname{/t}</label>
                                        <div class="controls">
                                            <input type="text" id="author" name="author" value="{$letter->author|clearslash}" required="required" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="form-label">{t}Email{/t}</label>
                                        <div class="controls">
                                            <input type="email" id="email" name="email" value="{$letter->email|clearslash}" required="required" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-inline-block">
                                    {foreach $letter->params as $key => $value}
                                    <div class="form-group">
                                        <label for="{$key}" class="form-label">{t}{$key|capitalize}{/t}</label>
                                        <div class="controls">
                                            <input type="text" id="params[{$key}]" name="params[{$key}]" value="{$value|clearslash}"  readonly class="form-control" />
                                        </div>
                                    </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="created" class="form-label">{t}Created at{/t}</label>
                            <div class="controls">
                                <input type="text" id="created" name="created" value="{$letter->created}"class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="body" class="form-label">{t}Body{/t}</label>
                            <div class="controls">
                                <textarea name="body" id="body" class="onm-editor form-control" onm-editor onm-editor-preset="standard" rows="10">{$letter->body|clearslash}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="url" class="form-label">{t}Related url{/t}</label>
                            <div class="controls">
                                <input type="text" id="url" name="url" value="{$letter->url}" class="form-control"/>
                            </div>
                        </div>
                        {acl isAllowed='PHOTO_ADMIN'}
                            {is_module_activated name="IMAGE_MANAGER"}
                                <div id="related_media" class="form-group">
                                    <label for="special-image" class="form-label">{t}Image for Special{/t}</label>
                                    <div class="controls">
                                        <ul class="related-images thumbnails">
                                            <li class="contentbox frontpage-image {if isset($photo1) && $photo1->name}assigned{/if}">
                                                <h3 class="title">{t}Frontpage image{/t}</h3>
                                                <div class="content">
                                                    <div class="image-data">
                                                        <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="image thumbnail">
                                                            <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}"/>
                                                        </a>
                                                        <input type="hidden" name="img1" value="{$special->img1|default:""}" class="related-element-id" />
                                                    </div>

                                                    <div class="not-set">
                                                        {t}Image not set{/t}
                                                    </div>

                                                    <div class="btn-group">
                                                        <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="btn btn-small">{t}Set image{/t}</a>
                                                        <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            {/is_module_activated}
                        {/acl}

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="grid simple">
                    <div class="grid-body">
                        <div class="form-group">
                            <label for="metadata" class="form-label">{t}Tags{/t}</label>
                            <span class="help">{t}List of words separated by words.{/t}</span>
                            <div class="controls">
                                <input data-role="tagsinput" id="metadata" name="metadata" required="required"  type="text" value="{$letter->metadata|clearslash|escape:"html"}"/>
                            </div>
                        </div>
                        {acl isAllowed="LETTER_AVAILABLE"}
                            <div class="form-group">
                                <label for="content_status" class="form-label">{t}Published{/t}</label>
                                <div class="controls">
                                    <select name="content_status" id="content_status" required="required">
                                        <option value="1" {if $letter->content_status eq 1} selected {/if}>Si</option>
                                        <option value="0" {if $letter->content_status eq 0} selected {/if}>No</option>
                                    </select>
                                </div>
                            </div>
                        {/acl}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="id" id="id" value="{$letter->id|default:""}" />
</form>
{/block}
