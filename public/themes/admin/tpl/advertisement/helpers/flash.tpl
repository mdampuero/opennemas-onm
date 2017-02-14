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
  </head>
  <body>
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
  </body>
</html>
<style>body { margin: 0; padding: 0 }</style>
