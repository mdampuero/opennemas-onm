{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style>
    label {
        text-weigth:normal;
    }
    </style>
{/block}

{block name="content"}

<form action="{if $user->id}{url name=admin_newsletter_subscriptor_update id=$user->id}{else}{url name=admin_newsletter_subscriptor_create}{/if}" method="post" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {t}Newsletter{/t} ::
                    {if !is_object($user)}
                        {t}Creating subscriptor{/t}
                    {else}
                        {t 1=$user->name}Editing subscriptor "%1"{/t}
                    {/if}
                </h2>
            </div>
            <ul class="old-button">
                <li>
                    <button type="submit" name="continue" value="1">
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save{/t}" alt="{t}Save{/t}"><br />
                        {if is_object($user)}{t}Update{/t}{else}{t}Save{/t}{/if}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_newsletter_subscriptors}" class="admin_add" title="{t}Cancel{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" ><br />
                        {t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="form-horizontal panel">
            <fieldset>
                <div class="control-group">
                    <label for="email" class="control-label">{t}Email{/t}</label>
                    <div class="controls">
                        <input type="email" id="email" name="email" value="{$user->email|default:""}" class="input-xlarge"/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="name" class="control-label">{t}Name{/t}</label>
                    <div class="controls">
                        <input type="text" id="name" name="name" value="{$user->name|default:""}" class="input-xlarge validate-alpha"/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="firstname" class="control-label">{t}Surname{/t}</label>
                    <div class="controls">
                        <input type="text" id="firstname" name="firstname" value="{$user->firstname|default:""}" class="input-xlarge validate-alpha"/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="lastname" class="control-label">{t}LastName{/t}</label>
                    <div class="controls">
                        <input type="text" id="lastname" name="lastname" value="{$user->lastname|default:""}" class="input-xlarge validate-alpha"/>
                    </div>
                </div>

                <div class="control-group">
                    <label for="subscribed" class="control-label">{t}Subscribed{/t}</label>
                    <div class="controls">
                        <select name="subscription" id="subscribed">
                            <option value="1" {if is_null($user->subscription) || $user->subscription eq 1 }selected="selected"{/if}>{t}Yes{/t}</option>
                            <option value="0" {if (isset($user->subscription)) && $user->subscription eq 0}selected="selected"{/if}>{t}No{/t}</option>
                        </select>
                        <div class="help-block">{t}If is subscribed, the user email address will be available on the account provider{/t}</div>
                    </div>
                </div>

                <div class="control-group">
                    <label for="subscribed" class="control-label">{t}Activated{/t}</label>
                    <div class="controls">
                        <select name="status" id="activated">
                            <option value="2" {if is_null($user) || $user->status eq 2}selected="selected"{/if}>{t}Yes{/t}</option>
                            <option value="3" {if $user->status eq 3 || $user->status eq 1}selected="selected"{/if}>{t}No{/t}</option>
                        </select>
                        <div class="help-block">{t}If is activated means that the user is ready to receive newsletters{/t}</div>
                    </div>
                </div>

            </fieldset>
        </div>
    </div>
</form>
{/block}
