<html>
<head>
  <meta charset="UTF-8">
  <title>Document</title>
  <link href="/assets/components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  {block name="header-css"}
    {stylesheets src="@Common/components/bootstrap/dist/css/bootstrap.min.css,
        @AdminTheme/less/_social.less" filters="cssrewrite,less" output="social"}
      <style>
        html, body {
          background: none;
          margin:0 auto;
          min-height:0;
          overflow-y:hidden;
          padding:0;
        }
      </style>
    {/stylesheets}
  {/block}
</head>
<body>
  <div class="social-connections text-center">
    {if $connected}
      <div class="social-orb">
        {if $user->photo->name}
            <img src="{$smarty.const.MEDIA_IMG_PATH_URL}{$user->photo->path_file}/{$user->photo->name}" alt="{t}Photo{/t}"/>
        {else}
          {gravatar email=$user->email image_dir=$_template->getImageDir() image=true size="100"}
        {/if}
      </div>
      <h5>
        {assign var="meta" value="{$resource}_realname"}
        {$user->meta[$meta]}
      </h5>
      <p>
        {if $current_user_id == $user->id}
          {t 1=$resource_name}Your account is connected to %1.{/t}
        {/if}
      </p>
      <p>
        <a href="{url name=admin_acl_user_social_disconnect id=$user->id resource=$resource style='orb'}" title="{t}Disconnect from Facebook{/t}" class="btn btn-danger">{t}Disconnect{/t}</a>
      </p>
    {else}
      {if $current_user_id == $user->id}
        <button class="social-orb {$resource}-orb" data-url="{hwi_oauth_login_url name={$resource}}" onclick="connect(this)" type="button">
          <i class="fa fa-{$resource} fa-3x"></i>
        </button>
        <p>
          {if $resource == 'facebook'}
            {t 1="Facebook"}Click here to associate your %1 account to login into Opennemas with it.{/t}
          {else}
            {t 1="Twitter"}Click here to associate your %1 account to login into Opennemas with it.{/t}
          {/if}
        </p>
      {else}
        <p>Only the user can connect their social accounts with Opennemas.</p>
      {/if}
    {/if}
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

        if (win.success) {
          window.location.reload();
        }
      }
    }, 1000);
  };
</script>
{/block}
</html>
