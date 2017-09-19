{extends file="login/login.tpl"}

{block name="login_content"}
  {if $mailSent}
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
  {else}
    <form id="loginform" name="loginform" action="{url name=admin_acl_user_recover_pass}" method="POST">
      <div class="container">
        <div class="row login-container animated fadeInUp">
          <div class="col-md-6 col-md-offset-3 tiles white no-padding">
            <div class="p-t-30 p-l-20 p-b-10 xs-p-t-10 xs-p-l-10 xs-p-b-10">
              <h2 class="normal center">{t escape=off}Reset <strong>password</strong>{/t}</h2>
              <p>{t}Enter your e-mail address and click Submit to recover your password.{/t}<br></p>
            </div>
            <div class="tiles grey p-t-20 p-b-20 text-black">
              <div class="row m-l-10 m-r-10">
                <div class="col-sm-12">
                  {render_messages}
                  <div class="form-group">
                    <div class="controls">
                      <input type="email" class="form-control" name="email" required autofocus placeholder="{t}example@example.com{/t}">
                    </div>
                  </div>

                  <div class="form-group text-right">
                    <div class="controls">
                      <a href="{url name=admin_login}" class="btn btn-link">{t}Go back to login{/t}</a>
                      <button type="submit" class="btn btn-primary" id="recover-pass-button">{t}Submit{/t}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <input type="hidden" id="_token" name="_token" value="{$token}">
      <input type="hidden" id="_referer" name="_referer" value="{$referer}">
    </form>
  {/if}
{/block}
