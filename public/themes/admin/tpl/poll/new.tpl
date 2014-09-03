{extends file="base/admin.tpl"}
{block name="footer-js" append}
    {javascripts src="@Common/js/jquery/jquery.tagsinput.min.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
    <script type="text/javascript">
    jQuery(document).ready(function ($){
        var tags_input = $('#metadata').tagsInput({ width: '100%', height: 'auto', defaultText: "{t}Write a tag and press Enter...{/t}"});

        $('#title').on('change', function(e, ui) {
            if (tags_input.val().length == 0) {
                fill_tags_improved($('#title').val(), tags_input, '{url name=admin_utils_calculate_tags}');
            }
        });
        $('#formulario').onmValidate({
            'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
        });

        $('#answers').on('click', '.del', function() {
            var button = $(this);
            log(button)
            button.closest('.poll_answer').each(function(){
                log($(this));
                $(this).remove();
            });
        })

        $('#add_answer').on('click', function(){
            var source = $('#poll-template').html();
            $('#answers').append(source);
        });

    });
    </script>
<script id="poll-template" type="text/x-handlebars-template">
<div class="poll_answer">
    <div class="input-append">
        <input type="text" name="item[]" value=""/>
        <div class="btn addon del">
            <i class="icon-trash"></i>
        </div>
    </div>
</div>
</script>
{/block}

{block name="header-css" append}
<style type="text/css">
    .poll_answer {
        margin-bottom:5px;
    }
</style>
{/block}


{block name="content"}
<form action="{if $poll->id}{url name=admin_poll_update id=$poll->id}{else}{url name=admin_poll_create}{/if}" method="post" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if $poll->id}{t}Editing poll{/t}{else}{t}Creating a poll{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_polls category=$category}">
                        <img src="{$params.IMAGE_DIR}previous.png" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content contentform clearfix">
        {render_messages}

        <div class="form-vertical contentform-inner">
            <div class="contentform-main">
                <div class="control-group">
                    <label class="control-label" for="title">{t}Title{/t}</label>
                    <div class="controls">
                        <input type="text" id="title" name="title"
                            value="{$poll->title|clearslash|escape:"html"}" required="required" class="input-xxlarge"/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="subtitle">{t}Subtitle{/t}</label>
                    <div class="controls">
                        <input  type="text" id="subtitle" name="subtitle"
                            value="{$poll->subtitle|clearslash}"  required="required" class="input-xxlarge" />
                    </div>
                </div>
            </div>

            <div class="contentbox-container">
                <div class="contentbox">
                    <h3 class="title">{t}Attributes{/t}</h3>
                    <div class="content">
                        <input id="content_status" name="content_status" type="checkbox" {if !isset($poll) || $poll->content_status eq 1}checked="checked"{/if} value="1"/>
                        <label for="content_status">{t}Available{/t}</label>

                        <hr class="divisor">

                        {is_module_activated name="COMMENT_MANAGER"}
                        <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($poll) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($poll) && $poll->with_comment eq 1)}checked{/if} value="1" />
                        <label for="with_comment">{t}Allow comments{/t}</label>
                        <br>
                        {/is_module_activated}

                        <input id="favorite" name="favorite" type="checkbox" {if $poll->favorite eq 1}checked="checked"{/if} value="1" />
                        <label for="favorite">{t}Favorite{/t}</label>

                        <hr class="divisor">

                        <div class="control-group">
                            <label class="control-label" for="category">{t}Category{/t}</label>
                            <div class="controls">
                                <select name="category" id="category"  >
                                    {section name=as loop=$allcategorys}
                                        <option value="{$allcategorys[as]->pk_content_category}"
                                            {if $allcategorys[as]->inmenu eq 0} class="unavailable" {/if}
                                            {if $poll->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                        {section name=su loop=$subcat[as]}
                                            <option value="{$subcat[as][su]->pk_content_category}"
                                            {if $subcat[as][su]->inmenu eq 0} class="unavailable" {/if}
                                                {if $poll->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                        {/section}
                                    {/section}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contentbox">
                    <h3 class="title">{t}Tags{/t}</h3>
                    <div class="content">
                        <div class="control-group">
                            <div class="controls">
                                <input  type="text" id="metadata" name="metadata" required="required" value="{$poll->metadata|clearslash|escape:"html"}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contentbox-main">
                <div class="control-group">
                    <label class="control-label" for="visualization">{t}Visualization format{/t}</label>
                    <div class="controls">
                        <select name="visualization" id="visualization" class="required">
                            <option value="0" {if $poll->visualization eq 0} selected {/if}>{t}Circular{/t}</option>
                            <option value="1" {if $poll->visualization eq 1} selected {/if}>{t}Bars{/t}</option>
                        </select>
                    </div>
                </div>

                <fieldset>
                    <div class="control-group">
                        <label class="control-label" for="answers">{t}Allowed answers{/t}</label>
                        <div class="controls">
                            <div id="answers">
                                {foreach name=i from=$items item=answer}
                                    <div class="poll_answer">
                                        <div class="input-append" style="display:inline-block">
                                            <input type="text" name="item[{$answer.pk_item}]" value="{$answer.item}"/>
                                            <input type="hidden" name="votes[{$answer.pk_item}]" value="{$answer.votes}">
                                            <div class="btn addon del">
                                                <i class="icon-trash"></i>
                                            </div>
                                        </div>
                                        <small style="display:inline-block">{t}Votes{/t}:  {$answer.votes} / {$poll->total_votes}</small>
                                    </div>
                                {/foreach}
                                </div>
                            <br>
                            <a id="add_answer" class="btn">
                                <i class="icon-plus"></i>
                                {t}Add new answer{/t}
                            </a>
                        </div>
                    </div>
                </fieldset>
            </div>



        </div>
    </div>
</form>
{/block}
