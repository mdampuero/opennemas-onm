{extends file="base/admin.tpl"}

{block name="footer-js" append}
  {javascripts}
    <script type="text/javascript">
      $(document).ready(function ($){
        $('#closetime').datetimepicker({
          format: 'YYYY-MM-DD HH:mm:ss',
          useCurrent: false,
          minDate: '{$poll->created|default:$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}'
        });
      });
    </script>
  {/javascripts}
{/block}

{block name="content"}
  <form action="{if $poll->id}{url name=admin_poll_update id=$poll->id}{else}{url name=admin_poll_create}{/if}" method="post"  ng-controller="PollCtrl" id="formulario" ng-init="init({json_encode($poll)|clear_json}, {json_encode($locale)|clear_json}, {json_encode($tags)|clear_json})">
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
                <a class="btn btn-link" href="{url name=admin_polls}" title="{t}Go back{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-primary" data-text="{t}Saving{/t}..." type="submit" id="save-button">
                  <i class="fa fa-save"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="row">
        <div class="col-md-8">
          <div class="grid simple">
            <div class="grid-body">
              <div class="form-group">
                <label class="form-label" for="title">{t}Title{/t}</label>
                <div class="controls">
                  <input class="form-control" id="title" name="title" ng-model="title" required type="text" value="{$poll->title|clearslash|escape:"html"}"/>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="pretitle">{t}Pretitle{/t}</label>
                <div class="controls">
                  <input class="form-control" id="pretitle" name="pretitle" type="text" required value="{$poll->pretitle|clearslash}"/>
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
                            <input class="form-control" name="item[]" type="text" ng-value="answer.item" ng-model="answer.item" class="form-control" required />
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
                      <input id="with_comment" name="with_comment" type="checkbox" {if (!isset($poll) && (!isset($enableComments) || $enableComments) eq 1) || (isset($poll) && $poll->with_comment eq 1)}checked{/if} value="1" />
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
                                  {if $allcategorys[as]->inmenu eq 0} class="unavailable" disabled {/if}
                                  {if $poll->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                          {section name=su loop=$subcat[as]}
                          <option value="{$subcat[as][su]->pk_content_category}"
                                  {if $subcat[as][su]->inmenu eq 0} class="unavailable" disabled {/if}
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
                      <onm-tag ng-model="tag_ids" locale="locale" tags-list="tags" check-new-tags="checkNewTags" get-suggested-tags="getSuggestedTags" load-auto-suggested-tags="loadAutoSuggestedTags" suggested-tags="suggestedTags" placeholder="{t}Write a tag and press Enter...{/t}"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {*is_module_activated name="CONTENT_SUBSCRIPTIONS"}
            <div class="row">
              <div class="col-md-12">
                <div class="grid simple">
                  <div class="grid-title">
                    <h4>{t}Subscription{/t}</h4>
                  </div>
                  <div class="grid-body">
                    <div class="checkbox">
                      <input {if $poll->params["only_registered"] == "1"}checked=checked{/if} id="only_registered" name="params[only_registered]" type="checkbox" value="1">
                      <label for="only_registered">
                        {t}Only available for registered users{/t}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          {/is_module_activated*}
        </div>
      </div>
    </div>
  </form>
{/block}
