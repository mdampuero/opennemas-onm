<div class="wizard">
    <ul class="nav nav-wizard">
      <li class="active"><a href='#step1'>1. {t}Welcome{/t}</a></li>
      <li class="disabled"><a href='#step2'>2. {t}Terms of use{/t}</a></li>
      <li class="disabled"><a href="#step3">3. {t}Getting help{/t}</a></li>
      <li class="disabled"><a href="#step4">4. {t}Social network{/t}</a></li>
      <li class="disabled"><a href="#step5">5. {t}Ready!{/t}</a></li>
    </ul>
    <div class="row-fluid wizard-content" id="step5">
        <div class="span12"></div>
        <div class="span12">
            <div class="row-fluid">
                <div class="span12 text-center">
                    <span class="icon-ok-sign icon-huuge color-green"></span>
                    <h3 class="final-message">{t}And that's it!{/t}</h3>
                    <h4>You can start to use your newspaper.</h4>
                </div>
            </div>
            <div class="row-fluid wizard-buttons">
                <div class="span12">
                    <div class="pull-left">
                        <button data-target="#step3" class="btn btn-primary btn-large activate">{t}Previous{/t}</button>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-success btn-large" href="{url name='admin_getting_started_finish'}" style="padding: 11px 19px;">{t}Finish{/t}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid wizard-content" id="step1">
        <div class="span12">
            <div class="row-fluid">
                <div class="span12"></div>
            </div>
            <div class="row-fluid presentation">
                <div class="span8">
                    <p>{t}Welcome to your new newspaper. Now you will be able to publish your own news, articles and
                    take part of the information around everyone.{/t}</p>
                    <p>{t}Before starting to work on it you have to perform some tasks, sush as setup your social networks
                    and get some information about how to use Opennemas{/t}</p>
                    <p>{t}Hope you will enjoy opennemas!{/t}</p>
                    <div class="click-next">{t}Click next to continue{/t} <i class="icon">{image_tag src="/gstarted/arrow-left.png" common=1}</i></div>
                </div>
                <div class="span4 screenshot">
                    {image_tag src="/gstarted/screenshot-little.png" class="opennemas-screenshot" common=1}
                </div>
            </div>
            <div class="row-fluid wizard-buttons">
                <div class="span12">
                    <div class="pull-right">
                        <button data-target="#step2" class="btn btn-primary btn-large activate">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid wizard-content" id="step2">
        <div class="span12">
            <div class="terms">
                <p>{t}In order to use Opennemas you must accept the terms of use:{/t}</p>
                <div class="text-center">
                    <iframe class="terms-of-use" src="/terms_of_use.html" frameborder="0"></iframe>
                </div>
                <div class="accept-terms">
                    <label for="accept-terms" class="checkbox">
                        <input name="accept-terms" id="accept-terms" type="checkbox" data-url="{url name='admin_getting_started_accept_terms'}">
                        {t}Accept the terms of use{/t}
                    </label>
                </div>
            </div>
            <div class="row-fluid wizard-buttons">
                <div class="span12">
                    <div class="pull-left">
                        <button data-target="#step1" class="btn btn-primary btn-large activate">{t}Previous{/t}</button>
                    </div>
                    <div class="pull-right">
                        <button data-target="#step3" class="btn btn-primary btn-large activate disabled terms-accepted">{t}Next{/t}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid wizard-content" id="step3">
        <div class="span12">
            <div class="row-fluid">
                <div class="span12"></div>
            </div>
            <div class="row-fluid">
                <div class="span8">
                    <p>{t}You can read and learn how to use your Opennemas by using our online documentation and videos.
                    Take a look around and you will find it.{/t}</p>
                    <ul>
                        <li>{t escape=off}Our <a href="http://help.opennemas.com/" target="_blank">knownledge base</a> has manuals and howtos about how to create contents and improve your newspaper.{/t}</li>
                        <li>{t escape=off}See our <a href="http://www.youtube.com/user/OpennemasPublishing" target="_blank">video tutorials</a> for getting step-by-step guidance.{/t}</li>
                    </ul>

                    <p>{t escape=off}If you need further information you can always contact us by using the  <span class="icon-large icon-question-sign"></span> Help button in the upper right corner.{/t}</p>
                </div>
                <div class="span4 text-center">
                    <span class="icon-large icon-question-sign icon-huuge"></span>
                </div>
            </div>
            <div class="row-fluid wizard-buttons">
                <div class="span12">
                    <div class="pull-left">
                        <button data-target="#step2" class="btn btn-primary btn-large activate">{t}Previous{/t}</button>
                    </div>
                    <div class="pull-right">
                        <!-- <a href="{url name='admin_getting_started_finish'}">{t}Skip tour{/t}</a> -->
                        <button data-target="#step4" class="btn btn-primary btn-large activate">{t}Next{/t}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid wizard-content" id="step4">
        <div class="span12">
            <div class="row-fluid">
                <div class="form-horizontal social-connections">
                    <p>{t}Do you have a Facebook or a Twitter account?{/t}</p>
                    <p>{t}Then you can associate those accounts to access your opennemas. It will make easier to get into your  administration panel.{/t}</p>
                    <div class="control-group">
                        <label class="control-label" for="facebook_login">{t}Facebook{/t}</label>
                        <div class="controls">
                            <iframe src="{url name=admin_acl_user_social id=$user->id resource='facebook'}" frameborder="0" style="width:100%;overflow-y:hidden;"></iframe>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">{t}Twitter{/t}</label>
                        <div class="controls">
                            <iframe src="{url name=admin_acl_user_social id=$user->id resource='twitter'}" frameborder="0" style="width:100%;overflow-y:hidden;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-fluid wizard-buttons">
                <div class="span12">
                    <div class="pull-left">
                        <button data-target="#step3" class="btn btn-primary btn-large activate">{t}Previous{/t}</button>
                    </div>
                    <div class="pull-right">
                        <button data-target="#step5" class="btn btn-primary btn-large activate">{t}Next{/t}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
