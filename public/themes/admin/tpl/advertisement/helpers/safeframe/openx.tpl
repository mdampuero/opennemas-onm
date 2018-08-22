<html>
  <head>
    <style>
      body {
        display: table;
        margin: 0;
        overflow: hidden;
        padding: 0;
        text-align: center;
      }

      img {
        height: auto;
        max-width: 100%;
      }
    </style>
    <script>
      var OA_zones = {
        'zone_{{$id}}': {{$openXId}}
      };
    </script>
  </head>
  <body>
    <div class="content">
      <script src="{{$url}}/www/delivery/spcjs.php?cat_name={{$category}}"></script>
      <script>
        OA_show('zone_{{$id}}');
      </script>
    </div>
  </body>
</html>
