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
        width: 100%;
      }

      .btn-social .fa {
        margin-right: 5px;
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
    <div class="social-connections">
      <div id="test"></div>
      {if $current_user_id == $user->id}
      <button class="btn btn-social btn-{$resource}" data-url="{hwi_oauth_login_url name={$resource}}{if !empty($target)}?_target_path={$target}{/if}" onclick="connect(this)" type="button">
            <i class="fa fa-{$resource}"></i>
            {t}Connect with{/t} {if $resource == 'facebook'}Facebook{else}Twitter{/if}
          </button>
      {/if}
    </div>
    <script>
      function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
      }

      function connect(btn) {
        var win = window.open(
          btn.getAttribute('data-url'),
          btn.getAttribute('id'),
          'height=400, width=400'
        );

        var interval = window.setInterval(function() {
          if (win == null || win.closed) {
            window.clearInterval(interval);

            parent.postMessage({
              success: true,
              cookie: getCookie('__onm_sess')
            }, '*');
          }
        }, 1000);
      };
    </script>
  </body>
</html>
