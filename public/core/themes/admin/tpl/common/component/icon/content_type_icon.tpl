{if $iFlagIcon}
  <i class="fa" ng-class="{ 'fa-file-text-o': {$iField}.content_type_name == 'article', 'fa-quote-right': {$iField}.content_type_name == 'opinion', 'fa-pie-chart': {$iField}.content_type_name == 'poll', 'fa-file': {$iField}.content_type_name == 'static_page', 'fa-envelope': {$iField}.content_type_name == 'letter', 'fa-paperclip': {$iField}.content_type_name == 'attachment', 'fa-film': {$iField}.content_type_name == 'video', 'fa-camera': {$iField}.content_type_name == 'album', 'fa-photo': {$iField}.content_type_name == 'photo', 'fa-shield fa-flip-vertical': {$iField}.content_type_name === 'obituary'  }"></i>
{/if}
{if $iFlagName}
  <span ng-if="{$iField}.content_type_name == 'album'">{t}Album{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'article'">{t}Article{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'attachment'">{t}Attachment{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'event'">{t}Event{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'letter'">{t}Letter{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'obituary'">{t}Obituary{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'opinion'">{t}Opinion{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'photo'">{t}Photo{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'poll'">{t}Poll{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'static_page'">{t}Static Page{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'video'">{t}Video{/t}</span>
  <span ng-if="{$iField}.content_type_name == 'widget'">{t}Widget{/t}</span>
{/if}
