<!-- OneTrust CMP Builder start -->
<script src="https://cmp-cdn.cookielaw.org/scripttemplates/otSDKStub.js" type="text/javascript" charset="UTF-8" data-cmp-builder-version="2.0.0" data-domain-script="bottom-panel-dark-stack-global-ot"></script>
<script>
    function OptanonWrapper() {
        addCmpBuilderFeatures();

        function addCmpBuilderFeatures() {
            var existingCmpFeaturesScript = document.getElementById('cmp-builder-features-script');
            if (!existingCmpFeaturesScript) {
                let  div= document.createElement("div");
                div.className += "overlay";
                div.id = "overlay-cookies";
                document.body.appendChild(div);

                var cmpFeaturesScript = document.createElement('script'),
                    script1 = document.getElementsByTagName('script')[0];
                cmpFeaturesScript.src = "https://cmp-cdn.cookielaw.org/consent/cmp-features/cmp-features.js";
                cmpFeaturesScript.setAttribute('id', 'cmp-builder-features-script');
                cmpFeaturesScript.async = false;
                cmpFeaturesScript.type = 'text/javascript';
                script1.parentNode.insertBefore(cmpFeaturesScript, script1);
            }

            let divsdk = document.getElementById("onetrust-banner-sdk");
            if (!divsdk || divsdk.style.visibility == "hidden"){
                let div = document.getElementById("overlay-cookies");
                if(div){
                  document.body.removeChild(div);
                }
            }
        }
    }
</script>
<!-- OneTrust CMP Builder end -->
<style>
  .ot-floating-button,
  button#onetrust-reject-all-handler {
    display: none !important;
  }

  .overlay {
    background-color:#57585A;
    position: fixed;
    width: 100%;
    height: 100%;
    z-index: 2147483647;
    top: 0px;
    left: 0px;
    opacity: .5;
}
</style>
