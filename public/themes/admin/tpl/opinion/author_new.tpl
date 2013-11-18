{extends file="base/admin.tpl"}

{block name="footer-js" append}
{script_tag src="/onm/jquery.password-strength.js" common=1}
{script_tag src="/onm/bootstrap-fileupload.min.js" common=1}
<script>
jQuery(document).ready(function($){
    $('[rel=tooltip]').tooltip({ placement: 'bottom', html: true });

    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    // PAssword strength checker
    var strength = $('#password').passStrength({
        userid: '#login'
    });

    // Avatar image uploader
    $('.fileupload').fileupload({
        name: 'avatar',
        uploadtype:'image'
    });

    $('.delete').on('click', function(){
        $('.file-input').val('0');
    })
});
</script>
{/block}

{block name="header-css" append}
{css_tag href="/bootstrap/bootstrap-fileupload.min.css" common=1}
<style type="text/css">
label {
    font-weight:normal;
}
.avatar, .user-info {
    vertical-align: top;
    display:inline-block;
}
.avatar {
    margin-right:20px;
}
.avatar img {
    width:150px;
    height:150px;
}

.tooltip {
    max-width:160px;
}
/* Styles for password strenght */
.alert-pass {
    background: #F8D47A url("/assets/images/alert-ok-small.png") no-repeat 16px;
    display: inline-block;
    margin: 0;
    padding: 5px 15px 5px 50px;
    margin-left: 10px;
    border-radius: 5px;
    font-size: 14px;
    color: white;
}
.alert-pass.alert-success { background: #468847 url("/assets/images/alert-ok-small.png") no-repeat 16px; }
.alert-pass.alert-error { background: #B22222 url("/assets/images/alert-error-small.png") no-repeat 16px; }
</style>
{/block}

{block name="content"}
<form action="{if isset($user->id)}{url name=admin_opinion_author_update id=$user->id}{else}{url name=admin_opinion_author_create}{/if}" method="POST" enctype="multipart/form-data" id="formulario" autocomplete="off">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{if isset($user->id)}{t}Editing author{/t}{else}{t}Creating author{/t}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <button action="submit"  name="action" value="validate">
                        <img src="{$params.IMAGE_DIR}save.png" title="{t}Save and exit{/t}" alt="{t}Save and exit{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_opinion_authors}">
                        <img src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        {render_messages}
            <div id="basic">
                <div class="avatar">
                    <div class="fileupload {if $user->photo}fileupload-exists{else}fileupload-new{/if}" data-provides="fileupload">
                        {if $user->photo->name}
                        <div class="fileupload-preview thumbnail" style="width: 140px; height: 140px;">
                            <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$user->photo->path_file}/{$user->photo->name}" alt="{t}Photo{/t}"/>
                        </div>
                        {else}
                        <div class="fileupload-preview thumbnail" style="width: 140px; height: 140px;" rel="tooltip" data-original-title="{t escape=off}If you want a custom avatar sign up in <a href='http://www.gravatar.com'>gravatar.com</a> with the same email address as you have here in OpenNemas{/t}">
                            {gravatar email=$user->email image_dir=$params.IMAGE_DIR image=true size="150"}
                        </div>
                        {/if}
                        <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new">{t}Add new photo{/t}</span>
                                <span class="fileupload-exists">{t}Change{/t}</span>
                                <input type="file"/>
                                <input type="hidden" name="avatar" class="file-input" value="1">
                            </span>
                            <a href="#" class="btn fileupload-exists delete" data-dismiss="fileupload">{t}Remove{/t}</a>
                        </div>
                    </div>
                </div>

                <div class="user-info form-vertical">
                    <fieldset>
                        <div class="control-group">
                            <label for="name" class="control-label">{t}Display name{/t}</label>
                            <div class="controls">
                                <input type="text" id="name" name="name" value="{$user->name|default:""}" class="input-xlarge required" required="required" maxlength="50"/>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="control-group">
                            <label for="email" class="control-label">{t}Email{/t}</label>
                            <div class="controls">
                                <input class="input-xlarge" id="email" type="email" name="email" placeholder="test@example.com"  value="{$user->email|default:""}" required="required">
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="meta[twitter]" class="control-label">{t}Twitter user{/t}</label>
                            <div class="controls">
                                <div class="input-prepend">
                                    <span class="add-on">@</span>
                                    <input class="span2" id="prependedInput" type="text" placeholder="{t}Username{/t}" id="meta[twitter]" name="meta[twitter]" value="{$user->meta['twitter']|default:""}">
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="meta[twitter]" class="control-label">{t}View as Blog{/t}</label>
                            <div class="controls">
                                <input type="checkbox" name="meta[is_blog]" id="meta[is_blog]" {if $user->meta['is_blog'] eq 1}checked="checked"{/if}>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="url" class="control-label">{t}Other Blog Url{/t}</label>
                            <div class="controls">
                                <input type="text" name="url" id="url" placeholder="http://" value="{$user->url|default:""}" class="input-xxlarge" >
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="bio" class="control-label">{t}Short Biography{/t}</label>
                            <div class="controls">
                                <textarea id="bio" name="bio" rows="3" class="input-xxlarge">{$user->bio|default:""}</textarea>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="meta[bio_description]" class="control-label">{t}Biography{/t}</label>
                            <div class="controls">
                                <textarea id="meta[bio_description]" name="meta[bio_description]" rows="3" class="input-xxlarge">{$user->meta['bio_description']|default:""}</textarea>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="meta[inrss]" class="control-label">{t}Show in RSS{/t}</label>
                            <div class="controls">
                                <label class="checkbox">
                                    <input type="checkbox" name="meta[inrss]" id="meta[inrss]" {if !isset($user->meta['inrss']) || $user->meta['inrss'] eq 'on'} checked="checked"{/if}>
                                    {t}If this option is activated this author will be showed in rss{/t}
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="username" value="{$user->username|default:""}">
                    </fieldset>

                </div>
            </div>
        </div>
    </div>
</form>
{/block}
