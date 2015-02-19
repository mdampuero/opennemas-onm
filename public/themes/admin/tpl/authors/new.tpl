{extends file="base/admin.tpl"}

{block name="footer-js" append}
{script_tag src="/onm/jquery.password-strength.js" common=1}
{script_tag src="/onm/bootstrap-fileupload.min.js" common=1}
<script>
jQuery(document).ready(function($){
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
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-user fa-lg"></i>
                            {t}Authors{/t}
                        </h4>
                    </li>
                    <li class="quicklinks">
                        <span class="h-seperate"></span>
                    </li>
                    <li class="quicklinks">
                        <h5>
                            {if isset($user->id)}
                                {t}Editing author{/t}
                            {else}
                                {t}Creating author{/t}
                            {/if}
                        </h5>
                    </li>
                </ul>
                <div class="all-actions pull-right">
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <a class="btn btn-link" href="{url name=admin_opinion_authors}">
                                <i class="fa fa-reply"></i>
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="h-seperate"></span>
                        </li>
                        <li class="quicklinks">
                            <button action="submit" class="btn btn-primary" name="action" value="validate">
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
        <div class="grid simple">
            <div class="grid-title">
                <h4>{t}User info{/t}</h4>
            </div>
            <div class="grid-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label" for="name">
                                {t}Display name{/t}
                            </label>
                            <div class="controls">
                                <input class="form-control" id="name" maxlength="50" name="name" required="required" type="text" value="{$user->name|default:""}"  />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">
                                {t}Email{/t}
                            </label>
                            <div class="controls">
                                <input class="form-control" id="email" name="email" placeholder="test@example.com" required="required" type="email" value="{$user->email|default:""}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="meta[twitter]">
                                {t}Twitter user{/t}
                            </label>
                            <div class="controls">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </span>
                                    <input class="form-control" id="prependedInput" type="text" placeholder="{t}Username{/t}" id="meta[twitter]" name="meta[twitter]" value="{$user->meta['twitter']|default:""}">
                                </div>
                            </div>
                        </div>
                         {is_module_activated name="BLOG_MANAGER"}
                            <div class="control-group">
                                <label class="control-label">{t}View as Blog{/t}</label>
                                <div class="controls">
                                    <div class="checkbox">
                                        <input type="checkbox" name="meta[is_blog]" id="meta[is_blog]" {if $user->meta['is_blog'] eq 1}checked="checked"{/if}>
                                        <label for="meta[is_blog]">
                                            {t}If this option is activated page author will be showed as blog{/t}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        {/is_module_activated}
                        <div class="form-group">
                            <label class="form-label" for="url">
                                {t}Other Blog Url{/t}
                            </label>
                            <div class="controls">
                                <input class="form-control" id="url" name="url" placeholder="http://" value="{$user->url|default:""}" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bio">
                                {t}Short Biography{/t}
                            </label>
                            <div class="controls">
                                <textarea class="form-control" id="bio" name="bio" rows="3">{$user->bio|default:""}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="meta[bio_description]">
                                {t}Biography{/t}
                            </label>
                            <div class="controls">
                                <textarea class="form-control" id="meta[bio_description]" name="meta[bio_description]" rows="3">{$user->meta['bio_description']|default:""}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="meta[inrss]">
                                {t}Show in RSS{/t}
                            </label>
                            <div class="controls">
                                <div class="checkbox">
                                    <input type="checkbox" name="meta[inrss]" id="meta[inrss]" {if !isset($user->meta['inrss']) || $user->meta['inrss'] eq 'on' || $user->meta['inrss'] eq '1'} checked="checked"{/if}>
                                    <label for="meta[inrss]">
                                        {t}If this option is activated this author will be showed in rss{/t}
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="username" value="{$user->username|default:""}">
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
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
                                <a href="#" class="btn btn-danger fileupload-exists delete" data-dismiss="fileupload">
                                    <i class="fa fa-trash-o"></i>
                                    {t}Remove{/t}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
{/block}
