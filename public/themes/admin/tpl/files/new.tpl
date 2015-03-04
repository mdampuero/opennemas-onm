{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#title').on('change', function(e, ui) {
                fill_tags(jQuery('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
            });
            $('#formulario').onmValidate({
                'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
            });
        });

    </script>
{/block}

{block name="content"}
<form action="{if !is_null($attaches)}{url name=admin_files_update id=$attaches->id}{else}{url name=admin_files_create}{/if}"
    enctype="multipart/form-data" method="POST" name="formulario" id="formulario" />

<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa file-o"></i>
                        {t}Files{/t}
                    </h4>
                </li>
                <li class="quicklinks hidden-xs">
                    <span class="h-seperate"></span>
                </li>
                <li class="quicklinks hidden-xs">
                    <h5>
                        {if $attaches}
                            {t}Editing file{/t}
                        {else}
                            {t}Creating file{/t}
                        {/if}
                    </h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a class="btn btn-link" href="{url name=admin_files}" title="{t}Go back{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>

                    {acl isAllowed="BOOK_CREATE"}
                    <li class="quicklinks">
                        <button class="btn btn-primary" type="submit">
                            <span class="fa fa-plus"></span>
                            {t}Save{/t}
                        </button>
                    </li>
                    {/acl}
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
                        <label for="" class="form-label">{t}Title{/t}</label>
                        <div class="controls">
                            <input type="text" id="title" name="title" value="{$attaches->title|clearslash}"
                                class="form-control" required="required">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">{t}Description{/t}</label>
                        <div class="controls">
                            <textarea id="description" name="description" class="form-control" required="required" class="required" onm-editor onm-editor-preset="simple">{$attaches->description|clearslash}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="path" class="form-label">{t}Path{/t}</label>
                        <div class="controls">
                            {if !is_null($attaches)}
                            <a href="{$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}{$attaches->path}">{$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}{$attaches->path}</a>
                            <input type="hidden" id="path" name="path" value="{$attaches->path|clearslash}" class="form-control" required="required" readonly="readonly">
                            {else}
                            <input type="file" id="path" name="path" value="" required="required" />
                            {/if}
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="grid simple">
                <div class="grid-body">
                    <div class="form-group">
                        <label for="category" class="form-label">{t}Category{/t}</label>
                        <div class="controls">
                            {include file="common/selector_categories.tpl" name="category" item=$attaches}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="metadata" class="form-label">{t}Tags{/t}</label>
                        <div class="controls">
                            <input data-role="tagsinput" id="metadata" name="metadata" required="required" type="text" value="{$attaches->metadata|clearslash}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {if !is_null($attaches->id)}
    <input type="hidden" id="id" name="id"  value="{$attaches->id|default:""}" />
    <input type="hidden" id="fich" name="fich" value="{$attaches->pk_attachment}" />
    {/if}
    <input type="hidden" name="page" id="page" value="{$page|default:"1"}" />

</form>
{/block}
