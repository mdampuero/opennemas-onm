<!-- Quantcast Choice. Consent Manager Tag -->
<script async=true>
/* eslint-disable */
  var elem = document.createElement('script');
  elem.src = 'https://quantcast.mgr.consensu.org/cmp.js';
  elem.async = true;
  var scpt = document.getElementsByTagName('script')[0];
  scpt.parentNode.insertBefore(elem, scpt);
  (function() {
    var gdprAppliesGlobally = true;
    function addFrame() {
      if (!window.frames['__cmpLocator']) {
        if (document.body) {
          var body = document.body,
            iframe = document.createElement('iframe');
          iframe.style = 'display:none';
          iframe.name = '__cmpLocator';
          body.appendChild(iframe);
        } else {
          // In the case where this stub is located in the head,
          // this allows us to inject the iframe more quickly than
          // relying on DOMContentLoaded or other events.
          setTimeout(addFrame, 5);
        }
      }
    }
    addFrame();
    function cmpMsgHandler(event) {
      var msgIsString = typeof event.data === 'string';
      var json;
      if(msgIsString) {
        json = event.data.indexOf('__cmpCall') != -1 ? JSON.parse(event.data) : {};
      } else {
        json = event.data;
      }
      if (json.__cmpCall) {
        var i = json.__cmpCall;
        window.__cmp(i.command, i.parameter, function(retValue, success) {
          var returnMsg = { '__cmpReturn': {
          'returnValue': retValue,
          'success': success,
          'callId': i.callId
          }};
          event.source.postMessage(msgIsString ?
          JSON.stringify(returnMsg) : returnMsg, '*');
        });
      }
    }
    window.__cmp = function (c) {
      var b = arguments;
      if (!b.length) {
        return __cmp.a;
      } else if (b[0] === 'ping') {
        b[2]({ 'gdprAppliesGlobally': gdprAppliesGlobally,
          'cmpLoaded': false }, true);
      } else if (c == '__cmp') {
        return false;
      } else {
        if (typeof __cmp.a === 'undefined') {
          __cmp.a = [];
        }
        __cmp.a.push([].slice.apply(b));
      }
    }
    window.__cmp.gdprAppliesGlobally = gdprAppliesGlobally;
    window.__cmp.msgHandler = cmpMsgHandler;
    if (window.addEventListener) {
      window.addEventListener('message', cmpMsgHandler, false);
    }
    else {
      window.attachEvent('onmessage', cmpMsgHandler);
    }
  })();

  window.__cmp('init', {
    'Language': '{$lang}',
    'Initial Screen Body Text Option': 1,
    'Publisher Name': '{$site}',
    'Publisher Purpose IDs': [ 1, 2, 3, 4, 5 ],
    'Consent Scope': 'service',
    'No Option': false,
    'Display Persistent Consent Link': false,
    'Default Value for Toggles': 'on',
    'UI Layout': 'banner',
    'Initial Screen Title Text': '{t}We value your privacy{/t}',
    'Initial Screen Reject Button Text': '{t}I Do Not Accept{/t}',
    'Initial Screen Accept Button Text': '{t}I Accept{/t}',
    'Initial Screen Purpose Link Text': '{t}Show Purposes{/t}',
    'Initial Screen Body Text': '{t}We and our partners use technology such as cookies on our site to personalize content and ads, provide social media features, and analyze our traffic. Click below to consent to the use of this technology by us and these 3rd parties across the web. You can change your mind and revisit your consent choices at anytime by returning to this site.{/t}',
    'Purpose Screen Title Text': '{t}We value your privacy{/t}',
    'Purpose Screen Header Title Text': '{t}Privacy Settings{/t}',
    'Purpose Screen Body Text': '{t}We and our partners use technology such as cookies on our site to personalize content and ads, provide social media features, and analyze our traffic. You can toggle on or off your consent preference based on purpose for all companies listed under each purpose to the use of this technology across the web. You can change your mind and revisit your consent choices at anytime by returning to this site.{/t}',
    'Purpose Screen Enable All Button Text': '{t}Enable all purposes{/t}',
    'Purpose Screen Vendor Link Text': '{t}See full vendor list{/t}',
    'Purpose Screen Cancel Button Text': '{t}Cancel{/t}',
    'Purpose Screen Save and Exit Button Text': '{t}Save & Exit{/t}',
    'Vendor Screen Title Text': '{t}We value your privacy{/t}',
    'Vendor Screen Body Text': '{t}We and our partners use technology such as cookies on our site to personalize content and ads, provide social media features, and analyze our traffic. You can toggle on or off your consent preference for each company to the use of this technology across the web. You can change your mind and revisit your consent choices at anytime by returning to this site.{/t}',
    'Vendor Screen Reject All Button Text': '{t}Reject All{/t}',
    'Vendor Screen Accept All Button Text': '{t}Accept All{/t}',
    'Vendor Screen Purposes Link Text': '{t}Back to purposes{/t}',
    'Vendor Screen Cancel Button Text': '{t}Cancel{/t}',
    'Vendor Screen Save and Exit Button Text': '{t}Save & Exit{/t}',
  });
</script>
<!-- End Quantcast Choice. Consent Manager Tag -->
