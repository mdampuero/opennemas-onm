20140908 / 2014-09-08 
==================
Summary:
 * Manager
    * Port manager to SPA with webarch theme
    * Improve manager listing information
 * Use Assetic for minimize/unify CSS and JS files
 * Implement system to use server assets
 * Documentation for all API services (symfony services)
 * Review original strings and translations

List of bugfixes:
 e10c288 Remove protocol from URL 2014-09-01
 5599685 Remove protocol from URLs 2014-09-01
 b525a02 Added command to clean failed spool messages 2014-08-28
 7764f5f Fixed countBy sql contruction on EntityManager2014-08-27
 56207ef Create command to extract images from articles body/summary 2014-08-27
 92b8054 Call event content.update when voting on a poll 2014-08-25
 5a578b3 Fixed inner image href uri in newsML base template 2014-08-22
 e8a7e14 Select the right namespace when deleting an instance 2014-08-21
 ac30b99 Comment x-tags in OpinionController to avoid caching 2014-08-21
 175735b Changed logic for sitemap images to improve performance 2014-08-20
 e9e169c Fix preg_replace to work with both simple and double quote in render_video plugin 2014-08-20
 387f2c5 Fix total iteraction counter for content_types on export command 2014-08-20
 70ae5a7 Added improvements on ExportContentsCommand and newsML base template 2014-08-20
 ea3f9d8 Added sitemap video and image to robots.txt 2014-08-19
 99bb3e7 Fixed if conditions when saving comments 2014-08-18
 cd41171 Check if exists comments option moderation before use it    2014-08-18
 b603094 Updated spanish and galician translations   2014-08-18
 3aab3ce Check comments moderation option before save and set status 2014-08-18
 1229ff2 Use cache for content metas and use it in frontpages    2014-08-14
 8225782 Fix invalid references in Article and Tags frontend controllers 2014-08-12
 3f80416 Check if redirect URL is empty before redirect  2014-08-12

20140812 / 2014-08-12 
==================
Summary:
 * Frontend code QA review
 * New instance creation logic: opennemas.com redirects to the instance after create it
 * Manager
    * Port instance listing to angular
    * Revamp instance editing form
 * Authentication
    * Facebook and Twitter auth in backend
    * New login form design (backend, manager)
  * New getting started page in backend
 * Views to own table
 * Simplification of Onm\MachineSearcher (now without fuzzy searches, just category matching)
 * Refactorization of InstanceManager and Instance, move instance creation to separate class
 * Added RSS provider in News Agency module
 * Refactorization of frontpage cache contents (only save the list of content ids, not complete contents)

