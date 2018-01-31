<html>
  <head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href="/assets/components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <style>
      html, body {
        margin:0 auto;
        padding:0;
        min-height:0;
        overflow-y:hidden;
      }

      .arrow {
        padding: 10px 15px;
      }

      .btn-social,
      .btn-social:focus,
      .btn-social:hover {
        color: #fff;
      }

      .btn-social .fa:after {
        content: " ";
        border-right: 1px solid #fff;
        height: 30px;
        margin: 0 5px 0 8px;
      }

      .btn-facebook {
        background-color: #3b5998;
        border-color: #3b5998;
      }

      .btn-twitter {
        background-color: #00aced;
        border-color: #00aced;
      }

      .social-connection {
        list-style: none;
        margin: 0 0 10px;
      }

      .social-connection li {
        float: left;
      }
    </style>
  </head>
  <body>
    {block name="header-js"}{/block}
    <div class="social-connections">
      {if $connected}
        <p>
        {if $current_user_id == $user->id}
          {t 1=$resource_name}Your account is connected to %1.{/t}
          <a href="{url name=admin_acl_user_social_disconnect id=$user->id resource=$resource}" title="{t}Disconnect from Facebook{/t}" class="disconnect">{t}Disconnect{/t}</a>
        {/if}
        </p>
        <ul class="social-connection clearfix">
          <li>
            {if $user->photo->name}
              <div style="width: 40px; height: 40px;">
                <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$user->photo->path_file}/{$user->photo->name}" alt="{t}Photo{/t}"/>
              </div>
            {else}
              <div style="width: 40px; height: 40px;">
                {gravatar email=$user->email image_dir=$_template->getImageDir() image=true size="40"}
              </div>
            {/if}
          </li>
          <li class="arrow"><i class="fa fa-arrow-right fa-lg"></i></li>
          <li>
            <div class="btn btn-social btn-{$resource}">
              <i class="fa fa-{$resource}"></i>
              {assign var="meta" value="{$resource}_realname"}
              {$user->{$meta}}
            </div>
          </li>
        </ul>
        <p>{t 1=$resource_name}Allows you to login into Opennemas with %1{/t}.</p>
      {else}
      {if $current_user_id == $user->id}
        <button class="btn btn-social btn-{$resource}" data-url="{hwi_oauth_login_url name={$resource}}{if !empty($target)}?_target_path=/auth/social/{$resource}/connect{/if}" onclick="connect(this)" type="button">
          <i class="fa fa-{$resource}"></i> {t}Connect with {if $resource == 'facebook'}Facebook{else}Twitter{/if}{/t}
        </button>
        <div class="help-block">{t}Associate your {if $resource == 'facebook'}Facebook{else}Twitter{/if} account to login into Opennemas with it.{/t}</div>
      {else}
        <p>Only the user can connect their social accounts with Opennemas.</p>
      {/if}
      {/if}
      {render_messages}
    </div>
  </body>
  {block name="footer-js"}
  <script>
function connect(btn) {
  var win = window.open(
      btn.getAttribute('data-url'),
      btn.getAttribute('id'),
      'height=400, width=400'
      );

  var interval = window.setInterval(function() {
    if (win == null || win.closed) {
      window.clearInterval(interval);
      window.location.reload();
    }
  }, 1000);
};
  </script>
  {/block}
</html>
