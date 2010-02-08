{if $num_col eq '2'}
 <div class="zonaVisorVideos" style="width:300px;height:250px;">
    <div class="cuerpoVisorVideos"  style="width:300px;height:230px;">
        <div class="contVisorVideo" style="height:200px; width:270px; margin-left: 5px; margin-right: 5px;">
                        <object width="270" height="200" >
                                <param name="movie" value="http://www.youtube.com/v/{$video->videoid}"></param>
                                <param name="allowFullScreen" value="true"></param>
                                <param name="allowscriptaccess" value="always"></param>
                                <embed src="http://www.youtube.com/v/{$video->videoid}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="270"></embed>
                        </object>
        </div>
    </div>
</div>
{else}
<div class="zonaVisorVideos">
    <div class="cuerpoVisorVideos"  style="height:310px;">
        <div class="contVisorVideo" style="height:280px;">
                        <object width="370" height="268">
                                <param name="movie" value="http://www.youtube.com/v/{$video->videoid}"></param>
                                <param name="allowFullScreen" value="true"></param>
                                <param name="allowscriptaccess" value="always"></param>
                                <embed src="http://www.youtube.com/v/{$video->videoid}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="370" height="268"></embed>
                        </object>                      
        </div>    
    </div>
</div>
{/if}