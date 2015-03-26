{extends file="base/admin.tpl"}

{block name="content"}
<div class="content">

  {render_messages}

  <div id="info-page" >

    <div class="row">
      <div class="col-md6 col-vlg-3 col-sm-6">
        <div class=" tiles white no-padding">
          <div class="tiles green cover-pic-wrapper">
            <img src="http://revox.io/webarch/2.7/assets/img/cover_pic.png" alt="">
          </div>
          <div class="tiles white">
            <div class="row">
              <div class="col-md-3 col-sm-3">
                <div class="user-profile-pic">
                  <img width="69" height="69" data-src-retina="assets/img/profiles/avatar2x.jpg" data-src="assets/img/profiles/avatar.jpg" src="assets/img/profiles/avatar.jpg" alt="">
                </div>
                <div class="user-mini-description">
                  <h3 class="text-success semi-bold">
                    2548
                  </h3>
                  <h5>Users</h5>
                  <h3 class="text-success semi-bold">
                    457
                  </h3>
                  <h5>Mb of storage</h5>
                </div>
              </div>
              <div class="col-md-8 user-description-box  col-sm-8">
                <h4 class="semi-bold no-margin">{$instance->name}</h4>
                <h6 class="no-margin"><a href="http://{$instance->getMainDomain()}">{$instance->getMainDomain()}</a></h6>
                <br>
                <p><i class="fa fa-briefcase"></i>{$instance->created}</p>
                <p>
                <i class="fa fa-globe"></i>{implode(', ',$instance->domains)}
                </p>
                <p><i class="fa fa-file-o"></i>Download Resume</p>
                <p><i class="fa fa-envelope"></i>{$instance->contact_mail}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-vlg-3 col-sm-6">

        <div class="tiles blue m-b-10">
          <div class="tiles-body">
            <div class="tiles-title text-black">{t}INSTANCE INFO{/t} </div>

            <div class="widget-stats">
              <div class="wrapper transparent">
                <h4 class="no-margin">{t}Activated users{/t}</h4>
                <h5><span class="item-count semi-bold">{$instance->users} of {$max_users}</span></h5>
              </div>
            </div>
            <div class="widget-stats">
              <div class="wrapper transparent">
                <h4>{t}Media size{/t}</h4>
                <h5><span class="item-count animate-number semi-bold" data-value="{$instance->media_size}" data-animation-duration="700">{$instance->media_size|string_format:"%.2f"} MB</span></h5>
              </div>
            </div>
            <div class="widget-stats ">
              <div class="wrapper last">
                <h4>Support plan</h4>
                <h5><span class="item-count animate-number semi-bold" data-value="1547" data-animation-duration="700">{$instance->support_plan}&nbsp; <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="{$support_description}"></i></span></h5>
              </div>
            </div>
            <div class="progress transparent progress-small no-radius m-t-20" style="width:100%">
              <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="100%" style="width: 100%;"></div>
            </div>
            <div class="description"> <span class="text-white mini-description ">Owner email <span class="blend">{$instance->contact_mail}</span></span></div>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-vlg-3 col-sm-6">

        <div class="tiles gray m-b-10">
          <div class="tiles-body">
            <div class="tiles-title text-black">{t}OWNER{/t} </div>

            <div class="description"> <span class="text-white mini-description ">Owner email <span class="blend">{$instance->contact_mail}</span></span></div>
          </div>
        </div>
      </div>

    </div>

    <div>
      <h2>{t}Plans & Modules{/t}</h2>
      <p class="lead">{t}Here you can see a list of activated modules by plan{/t}</p>

      <form id="upgrade-form" method="POST" action="{url name=admin_client_send_upgrade_mail}">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab" data-toggle="tab">{t}Basic{/t}</a></li>
            {foreach $plans as $plan => $total}
            <li>
              <a href="#tab{$total@index}" data-toggle="tab">{t}{$plan}{/t} ({$total})</a>
            </li>
            {/foreach}
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab">
              <div class="basic modules">
                <h4><i class="icon-check"></i> {t}Basic{/t}</h4>
                {foreach $available_modules as $module}
                {if $module['plan'] eq Base}
                <div class="span4 element">
                  <label class="inline">
                    {if in_array($module['id'], $instance->activated_modules)}
                    <i class="icon-check"></i>
                    <input type="hidden" id="{$module['name']}" name="modules[{$module['name']}]" value="{$module['id']}">
                    {else}
                    <i class="icon-check-empty"></i>
                    <input type="hidden" id="{$module['name']}" name="modules[{$module['name']}]" value="{$module['id']}">
                    {/if}
                    {$module['name']}
                  </label>
                </div>
                {/if}
                {/foreach}
              </div>
            </div>
            {foreach $plans as $plan => $total}
            <div class="tab-pane" id="tab{$total@index}">
              <div class="modules">
                <label class="inline">
                  <h4>
                    <input type="checkbox" id="select_{$total@index}">
                    <span class="plan-title" id="{$plan}">{$plan}</span>
                  </h4>
                </label>
                {foreach $available_modules as $module}
                {if $module['plan'] eq $plan}
                <div class="span4 element">
                  <label class="inline">
                    <input type="checkbox" id="{$module['name']}" name="modules[{$module['name']}]" value="{$module['id']}"
                    {if in_array($module['id'], $instance->activated_modules)}
                    checked> {$module['name']} <span class="pending"
                    {if in_array($module['id'], $upgrade)}
                    style="display:inline;">({t}pending activation{/t})</span>
                    {/if}
                    {else}
                    > {$module['name']} <span class="pending"
                    {if in_array($module['id'], $downgrade)}
                    style="display:inline;">({t}pending deactivation{/t})</span>
                    {/if}
                    {/if}
                  </label>
                </div>
                {/if}
                {/foreach}
              </div>
            </div>
            {/foreach}
          </div>
        </div>
      </form>
      <div class="upgrade right">
        <button class="btn btn-large btn-success" type="submit" form="upgrade-form"
        {if $has_changes}
        disabled>{t}Waiting for upgrade{/t}
        <input type="hidden" name="waiting-upgrade" id="waiting-upgrade" value="1">
        {else}
        >{t}Upgrade instance{/t}
        {/if}
      </button>
    </div>
    <br>
    <br>
  </div>
