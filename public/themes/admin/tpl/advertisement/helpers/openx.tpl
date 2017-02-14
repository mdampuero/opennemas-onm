<!DOCTYPE html>
<!--[if lt IE 8]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <title>html</title>
    <style>
      body {
        margin: 0;
        padding: 0;
      }
    </style>
    <script type="text/javascript">
      var OA_zones = {
        'zone_{{$id}}': {{$openXId}}
      };
    </script>
  </head>
  <body>
    <a target="_blank" href="{{$url}}" rel="nofollow">
      <img alt="{{$category}}" src="{{$url}}" width="{{$width}}" height="{{$height}}"/>
    </a>
  </body>
  <script type="text/javascript" src="{{$url}}/www/delivery/spcjs.php?cat_name={{$category}}"></script>
  <script type="text/javascript">
    OA_show('zone_{{$id}}');
  </script>
</html>
