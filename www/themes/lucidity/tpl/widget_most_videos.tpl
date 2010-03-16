

<div class="border-dotted">

    <div id="tabs" class="inner-video-tabs">
        <ul>
                <li><a href="#tab-related"><span>Relacionados</span></a></li>
                <li><a href="#tab-new"><span>Novos</span></a></li>
        </ul>
        <div id="tab-related">
            {php} for( $i = 1; $i <= 10; $i++ ){
                echo '
            <div class="tab-thumb-video clearfix">
                <img src="images/video/thumb-video-'.(rand(1,50)%5+1).'.jpg" />
                <div class="tab-thumb-video-shortitle">Política</div>
                <div class="tab-thumb-video-title">Bla Bla Bla Bla</div>
            </div>';
            } {/php}



        </div>
        <div id="tab-new">
           {php} for( $i = 1; $i <= 10; $i++ ){
            echo '
            <div class="tab-thumb-video clearfix">
                <img src="images/video/thumb-video-'.rand(1,5).'.jpg" />
                <div class="tab-thumb-video-shortitle">Deporte</div>
                <div class="tab-thumb-video-title">Bla Bla Bla Bla</div>
            </div>';
            } {/php}
        </div>
    </div>
    <div id="public_lateral_video">  {include file="wdiget_ad_button.tpl"}</div>
</div>