</div>
</div>
{include file="stats/modals/_modal_upgrade_instance.tpl"}
{/block}

{block name="footer-js"}
<script type="text/javascript">
  $(document).ready(function (){
    var checkedArray = [];
    var isWaiting = $('#waiting-upgrade').val();

    $('.modules').each(function(){
      var item = $(this).find('.element');

      if (item.length == item.find('input:checkbox:checked').length) {
        $(this).find('[id^=select]').prop('checked', true);
      };

      $(this).each(function(){
        checkedArray.push(
          $(this).find('.element input:checkbox:checked').length
          );
      });
    });

    $('.modules .element input:checkbox').on('change', function(){
      $(this).parent().find('span').toggle();
      var plan = $(this).parents('.modules');
      if (plan.find('.element').length ==
        plan.find('.element input:checkbox:checked').length
        ) {
        plan.find('[id^=select]').prop('checked', true);
    } else {
      plan.find('[id^=select]').prop('checked', false);
    }
  });

    $('.upgrade button').on('click', function(e){
      e.preventDefault();
      if (isWaiting != 1) {
        var item = $('.modules');
        var hasChanges = false;
        item.each(function(){
          var elem = $(this).find('.element');
          elem.each(function(){
            log($(this).find('.pending').css('display'));
            if ($(this).find('.pending').css('display') == 'inline') {
              hasChanges = true;
            };
          });
        });

        if (!hasChanges) {
          $('.warnings-validation').html(
            '<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>'+
            '{t}You have to select at least one module to upgrade.{/t}'+
            '</div>'
            );
        } else {
          $("#modal-upgrade-instance").modal('show');
        }
      } else {
        $('.warnings-validation').html(
          '<div class="alert alert-notice"><button class="close" data-dismiss="alert">×</button>'+
          '{t}You have already requested an upgrade.{/t}'+
          '</div>'
          );
      }
    });

$('[id^=select]').on('click', function(){
  var root   = $(this).parents('.modules');
  var status = this.checked;

  root.find('input:checkbox').each(function(){
    var element   = $(this).parent().find('span.pending');
    var isChecked = $(this).is(':checked');
    $(this).prop('checked', status);
    if (isChecked != $(this).is(':checked')) {
      element.toggle();
    }
  });
});
});
</script>
{/block}
