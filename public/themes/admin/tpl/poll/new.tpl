{extends file="base/admin.tpl"}
{block name="footer-js" append}
    {javascripts src="@Common/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js,
                      @Common/js/jquery/jquery.tagsinput.min.js"}
        <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
    <script type="text/javascript">
    jQuery(document).ready(function ($){
        $('#title').on('change', function(e, ui) {
            if (!$('#metadata').val()) {
                fill_tags($('#title').val(), $('#metadata'), '{url name=admin_utils_calculate_tags}');
            }
        });

        $('#closetime').datetimepicker({
          format: 'YYYY-MM-D HH:mm:ss'
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
    {stylesheets src="@Common/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" filters="cssrewrite"}
        <link rel="stylesheet" href="{$asset_url}" media="screen">
    {/stylesheets}
{/block}


{block name="content"}
<form action="{if $poll->id}{url name=admin_poll_update id=$poll->id}{else}{url name=admin_poll_create}{/if}" method="post" id="formulario">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-pie-chart"></i>
                            {t}Polls{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>{if $poll->id}{t}Editing poll{/t}{else}{t}Creating a poll{/t}{/if}</h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_polls category=$category}" title="{t}Go back{/t}">
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i>
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
                            <label class="form-label" for="title">{t}Title{/t}</label>
                            <div class="controls">
                                <input class="form-control" id="title" name="title" required="required" type="text" value="{$poll->title|clearslash|escape:"html"}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="subtitle">{t}Subtitle{/t}</label>
                            <div class="controls">
                                <input class="form-control" id="subtitle" name="subtitle" type="text" required="required" value="{$poll->subtitle|clearslash}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="visualization">{t}Visualization format{/t}</label>
                            <div class="controls">
                                <select name="visualization" id="visualization" class="required">
                                    <option value="0" {if $poll->visualization eq 0} selected {/if}>{t}Circular{/t}</option>
                                    <option value="1" {if $poll->visualization eq 1} selected {/if}>{t}Bars{/t}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="answers">{t}Allowed answers{/t}</label>
                            <div class="controls">
                                <div id="answers">
                                    {foreach name=i from=$items item=answer}
                                        <div class="row">
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <input name="votes[{$answer.pk_item}]" type="hidden" value="{$answer.votes}">
                                                    <div class="input-group ">
                                                        <input class="form-control" name="item[{$answer.pk_item}]" type="text" value="{$answer.item}"/>
                                                        <div class="input-group-btn">
                                                            <div class="btn btn-danger">
                                                                <i class="fa fa-trash-o"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-8">
                                                <small class="help-block">{t}Votes{/t}:  {$answer.votes} / {$poll->total_votes}</small>
                                            </div>
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
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="grid simple">
                            <div class="grid-title">
                                <h4>{t}Attributes{/t}</h4>
                            </div>
                            <div class="grid-body">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input id="content_status" name="content_status" type="checkbox" {if !isset($poll) || $poll->content_status eq 1}checked="checked"{/if} value="1"/>
                                        <label for="content_status">{t}Available{/t}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="endtime">{t}Publication closed date{/t}</label>
                                    <div class="controls">
                                        <input id="closetime" name="params[closetime]" type="datetime" value="{$poll->params['closetime']}">
                                    </div>
                                </div>
                                {is_module_activated name="COMMENT_MANAGER"}
                                <div class="form-group">
                                    <div class="checkbox">
                                         <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($poll) && (!isset($commentsConfig['with_comments']) || $commentsConfig['with_comments']) eq 1) || (isset($poll) && $poll->with_comment eq 1)}checked{/if} value="1" />
                                        <label for="with_comment">{t}Allow comments{/t}</label>
                                    </div>
                                </div>
                                {/is_module_activated}
                                <div class="form-group">
                                    <div class="checkbox">
                                        <input id="favorite" name="favorite" type="checkbox" {if $poll->favorite eq 1}checked="checked"{/if} value="1" />
                                        <label for="favorite">{t}Favorite{/t}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="form-label" for="category">{t}Category{/t}</label>
                                        <div class="controls">
                                            <select name="category" id="category">
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
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="grid simple">
                            <div class="grid-title">
                                <h4>{t}Tags{/t}</h4>
                            </div>
                            <div class="grid-body">
                                 <div class="form-group">
                                    <div class="controls">
                                        <input data-role="tagsinput" id="metadata" name="metadata" required="required" type="text" value="{$poll->metadata|clearslash|escape:"html"}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
{/block}
