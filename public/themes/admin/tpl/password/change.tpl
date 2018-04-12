{extends file="login/login.tpl"}

{block name="login_content"}
  <form action="{url name=backend_password_update token=$token}" method="POST">
    <div class="container">
      <div class="row login-container animated fadeInUp">
        <div class="col-md-6 col-md-offset-3 tiles white no-padding">
          <div class="p-t-30 p-l-20 p-b-10 xs-p-t-10 xs-p-l-10 xs-p-b-10">
            <h2 class="normal center">{t escape=off}Recover <strong>password</strong>{/t}</h2>
            <p>{t}Please enter your new password in both fields below, and then click Submit.{/t}</p>
          </div>
          <div class="tiles grey p-t-20 p-b-20 text-black">
            <div class="row m-l-10 m-r-10">
              <div class="col-sm-12">
                {render_messages}
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><span class="fa fa-lock"></span></span>
                    <input autofocus class="form-control" name="password" placeholder="{t}Password{/t}" required tabindex="1" type="password">
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <span class="input-group-addon"><span class="fa fa-lock"></span></span>
                    <input class="form-control" name="password-verify" placeholder="{t}Password confirmation{/t}" required tabindex="2" type="password">
                  </div>
                </div>
                <div class="form-group text-right">
                  <a href="{url name=backend_password_reset}" class="recover_pass btn btn-link">{t domain=base}Forgot Password?{/t}</a>
                  <button class="btn btn-primary" type="submit" id="forgot-pass-button">
                    {t}Submit{/t}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
