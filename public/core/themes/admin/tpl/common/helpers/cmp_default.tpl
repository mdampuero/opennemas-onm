<script src="https://cmp-cdn.cookielaw.org/scripttemplates/otSDKStub.js" type="text/javascript" charset="UTF-8" data-cmp-builder-version="2.0.0" data-domain-script="bottom-panel-dark-stack-global-ot"></script>
<script>
    function OptanonWrapper() {
      var div = document.getElementById('overlay-cookies');

      if (!div) {
        div = document.createElement('div');

        div.className = 'overlay';
        div.id        = 'overlay-cookies';

        document.body.appendChild(div);
      }

      if (!document.getElementById('cmp-builder-features-script')) {
        var cmpScript = document.createElement('script');
        var script1   = document.getElementsByTagName('script')[0];

        cmpScript.src   = 'https://cmp-cdn.cookielaw.org/consent/cmp-features/cmp-features.js';
        cmpScript.id    = 'cmp-builder-features-script';
        cmpScript.async = false;
        cmpScript.type  = 'text/javascript';

        script1.parentNode.insertBefore(cmpScript, script1);
      }

      var divsdk = document.getElementById('onetrust-banner-sdk');

      if (!divsdk || divsdk.style.visibility == 'hidden'){
        document.body.removeChild(div);
      }
    }
</script>
<style>
  .overlay {
    background-color: #57585A;
    position: fixed;
    width: 100%;
    height: 100%;
    z-index: 2147483640;
    top: 0px;
    left: 0px;
    opacity: .5;
}
</style>
