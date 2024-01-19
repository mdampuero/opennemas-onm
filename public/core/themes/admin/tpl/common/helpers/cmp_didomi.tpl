{if !empty($mrfpassCmp)}
<script type="text/javascript">
  if (!window.didomiConfig) {
     window.didomiConfig = {};
  }
  if (!window.didomiConfig.notice) {
     window.didomiConfig.notice = {};
  }
  if (!window.didomiConfig.app) {
    window.didomiConfig.app = {};
  }

  // time in seconds pay to reject should be valid. In this example, one day in seconds is 24h * 60min * 60s = 86400.
  window.didomiConfig.app.deniedConsentDuration = 86400;
  window.didomiConfig.notice.enable = false;
</script>
{/if}
<script type="text/javascript">
var apikey = "{$apikey}";
var id = "{$id}"
{literal}
window.gdprAppliesGlobally=false;(function(){function n(e){if(!window.frames[e]){if(document.body&&document.body.firstChild){var t=document.body;var r=document.createElement("iframe");r.style.display="none";r.name=e;r.title=e;t.insertBefore(r,t.firstChild)}else{setTimeout(function(){n(e)},5)}}}function e(r,i,o,c,s){function e(e,t,r,n){if(typeof r!=="function"){return}if(!window[i]){window[i]=[]}var a=false;if(s){a=s(e,n,r)}if(!a){window[i].push({command:e,version:t,callback:r,parameter:n})}}e.stub=true;e.stubVersion=2;function t(n){if(!window[r]||window[r].stub!==true){return}if(!n.data){return}var a=typeof n.data==="string";var e;try{e=a?JSON.parse(n.data):n.data}catch(t){return}if(e[o]){var i=e[o];window[r](i.command,i.version,function(e,t){var r={};r[c]={returnValue:e,success:t,callId:i.callId};n.source.postMessage(a?JSON.stringify(r):r,"*")},i.parameter)}}if(typeof window[r]!=="function"){window[r]=e;if(window.addEventListener){window.addEventListener("message",t,false)}else{window.attachEvent("onmessage",t)}}}e("__tcfapi","__tcfapiBuffer","__tcfapiCall","__tcfapiReturn");n("__tcfapiLocator");(function(e,t){var r=document.createElement("link");r.rel="preconnect";r.as="script";var n=document.createElement("link");n.rel="dns-prefetch";n.as="script";var a=document.createElement("link");a.rel="preload";a.as="script";var i=document.createElement("script");i.id="spcloader";i.type="text/javascript";i["async"]=true;i.charset="utf-8";var o="https://sdk.privacy-center.org/"+e+"/loader.js?target_type=notice&target="+t;r.href="https://sdk.privacy-center.org/";n.href="https://sdk.privacy-center.org/";a.href=o;i.src=o;var c=document.getElementsByTagName("script")[0];c.parentNode.insertBefore(r,c);c.parentNode.insertBefore(n,c);c.parentNode.insertBefore(a,c);c.parentNode.insertBefore(i,c)})(apikey, id)})();
{/literal}
</script>

