<html>
  <head>
    <style>
      body {
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
  </head>
  <body>
    <div class="content">
      <div style="width:{{$width}}px; height:{{$height}}px; margin: 0 auto;">
        <div style="position: relative; width: {{$width}}px; height: {{$height}}px;">
          <div style="left:0px;top:0px;cursor:pointer;background-color:#FFF; filter:alpha(opacity=0);opacity:0;position:absolute;z-index:100;width: {{$width}}px;height:{{$height}}px;" onclick="javascript:window.open('{{$url}}', '_blank'); return false;"></div>
          <object width="{{$width}}" height="{{$height}}" >
            <param name="wmode" value="transparent" />
            <param name="movie" value="{{$url}}" />
            <param name="width" value="{{$width}}" />
            <param name="height" value="{{$height}}" />
            <embed src="{{$src}}" width="{{$width}}" height="{{$url}}" SCALE="exactfit" wmode="transparent"></embed>
          </object>
        </div>
      </div>
    </div>
  </body>
</html>