List of changes:
 * Ignore filter if the result sql is empty
 * Refactoring of Varnish MessagePusher to return server responses
 * Fix internal name generation from instance domains
 * Fix service class name
 * Fix parameter name.
 * Fix naming collision
 * Complete, update and unify comments syntax and minor changes.
 * Move content manager initialization out of the if block
 * Check if criteria is an array before using it
 * Search by name and domain when name filter exists
 * Add option to get the created date from instance database
 * Allow master users to see if users have their social accounts connected
 * Updated exportContents command and newsml templates
 * Fix date creation from format in getViews
 * Fix main domain offset in Instance::getMainDomain
 * Fix main domain access in Instance class
 * Fix invalid search criteria in author frontpage
 * Merge tag 'fix_oa_zones_output' into develop
 * Merge branch 'hotfix/fix_oa_zones_output'
 * Check openx_zone_id and googledfp_unit_id while building OA_zones
 * Merge tag 'fix_contents_homepage_category' into develop
 * Merge branch 'hotfix/fix_contents_homepage_category'
 * Check content before loading properties
 * Check if content is null before update category_name
 * Fix join error when $criteria is a string
 * Merge branch 'hotfix/fix_float_ads_sync' into develop
 * Merge branch 'hotfix/fix_float_ads_sync'
 * Fixed url for external floating advertisement in webService
 * Set elements per page to 25 by default
 * Add parameter to configure elements per page on init
 * Update last_login field type and date format
 * Check database to avoid to search access categories for user
 * Add piwik parameters to parameters.yml.dist
 * Update countBy to build SQL statement properly
 * Skip getting started for masters
 * Merge branch 'develop' of ssh://git.openhost.es:23911/onm into develop
 * Move cookies overlay message from bottom to top
 * Merge tag 'fix_category_frontpage_contents_fetching' into develop
 * Added capistrano and capistrano-symfony to Gemfile
 * Convert internal name to lower to fix redirections
 * Remove explode in domains attribute
 * Fixed date format getting most view contents
 * Fix typos in user social action in backend
 * Updated Spanish and Galician translations
 * Updated Spanish and Galician translations
 * Added .development to .gitignore
 * Updated Spanish and Galician translations
 * Ignore spaces in domains field while loading instance and remove spaces in instance listing
 * Change domain type to VARCHAR(500) in onm-instances
 * Remove preventDefault from submit event handler
 * Implement EquatableInterface to compare roles when comparing users during authentication
 * Remove plain text password from login form
 * Hide social networks tab when creating a new user
 * Get content views when getting content quick info
 * Fix typo in not available user error message
 * Fix js error in backend login page
 * Improve error message when logging in with a social account not connected to any user
 * Redirect frontend requests to main domain
 * Fix social networks connection
 * Rename dbConn variable to conn
 * Fix login form to save password in browser before MD5 encoding
 * Add service to update user in session on every request
 * Update current_instance property when loading instance
 * Add missing contents counter update
 * Add cursor pointer to created and last_login columns
 * Rename dbConn variable to conn
 * Fix Rss elements ordering in news agency due to invalid file date
 * Improve getting started l&f
 * Delete activated_modules setting when instance is updated
 * Checked module activated in video controller
 * Checked module activated in letter controller
 * Checked module activated in form controller
 * Checked module activated in album controller
 * Add contents to getting-started page in backend
 * Allow to specify the bundle and common paths in image_tag smarty plugin
 * Merge tag 'unrequire_auth_news_agency' into develop
 * Merge tag 'fix_frontpage_cache_handling' into develop
 * Merge tag 'refactor_frontpage_cache_handling' into develop
 * Merge tag 'add_rss_provider_in_news_agency' into develop
 * Fix 'activated' property saving in InstanceController:update action
 * Use proper instance cache name in InstanceLoaderListener
 * Fix notice in Backend:ErrorController
 * Remove commented service from framework configuration files
 * Fix wrong showing of the symfony toolbar in manager section
 * Merge branch 'hotfix/fix_float_ads_and_newsmlg1' into develop
 * Upgrade application dependencies
 * Fix wrong bootstrap-nav-wizard css url file in backend theme
 * Enable development mode in the app by file presence in app root.
 * Merge branch 'hotfix/fix_frontpage_wrong_save_positions' into develop
 * Only calculate instance stats for specific content types
 * Fix notice in console instance:update command
 * Apply number format for current used storage in an instance form
 * Fix invalid updated column definition in schema-instance.yml
 * Load current_instance property on InstanceLoaderListener
 * Merge branch 'feature/instance_refactoring' into develop
 * Merge branch 'develop' into feature/instance_refactoring
 * Merge branch 'feature/refactor_sitemap_and_rss' into develop
 * Add missing exceptions for instance manager
 * Change LONGTEXT fields to TEXT fields
 * Add missing fields to manager schema.
 * Use new InstanceManager features when creating instances from opennemas.com
 * Add actions to create user and configure instance while instance creation
 * Fix wrong contents counter update
 * Fix wrong exception parameter
 * Fixed simple join on EntityManager and improved Sitemap controller
 * Update actions to use new instance manager features properly
 * Update checkInternalName to use the domain as internal name if internal_name is empty
 * Fix InstanceCreator method calls.
 * Fix external settings save process, remove old methods and improve internal name checking.
 * Fix assets backup and instance backup actions
 * Update field order in schema
 * Refactored RSS and Sitemap controllers.
 * Update to use Instance, InstanceManager and InstanceCreator after refactoring
 * Move actions from InstanceManager to InstanceCreator
 * Add missing cache save action and update used exceptions
 * Update to use new exceptions properly
 * Move and create extra exceptions in vendor\Onm\Exception
 * Inject services, add missing cache actions, rename variables and update documentation
 * Fix wrong cache delete in fetchInstance
 * Extract instance routes to a separated file
 * Remove explode as domains are already exploded
 * Add magic method to redirect function calls to database connection
 * Fix last_login definition to allow null values
 * Add extra columns and actions to hide/show that columns to instance listing
 * Update command to use InstanceManager after refactoring
 * Update the way to fetch the instance after refactoring
 * Refactor IntanceManager
 * Define all properties and INSTANCE_UNIQUE_NAME in Intance
 * Update manager database schema
 * Update findOne function
 * Fix 'use' statement to escape database name
 * Fix schemas to change LONGTEXT to TEXT fields
 * fixed render function with inherited widgets
 * Fixed widget hierarchy
 * Fix save instance settings when editing it from manager
 * Complete the command to update onm-instances database
 * Add missing internal_name property when loading the instance
 * Add emails to manager schema and fix wrong names
 * Merge branch 'hotfix/fix_agency_parse_xml_warning' into develop
 * Merge branch 'hotfix/fix_ads_blog_pages' into develop
 * Render subcategories creating menu
 * Fixed get director data in opinion controller
 * Fixed template cache in archive
 * Remove twitter field from user form
 * Restore instance listing to look like the old one
 * Add route requirements to avoid collisions
 * Fix user avatar in navbar
 * Fix domain editing when creating a new instance
 * Add countBy function and fix instance listing
 * Add actions to show/hide columns
 * Create command to update instances in onm-instances
 * Update manager database schema
 * Add Dbal and perform refactoring in instance and settings managers
 * Fix filters and remove old javascript
 * Add sort function to controller
 * Fix init and add sorting when clicking in table headers
 * Check instance domains and activated modules
 * Activate edge cache in OpinionController:show action
 * Enable framework-wise to enable edge cache while rendering content from controllers
 * Updated default database dumps
 * Deactivate failing Content::setNumViews tests due to latest refactor
 * Drop no longer required FormController::init() method
 * Deleted white space rendering tags
 * Customize items per page in opinion configuration and fixed order in opinion frontpage
 * Fix syntax error due to wrong merge done
 * Merge branch 'feature/views_to_table' into develop
 * Delete old facebook API keys
 * Changed default cache_handler to Memcache in parameters.yml.dist
 * Change Facebook API key and secret to a test app
 * Allow to fetch advertisements in Form and Letter controllers
 * Fixed RSS checkbox in author edit
 * Merge branch 'hotfix/fix_notice_agency_handler' into develop
 * Update getViews to allow an id or an array of ids as parameter
 * Fix criteria in getMostViewedContent and getAllMostViewed
 * Fix setViews function to always insert in database
 * Fix isField flag initialization in parseFilter function
 * Update content manager to use new join approach
 * Remove static join and use new base manager functions
 * Update base manager to support joins
 * Add missing content category for content with id equals to 50
 * Delete old facebook API keys
 * Changed default cache_handler to Memcache in parameters.yml.dist
 * Change Facebook API key and secret to a test app
 * Merge branch 'hotfix/fix_ads_letter_and_form' into develop
 * Check headers and update content views in stats action
 * Remove views from model
 * Create repository for content views
 * Merge branch 'hotfix/fixed-edit-author-rss-checkbox' into develop
 * Fix category filter to use name instead of id
 * Update functions related to content views
 * Add a join between contents with content_views in findBy
 * Update instance schema
 * Merge branch 'feature/manager_with_angular' into develop
 * Merge branch 'develop' into feature/manager_with_angular
 * Little resorting in default parameters
 * Added main_domain setting in manager instance editing form
 * Add redirection based on base domain and protocol check in backend
 * Added default params in parameters.yml.dist to get them autocompleted
 * Show proper theme names in instance editing from in manager
 * Add main_domain field in manager database
 * Port instance domains editing to a table in manager
 * Improved UX for instance domains editing in manager
 * Improve instance form layout in manager
 * Deleted call to cache apc  in blog controller
 * Added missed css file for manager styles
 * Improved HTML layout in instance form in manager
 * Improve instance layout listing to minimize vertical space
 * Revamp layout of instance form in manager
 * Merge branch 'develop' into feature/manager_with_angular
 * Merge branch 'feature/getting_started' into develop
 * Merge branch 'develop' into feature/getting_started
 * Fix syntax errors
 * Add watch to check when elements per page value changes
 * Update templates to use angular
 * Move actions from manager to manager webservice
 * Rename routes and fix wrong bundle name
 * Move export route to manager webservice.
 * Merge branch 'hotfix/fix_adv_filter_categories' into develop
 * Fix redirection after social network accounts connection
 * Add action to disconnect accounts.
 * Reload iframe instead of updating button style
 * Fix redirection after login
 * Create a custom login success handler for Oauth login
 * Use social network connections with iframe in backend getting started wizard
 * Move account connection feature to an iframe
 * Fix not displayed languages in manager login page
 * Improve HTML layout in manager login page
 * Redirect to login when regenerate token is not valid in backend
 * Improve recover and regenerate pass HTML layout in backend
 * Moved disconnect button in backend user form
 * Improved social connections layout in backend user form
 * Layout improvements in backend login and account association with Fb and Tw
 * Move instance routes to a separated file
 * Move assets from admin theme to common folder
 * Assign available languages in backend login
 * Improve backend login layout in tablets
 * New backend login layout with social network buttons
 * Merge branch 'hotfix/fix_category_frontpage_action' into develop
 * Mark message to translate
 * Remove user cache when usermetas are created after login.
 * Add livereload configuration files
 * Change oauth authorization route to oauth login route
 * Throw exception when user is null or empty
 * Add login callback route to session
 * Rename login callback route
 * Merge branch 'hotfix/fix_agency_create_obj_empty' into develop
 * Change repeated album show route name to avoid 404
 * Change repeated album show route name to avoid 404
 * Improved  message sent in letter controllers
 * Added more items sending email in letter controllers
 * Merge branch 'hotfix/minor_fix_undefined_session_var' into develop
 * Merge branch 'hotfix/fix_paypal_sdk_version' into develop
 * Add redirect_url parameter in smarty function call
 * Fix redirect url generation in smarty function
 * Change route names
 * Merge branch 'hotfix/add_repository_agency_export' into develop
 * Remove javascript popup to run login in the current page
 * Add angular include to manager theme
 * Code review in Frontend Controllers
 * Add *WebService, Framework and FrontendMobile to the CI checks
 * Merge branch 'hotfix/fix_newsml_agency_handler' into develop
 * Merge branch 'hotfix/fix_image_path_photo' into develop
 * Merge branch 'hotfix/add_image_url_sitemap' into develop
 * Clear user cache when updating user in frontend
 * Use repository for fetching contents in ws Handlers
 * Allow to clear user cache to all kind of users
 * Fix exception in Onm\MachineSearcher due to not related contents
 * Add getAds function to Archive and Subscriptions controllers
 * Fixed category filter in AdvManager due to not filtering some
 * Refactor Onm\MachineSearcher service to simplify it
 * Added repository to webService Handler Frontpage
 * Improved render hashtag and tags conditions
 * fixed hashtag name
 * Fixed bug in AjaxPaginateAction in videoController frontend
 * Use repository in Machine Searcher class
 * Fixed notice checking ads img type
 * Sent more information in letter controller
 * Sent more data in letter controller
 * Include render hastag in plugin render tags
 * Fixed undefined vbles when cache is used
 * Add documentation for supportClass function
 * Move hwi functions to smarty plugins
 * Create global function to generate hwioauthbundle routes
 * Load account connections status in getting started wizard
 * Add auto-close popup window to redirect after login/connect accounts
 * Update template to add connect account section
 * Add social network buttons to login template
 * Update user to extend from OAuthUser
 * Update OnmOauthUserProvider
 * Add twitter as resource owner
 * Add function to find users by meta.
 * Merge branch 'hotfix/add_repository_ws_handlers' into develop
 * Update symfony to 2.4.4
 * Add support to log in with external accounts
 * Merge branch 'hotfix/delete_user_cache_on_update' into develop
 * Fix exception in Onm\MachineSearcher due to not related contents
 * Merge branch 'hotfix/add_ads_on_letter_and_newsletter' into develop
 * Merge branch 'hotfix/fix_ads_category_filter' into develop
 * Refactor Onm\MachineSearcher service to simplify it
 * Merge branch 'hotfix/add_repository_to_sync_frontpage' into develop
 * Improved render hashtag and tags conditions
 * fixed hashtag name
 * Merge branch 'hotfix/fix_video_paginated_action' into develop
 * Merge branch 'hotfix/add_repository_machine_search' into develop
 * Merge branch 'hotfix/fixed-checking-ads-type' into develop
 * Merge branch 'hotfix/send-name-in-letters' into develop
 * Merge branch 'hotfix/send-more-data-in-letters' into develop
 * Sent more data in letter controller
 * Include render hastag in plugin render tags
 * Fixed undefined vbles when cache is used
 * Merge tag 'fix_empty_content' into develop
 * Update default action and remove acceptTerms action
 * Add wizard to show when users access the instance for the first time
 * Update email template
 * Create user with password when creating new instance
 * Add token checking and function to set password
 * Fix parsing users from cache
 * Merge branch 'hotfix/fix_machine_suggested' into develop
 * Merge tag 'fix_frontpage_manager_save_action' into develop
 * Merge tag 'add_frontpage_restore_command' into develop
 * Merge tag 'fix_base_manager' into develop
 * Merge tag 'fix_get_filter' into develop
 * Merge tag 'fix_get_filter_sql' into develop
 * Update sorting in entity managers
 * Merge branch 'hotfix/fix_article_content_provider_category' into develop
 * Merge branch 'hotfix/fix_tags_sql_criteria' into develop
 * Merge branch 'hotfix/fix_opinion_cache_and_frontpage' into develop
 * Merge branch 'hotfix/interstitial-with-openX' into develop
 * Deleted ksort in comments order function, it is not necessary.
 * Merge branch 'hotfix/fixed-author-frontpage' into develop
 * Merge branch 'develop' of ssh://git.openhost.es:23911/onm into develop
 * Merge branch 'hotfix/fixed-editorial-counter' into develop
 * Fix wrong template names in frontend controllers
 * Merge branch 'hotfix/fix_repositories_array_order' into develop
 * Merge branch 'hotfix/fix_delete_user_cache_in_subscriber_event' into develop
 * Merge branch 'hotfix/fix_widget_content_provider' into develop
 * Merge tag 'fix_template_names_frontpage_opinion' into develop
 * Use repository in Frontend::OpinionController actions
 * Merge branch 'hotfix/fix_content_provider_category_in_frontpage' into develop
 * Merge tag 'fix_base_path_in_database_check_schema_command' into develop
 * Merge tag 'use_repository_to_opinion_show' into develop
 * Merge tag 'fix_notices_iconv_in_comments' into develop
 * Merge branch 'hotfix/fix_content_provider_category' into develop
 * Merge branch 'hotfix/fix_content_provider_in_frontpage' into develop
 * Merge branch 'hotfix/fixed-opinion-uri' into develop
 * Merge branch 'hotfix/fix_sql_syntax_find_cm' into develop
 * Merge tag 'fix_advertisement_warnings' into develop
 * Merge tag 'fix_warnings_advertisement_render' into develop
 * Merge tag 'fix_wrong_sql_database_checker' into develop
 * Merge tag '20140522' into develop
