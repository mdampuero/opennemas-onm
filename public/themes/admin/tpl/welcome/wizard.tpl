<div class="wizard">
    <ul class="nav nav-wizard">
      <li class="active"><a href='#step1'>1. {t}Welcome{/t}</a></li>
      <li class="disabled"><a href='#step2'>2. {t}Terms of use{/t}</a></li>
      <li class="disabled"><a href="#step3">3. {t}Help{/t}</a></li>
      <li class="disabled"><a href="#step4">4. {t}Connect{/t}</a></li>
      <li class="disabled"><a href="#step5">5. {t}Finish{/t}</a></li>
    </ul>
    <div class="row-fluid wizard-content" id="step1">
        <div class="span12">
            <div class="row-fluid">
                <div class="span12">
                    <h3>{t}Welcome!{/t}</h3>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <h4>
                        <strong>
                            Are you new in Opennemas? If you need some help getting started
                            to create awesome content, check out our online user documentation.
                        </strong>
                    </h4>
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
            <div class="row-fluid">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="span12 text-center">
                            <iframe class="terms-of-use" src="/terms_of_use.html" frameborder="0"></iframe>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="accept-terms">
                                <label for="accept-terms" class="checkbox">
                                    <input name="accept-terms" id="accept-terms" type="checkbox" data-url="{url name='admin_getting_started_accept_terms'}">
                                    {t}Accept the terms of use{/t}
                                </label>
                            </div>
                        </div>
                    </div>
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
                <div class="span12">
                    <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. Donec non enim in turpis pulvinar facilisis. Ut felis. Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus</p>
                </div>
            </div>
            <div class="row-fluid wizard-buttons">
                <div class="span12">
                    <div class="pull-left">
                        <button data-target="#step2" class="btn btn-primary btn-large activate">{t}Previous{/t}</button>
                    </div>
                    <div class="pull-right">
                        <a href="{url name='admin_getting_started_finish'}">{t}Skip tour{/t}</a>
                        <button data-target="#step4" class="btn btn-primary btn-large activate">{t}Next{/t}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid wizard-content" id="step4">
        <div class="span12">
            <div class="row-fluid">
                <div class="span12">
                    <h4>
                        Connect Opennemas with your social networks
                    </h4>
                </div>
            </div>
            <div class="row-fluid">
                <div class="form-horizontal social-connections">
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
    <div class="row-fluid wizard-content" id="step5">
        <div class="span12">
            <div class="row-fluid">
                <div class="span12">

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
</div>
