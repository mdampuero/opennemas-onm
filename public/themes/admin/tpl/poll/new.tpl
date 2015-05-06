{extends file="base/admin.tpl"}
{block name="footer-js" append}
  {javascripts src="@Common/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js,
                    @Common/js/jquery/jquery.tagsinput.min.js"}
      <script type="text/javascript" src="{$asset_url}"></script>
  {/javascripts}
  <script type="text/javascript">
  $(document).ready(function ($){
    $('#title').on('change', function(e, ui) {
        if (!$('#metadata').val()) {
            fill_tags($('#title').val(), $('#metadata'), '{url name=admin_utils_calculate_tags}');
        }
    });

    $('#closetime').datetimepicker({
      format: 'YYYY-MM-D HH:mm:ss'
    });
  });
  </script>
{/block}

{block name="header-css" append}
  <style>
  .poll-type {
    margin-left:15px;
  }
  </style>
{/block}

{block name="content"}
<form action="{if $poll->id}{url name=admin_poll_update id=$poll->id}{else}{url name=admin_poll_create}{/if}" method="post"  ng-controller="PollCtrl">
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
                    <li class="quicklinks hidden-xs">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks hidden-xs">
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
                              <label for="visualization_bars" class="col-md-6">
                                <input type="radio" name="visualization" value="0" ng-model="visualization" class="required" id="visualization_bars"{if $poll->visualization eq 0} checked {/if}>
                                <div class="fa fa-bar-chart fa-4x"></div>
                                <div class="poll-type">{t}Bars{/t}</div>
                              </label>
                              <label for="visualization_pie" class="col-md-6">
                                <input type="radio" name="visualization" value="1" ng-model="visualization" class="required" id="visualization_pie"{if $poll->visualization eq 1} checked {/if}>
                                <div class="fa fa-pie-chart fa-4x"></div>
                                <div class="poll-type">{t}Circular{/t}</div>
                              </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="answers">{t}Answers{/t}</label>
                            <div class="controls">
                                <input type="hidden" id="parsedAnswers" name="parsedAnswers" ng-model="parsedAnswers" ng-value="parsedAnswers" ng-init="parseAnswers({json_encode($items)|clear_json})">
                                <div id="answers">
                                    <div class="ng-cloak" ng-repeat="answer in answers track by $index">
                                        <div>
                                            <div class="form-group">

                                              <div class="input-group" style="width: 100%">
                                                <input class="form-control" name="item[]" type="text" ng-value="answer.item" ng-model="answer.item" class="form-control" required="required" />
                                                <div class="input-group-btn">
                                                  <button type="button" class="btn btn-default">
                                                    <small ng-if="answer.votes > 0">{t}Votes{/t}:  [% answer.votes %] / {$poll->total_votes}</small>
                                                    <small ng-if="answer.votes <= 0">{t}No votes{/t}</small>
                                                  </button>
                                                  <button title="{t}Remove poll answer{/t}" class="btn btn-danger" ng-click="removeAnswer($index)"><i class="fa fa-trash-o"></i></button>
                                                </div>
                                              </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <button type="button" ng-click="addAnswer()" class="btn"><i class="fa fa-plus"></i> {t}Add new answer{/t}</a>
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
                                        <label for="content_status">{t}Published{/t}</label>
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
                                    <label class="form-label" for="endtime">{t}Publication closed date{/t}</label>
                                    <div class="controls">
                                      <div class="input-group">
                                        <input class="form-control" id="closetime" name="params[closetime]" type="datetime" value="{$poll->params['closetime']}">
                                        <span class="input-group-addon add-on">
                                          <span class="fa fa-calendar"></span>
                                        </span>
                                      </div>
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

                                <div class="form-group">
                                  <label for="metadata" class="form-label">{t}Tags{/t}</label>
                                    <div class="controls">
                                        <input data-role="tagsinput" id="metadata" name="metadata" placeholder="{t}Write a tag and press Enter...{/t}" required="required" type="text" value="{$poll->metadata|clearslash|escape:"html"}"/>
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