release/20140521 / 2014-05-21
==================

Summary:

 * Framework updates
    * swiftmailer 5.1 > 5.2
    * symfony to 2.4.3 > 2.4.4
    * monolog-bundle 2.5.1
    * monolog 1.6
 * New database schema checker with declarative database schema file
 * Use new DBAL-based repositories in the content-providers (frontpage, related contents and newsletter)
 * New application log channel to log events
 * Add cache/repositories in frontpage mobile
 * Fixes in Repository::findBy cache handling
 * Clean ups of unused methods in Controller and Model classes

List of changes:

 * Drop commented and unused ACL checking code from manager controllers
 * Drop Controller::checkAclOrForward method and clean unused code
 * Fixed wrong acl check for authors and opinions actions
 * ContentManager::getContentsForHomepageOfCategory do not return cache results directly if they are empty
 * Replace ContentManager::find_all to findAll method in VideosController
 * Improve SQL where clause order in ContentManager::find
 * Port available to content_status flag in ContentManager and SearchController
 * Rename database schema checker command
 * Fix category id reference in ArticlesController "related contents" provider
 * Fix errors in Article, Letters and Videos content providers
 * Fixed warning in explode function due to be array the second param
 * Added convert to utf-8 when creating comments and letters
 * Avoid to raise Fatal exceptions with all the warnings
 * Fix fatal error in Advertisement class due to wrong object context usage
 * Drop unused public/500.html file
 * Refactor Advertisement::findForPositionIdsAndCategory to use DBAL and repositories
 * Fix undefined variable name in Frontend::ArticlesController::show
 * Make app.php and app_dev.php files look like a symfony app
 * Disable firephp and chromephp monolog channels in dev environment
 * Unify app/config/app.yml into app/config/config.yml
 * Clean old .htaccess redirection rules
 * Drop unused app/container.php file
 * Move tests from app/ to src/Framework
 * Fix Repository/FrontpageManager::findBy error due to different implementation
 * Change project name in PHPUnit configuration file
 * Do not show 404 errors in the application log
 * Add instance name to application log records
 * Add log record formatter to include instance name
 * Remove getCountAndSlice and remove search action in ImagesController
 * Remove getSliceAndCount function from frontend controllers
 * Fix cache separator and added offset
 * Fix params access on a non-array
 * Change method call from count to countBy in UserController
 * Add new application.log channel and use it to log app events
 * Fix cache separator and update documentation
 * Fix cache separator in subscriber action
 * Fix response when removing items from trash.
 * Update backend controllers to use new repository features
 * Update repositories to improve filtering and allow complex conditions .
 * Update MonologBundle to v.v2.5.1) and Monolog to 1.6
 * Fix crash in Backend\FrontpagesController::preview
 * Use entity_repository to fetch Photos for relatedContents in frontend_article_show
 * Fix minor syntax errors in Controllers
 * Drop ContentManager::find_pages, fix little SQL in there
 * Update swiftmailer to v.5.2 and symfony to 2.4.4
 * Added category title in article inner in mobile version
 * Added smarty cache, cache service and entity repository in mobile version. Deleted unused sections.php
 * Delete not used instance database changes
 * Add option to allow to update database schema for manager
 * Update instance schema and add manager schema.
 * Added default instance schema and created a command to compare database schemas.
 * Reset connection after changing database parameters in DbalWrapper
 * Resort database changes in db/ files
 * Assign proper actual_category var to article and opinion template
 * Convert Content properties to UTF8 when reading them.

Hot fixes:

 * Merge branch 'hotfix/fix_letter_list_layout'
 * Merge branch 'hotfix/fixed-opinion-frontpage-admin'
 * Merge branch 'hotfix/updated_spanish_translations_newsletter'
 * Merge branch 'hotfix/updated_spanish_translations'
 * Merge branch 'hotfix/minor_fixes_newsletter_config'
 * Merge branch 'hotfix/convert_comments_utf8'
 * Merge branch 'hotfix/fix_order_date_opinion_frontpage'
 * Merge branch 'hotfix/add_hemeroteca_in_menu_modules'
 * Merge branch 'hotfix/fix-theme-hierarchy'
 * Merge branch 'hotfix/fix_swf_upload_and_render'
 * Merge branch 'hotfix/fix_minor_bug_newsagency_list'
 * Merge branch 'hotfix/fix_date_bug_firefox'
 * Merge branch 'hotfix/add_size_fonts_customize'
 * Merge branch 'hotfix/fix_date_newsagency_list'
 * Merge branch 'hotfix/fix_gif_upload'
 * Merge branch 'hotfix/fix_animated_gif_image_bug'
 * Merge branch 'hotfix/fix_upload_gif_images'
 * Merge branch 'hotfix/fix_blog_author_pagination'
