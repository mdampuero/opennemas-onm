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
