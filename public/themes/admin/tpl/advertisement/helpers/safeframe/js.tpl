<script type="text/javascript">
  var _onmaq = _onmaq || {};

  _onmaq.width          = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
  _onmaq.category       = '{{$category}}';
  _onmaq.contentId      = '{{$contentId}}';
  _onmaq.adGroup        = '{{$adGroup}}';
  _onmaq.debug          = {{$debug}};
  _onmaq.device         = _onmaq.width < 768 ? 'phone' : (_onmaq.width < 992 ? 'tablet' : 'desktop');
  _onmaq.extension      = '{{$extension}}';
  _onmaq.cookieLifetime = {if empty($lifetime)}86400{else}{{$lifetime}}{/if};
  _onmaq.slots          = [ {{$positions}} ];
  _onmaq.url            = '{{$url}}';
  _onmaq.strings        = {
    'entering': '{t}Entering on the requested page{/t}',
    'mark':     '{t}Advertisement{/t}',
    'skip':     '{t}Skip advertisement{/t}'
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
