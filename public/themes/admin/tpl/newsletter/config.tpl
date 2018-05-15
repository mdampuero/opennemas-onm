{extends file="base/admin.tpl"}
{block name="content"}
<form action="{url name=backend_newsletters_config}" method="POST" id="formulario" ng-controller="InnerCtrl" ng-init="type='{if empty($configs['newsletter_subscriptionType'])}submit{else}{$configs['newsletter_subscriptionType']}{/if}'">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="{url name=backend_newsletters_list}" title="{t}Go back to list{/t}">
                <i class="fa fa-envelope"></i>
                {t}Newsletters{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs">
            <div class="p-l-10 p-r-10 p-t-10">
              <i class="fa fa-angle-right"></i>
            </div>
          </li>
          <li class="quicklinks hidden-xs">
            <h4><strong>{t}Settings{/t}</strong></h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <button class="btn btn-primary" type="submit" data-text="{t}Saving{/t}..." id="save-button">
                <span class="fa fa-save"></span>
                <span class="text">{t}Save{/t}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">

    {if $missing_recaptcha}
    {capture name=url}{url name=admin_system_settings}{/capture}
    <ul class="messenger messenger-fixed messenger-on-bottom">
      <li>
        <div class="alert alert-error messenger-message">
          <div class="messenger-message-inner">{t escape=off 1=$smarty.capture.url}Before using newsletter you have to fill the <a href="%1#external"  target="_blank">reCaptcha keys on system settings</a>{/t}.</div>
        </div>
      </li>
    </ul>
    {/if}
    <div class="grid simple">
      <div class="grid-title">
        <h4>
          <i class="fa fa-envelope"></i>
          {t}Newsletter handler{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div class="form-group">
          <div class="help">{t}Opennemas allows you to use different methods to send your newsletters. Choose between those available below:{/t}</div>
          <div class="controls">
            <select name="newsletter_subscriptionType" id="newsletter_subscriptionType" ng-model="type">
              <option value="submit" {if $configs['newsletter_subscriptionType'] eq 'submit'} selected {/if}>{t}External service{/t}</option>
              <option value="create_subscriptor" {if $configs['newsletter_subscriptionType'] eq 'create_subscriptor'} selected {/if}>{t}Internal Send{/t} - {t}Subscriptions{/t}</option>
            </select>
          </div>
        </div>
        <div class="external-config ng-cloak" ng-show="type === 'submit'">
          <div class="form-group">
            <label for="email" class="form-label">{t}Mailing list address{/t}</label>
            <div class="controls">
              <input class="form-control" id="email" name="newsletter_maillist[email]" required type="email" value="{$configs['newsletter_maillist']['email']|default:""}"/>
              <div class="help">{t}If you have a mailing list service to deliver newsletters add the address here{/t}</div>
            </div>
          </div>
          <div class="form-group" >
            <label for="subscription" class="form-label">{t}Mail address to receive new subscriptions{/t}</label>
            <div class="controls">
              <input class="form-control" id="subscription" name="newsletter_maillist[subscription]" required type="email" value="{$configs['newsletter_maillist']['subscription']|default:""}"/>
            </div>
          </div>
        </div>

        <div class="ng-cloak" ng-show="type === 'create_subscriptor'">
          {t escape=off 1=$smarty.capture.subscriptors}You've choosen to use the <a href="%1" target="_blank">internal subscription list</a> to send your newsletters.{/t}
        </div>
      </div>
    </div>
    <div class="grid simple">
      <div class="grid-title">
        <h4>
          <i class="fa fa-pencil"></i>
          {t}Newsletter contents{/t}
        </h4>
      </div>
      <div class="grid-body">
        <div class="form-group">
          <label for="name" class="form-label">{t}Newsletter subject{/t}</label>
          <span class="help">{t}The subject of the emails in this newsletter{/t}</span>
          <div class="controls">
            <input type="text" required id="name" name="newsletter_maillist[name]" value="{$configs['newsletter_maillist']['name']|default:""}" class="form-control" placeholder="{t}Your newsletter subject{/t}"/>
          </div>
        </div>
        <div class="form-group">
          <label for="sender" class="form-label">{t}Email from{/t}</label>
          <span class="help">{t escape=off}Email sender{/t} (From)</span>
          <div class="controls">
            <input type="text" required id="sender" name="newsletter_maillist[sender]" value="{$configs['newsletter_maillist']['sender']|default:""}" class="form-control" placeholder="noreply@your_domain_name.com"/>
          </div>
        </div>
        <div class="form-group">
          <label for="newsletter_maillist_link" class="form-label">{t}Newsletter links points to{/t}</label>
          <div class="controls">
            <select name="newsletter_maillist[link]" id="newsletter_maillist_link">
              <option value="inner" {if $configs['newsletter_maillist']['link'] eq 'inner'} selected {/if}>{t}Point to inner{/t}</option>
              <option value="front" {if $configs['newsletter_maillist']['link'] eq 'front'} selected {/if}>{t}Point to frontpage{/t}</option>
            </select>
            <div class="help">{t}You can choose if you prefer that the links of the contents of the newsletter point within the content or contents on the frontpage{/t}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
