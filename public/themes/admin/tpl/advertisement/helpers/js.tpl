<script>
  var _onmaq = _onmaq || {};

  _onmaq.category       = '{{$category}}';
  _onmaq.cookieLifetime = {{$lifetime}};
  _onmaq.slots          = [ {{$positions}} ];
  _onmaq.url            = '{{$url}}';
</script>

<script>
  (function() {
    var am = document.createElement('script');
    am.type = 'text/javascript';
    am.src = '/assets/src/onm-am/am.{{$time}}.js';

    (document.getElementsByTagName('head')[0] ||
        document.getElementsByTagName('body')[0]).appendChild(am);
  })();
</script>
