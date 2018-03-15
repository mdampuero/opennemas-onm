{extends file="login/login.tpl"}

{block name="login_content"}
  <div class="container">
    <div class="row login-container animated fadeInUp">
      <div class="col-md-6 col-md-offset-3 tiles white no-padding">
        <div class="p-t-30 p-l-20 p-b-10 xs-p-t-10 xs-p-l-10 xs-p-b-10">
          <h2 class="normal center">{t escape=off}Reset <strong>password</strong>{/t}</h2>
        </div>
        <div class="tiles grey text-grey p-b-20 p-l-20 p-r-20 p-t-20">
          <p>
            {t}We've sent an e-mail to{/t}:<strong>&nbsp;&nbsp;{$user->email}</strong>.
          </p>
          <p>
            {t}Please check your e-mail now for a message with the subject line "Password reminder".{/t}
          </p>
          <p>
            {t}If you use e-mail filtering or anti-spam software,please make sure our e-mail is not filtered or blocked.{/t}
          </p>
        </div>
      </div>
    </div>
  </div>
{/block}
