{extends file="base/admin.tpl"}

{block name="content"}
<form action="{if $user->id}{url name=admin_newsletter_subscriptor_update id=$user->id}{else}{url name=admin_newsletter_subscriptor_create}{/if}" method="post">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Newsletters{/t}
                    </h4>
                </li>
                <li class="quicklinks"><span class="h-seperate"></span></li>
                <li class="quicklinks">
                    <h5>{if !is_object($user)}
                        {t}Creating subscriptor{/t}
                    {else}
                        {t 1=$user->name}Editing subscriptor "%1"{/t}
                    {/if}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name=admin_newsletter_subscriptors}" title="{t}Go back to newsletter manager{/t}">
                            <span class="fa fa-reply"></span>
                            {t}Newsletters{/t}
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks">
                        <button type="submit" class="btn btn-primary">
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

    <div class="grid simple">
        <div class="grid-body">
            <div class="row">
                <div class="col-md-7">
                    <div class="form-group">
                        <label for="email" class="form-label">{t}Email{/t}</label>
                        <div class="controls">
                            <input type="email" id="email" name="email" value="{$user->email|default:""}" class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">{t}Name{/t}</label>
                        <div class="controls">
                            <input type="text" id="name" name="name" value="{$user->name|default:""}" class="form-control validate-alpha"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="firstname" class="form-label">{t}Surname{/t}</label>
                        <div class="controls">
                            <input type="text" id="firstname" name="firstname" value="{$user->firstname|default:""}" class="form-control validate-alpha"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="form-label">{t}LastName{/t}</label>
                        <div class="controls">
                            <input type="text" id="lastname" name="lastname" value="{$user->lastname|default:""}" class="form-control validate-alpha"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subscribed" class="form-label">{t}Subscribed{/t}</label>
                        <div class="controls">
                            <select name="subscription" id="subscribed">
                                <option value="1" {if is_null($user->subscription) || $user->subscription eq 1 }selected="selected"{/if}>{t}Yes{/t}</option>
                                <option value="0" {if (isset($user->subscription)) && $user->subscription eq 0}selected="selected"{/if}>{t}No{/t}</option>
                            </select>
                            <div class="help-block">{t}If is subscribed, the user email address will be available on the account provider{/t}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subscribed" class="form-label">{t}Activated{/t}</label>
                        <div class="controls">
                            <select name="status" id="activated">
                                <option value="2" {if is_null($user) || $user->status eq 2}selected="selected"{/if}>{t}Yes{/t}</option>
                                <option value="3" {if $user->status eq 3 || $user->status eq 1}selected="selected"{/if}>{t}No{/t}</option>
                            </select>
                            <div class="help-block">{t}If is activated means that the user is ready to receive newsletters{/t}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    </div>
</form>
{/block}
