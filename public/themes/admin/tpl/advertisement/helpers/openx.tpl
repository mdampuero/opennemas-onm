<html>
  <head>
    <style>
      body {
        margin: 0;
        overflow: hidden;
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
    <script type="text/javascript" src="{{$url}}/www/delivery/spcjs.php?cat_name={{$category}}"></script>
    <script type="text/javascript">
      OA_show('zone_{{$id}}');
    </script>
  </body>
</html>
