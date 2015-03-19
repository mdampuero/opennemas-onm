{extends file="base/admin.tpl"}

{block name="content"}
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <i class="fa fa-home"></i>
              {t}Newsletters{/t}
            </h4>
          </li>
          <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
          <li class="quicklinks hidden-xs">
            <h5>{t}Delivering report{/t}</h5>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li>
              <a href="{url name=admin_newsletters}" class="btn btn-link" title="{t}Back to list{/t}">
                <span class="fa fa-reply"></span>
                {t}Back to list{/t}
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content newsletter-manager">
    {render_messages}
    <div class="grid simple">
      <div class="grid-title">
        <h4>{t}Newsletter sending report{/t}</h4>
      </div>
      <div class="grid-body">
        <p>{t}Your newsletter was sent to the list of mails. Please find below the detailed report for each email and its status.{/t}</p>
        <table class="table">
          {foreach from=$sent_result item=result}
          <tr>
            <td>
              {$result[0]->name} &lt;{$result[0]->email}&gt;
              {if $result[1]}
              <span class="ok">{t}OK{/t}</span>
              {else}
              <span class="failed">{t}Failed{/t} - {$result[2]}</span>
              {/if}
            </td>
          </tr>
          {/foreach}
        </table>
      </div>
    </div>
  </div>
{/block}
