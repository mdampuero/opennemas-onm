{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
    jQuery( ".author-photos" ).sortable({
        containment : '.author-photos'
    });
    jQuery( ".author-photos" ).disableSelection();
    jQuery(document).ready(function($) {
        $('.delete-author-photo').on('click', function(e, ui) {
            var element = $(this);
            element.parent('.thumbnail').remove();
        });

        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });
    });
</script>
{/block}

{block name="content"}
<form action="{if $author->id}{url name=admin_opinion_author_update id=$author->id}{else}{url name=admin_opinion_author_create}{/if}" method="POST" enctype="multipart/form-data" id="formulario" >
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{if !isset($author)}{t}Opinion Manager :: New author{/t}{else}{t}Opinion Manager :: Edit author{/t}{/if}</div>
            <ul class="old-button">
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_opinion_authors page=$page}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>

            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div id="warnings-validation"></div>

        <div class="form-horizontal panel">
            <fieldset>
            <div class="control-group">
                <label for="name" class="control-label">{t}Name{/t}</label>
                <div class="controls">
                    <input type="text" id="name" name="name" value="{$author->name|default:""}" class="input-xlarge" required="required" autofocus/>
                </div>
            </div>

            <div class="control-group">
                <label for="condition" class="control-label">{t}Condition{/t}</label>
                <div class="controls">

                    <textarea rows="3" name="condition" id="condition" class="input-xlarge">{$author->condition|default:""}</textarea>
                </div>
            </div>

            <div class="control-group">
                <label for="politics" class="control-label">{t}Blog name{/t}</label>
                <div class="controls">
                    <input type="text" id="politics" name="politics" value="{$author->politics|default:""}" class="input-xlarge"/>
                </div>
            </div>

            <div class="control-group">
                <label for="blog" class="control-label">{t}Blog url{/t}</label>
                <div class="controls">
                    <input type="text" id="blog" name="blog" value="{$author->blog|default:""}" class="input-xlarge"/>
                </div>
            </div>

            <div class="control-group">
                <label for="params[inrss]" class="control-label">{t}Show in RSS{/t}</label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" name="params[inrss]" id="params[inrss]" {if !isset($author->params['inrss']) || $author->params['inrss'] eq 1} checked="checked"{/if}>
                        {t}If this option is activated this author will be showed in rss{/t}
                    </label>
                </div>
            </div>

            {if count($photos) > 0}
            <div class="control-group">
                <label for="author-photos" class="control-label">{t}Author photos{/t}</label>
                <div class="controls">
                    <div class="author-photos">
                        {foreach name=as from=$photos|default:array() item=photo}
                        <div id='{$photo->pk_img}' class="thumbnail">
                            <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$photo->path_img}" />
                            <input type="hidden" name="photos[]" value="{$photo->fk_photo}">
                            <a class="btn btn-danger btn-mini delete-author-photo" href="#">{t}Delete{/t}</a>
                        </div>
                        {/foreach}
                    </div>
                    <p class="help-block">{t}You can change the image order by drag and drop them.{/t}</p>
                </div>
            </div>
            {/if}

            <div class="control-group">
                <label for="fileInput" class="control-label">{t}Add new photo{/t}</label>
                <div class="controls">
                    <input type="file" id="fileInput" class="input-file" name="photo-file">
                </div>
            </div>
            </fieldset>
            <input type="hidden" id="fk_author_img" name="fk_author_img" value="" />
        </div>
    </div>
</form>
{/block}
