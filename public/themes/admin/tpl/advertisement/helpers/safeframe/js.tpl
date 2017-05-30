<script>
  var _onmaq = _onmaq || {};

  _onmaq.width          = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
  _onmaq.category       = '{{$category}}';
  _onmaq.debug          = {{$debug}};
  _onmaq.device         = _onmaq.width < 768 ? 'phone' : (_onmaq.width < 990 ? 'tablet' : 'desktop');
  _onmaq.extension      = '{{$extension}}';
  _onmaq.cookieLifetime = {if empty($lifetime)}86400{else}{{$lifetime}}{/if};
  _onmaq.slots          = [ {{$positions}} ];
  _onmaq.url            = '{{$url}}';
  _onmaq.strings        = {
    'entering': '{t}Entering on the requested page{/t}',
    'mark':     '{t}Advertisement{/t}',
    'skip':     '{t}Skip advertisement{/t}'
  };

  window.onload = function() {
    var devices = [ 'desktop', 'tablet', 'smartphone' ];

    for (var i = 0; i < devices.length; i++) {
      if (devices[i] === _onmaq.device) {
        var slots = document.getElementsByClassName('hidden-' + devices[i]);

        for (var j = 0; j < slots.length; j++) {
          slots[j].remove();
        }
      }
    }
  };

  (function() {
    var am = document.createElement('script');

    am.type  = 'text/javascript';
    am.src   = '/assets/src/onm-am/am.{{$time}}.js';
    am.async = true;

    (document.getElementsByTagName('head')[0] ||
        document.getElementsByTagName('body')[0]).appendChild(am);
  })();
</script>
