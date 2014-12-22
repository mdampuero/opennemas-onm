{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        {render_messages}
        <div id="info-page" class="container-narrow">
            <div class="masthead">
                <h1 class="muted center">{$instance->name}</h1>
            </div>

            <hr>

            <div class="jumbotron">
                <h2>{t}Instance information{/t}</h2>
                <p class="lead">{t}Here you can see some information about your instance{/t}</p>
            </div>

            <div class="row-fluid info center">
                <div class="row-fluid">
                    <div class="span3">
                        <p><i class="icon-user awesome-circles awesome-background-green"></i></p>
                        <h4>{t}Owner email{/t}</h4>
                        <p>{$instance->contact_mail}</p>
                    </div>
                    <div class="span3">
                        <p><i class="icon-group awesome-circles awesome-background-red"></i></p>
                        <h4>{t}Activated users{/t}</h4>
                        <p>{$instance->users}</p>
                    </div>
                    <div class="span3">
                        <p><i class="icon-hdd awesome-circles awesome-background-purple"></i></p>
                        <h4>{t}Media size{/t}</h4>
                        <p>{$instance->media_size|string_format:"%.2f"} MB</p>
                    </div>
                    <div class="span3">
                        <p><i class="icon-bullhorn awesome-circles awesome-background-leaf-green"></i></p>
                        <h4>{t}Support plan{/t}</h4>
                        <p>{$instance->support_plan}</p>
                    </div>
                </div>
            </div>

            <hr>

            <div class="jumbotron">
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
                <div class="warnings-validation"></div>
            </div>
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