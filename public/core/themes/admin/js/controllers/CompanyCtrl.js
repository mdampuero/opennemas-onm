(function() {
  'use strict';

  angular.module('BackendApp.controllers')

    /**
     * @ngdoc controller
     * @name  CompanyCtrl
     *
     * @requires $controller
     * @requires $scope
     * @requires $timeout
     * @requires $uibModal
     * @requires $window
     * @requires cleaner
     * @requires http
     * @requires linker
     * @requires localizer
     * @requires messenger
     * @requires webStorage
     *
     * @description
     *   Provides actions to edit, save and update companies.
     */
    .controller('CompanyCtrl', [
      '$controller', '$scope', '$timeout', '$uibModal', '$window', 'cleaner',
      'http', 'related', 'routing', 'translator',
      function($controller, $scope, $timeout, $uibModal, $window, cleaner,
          http, related, routing, translator) {
        // Initialize the super class and extend it.
        $.extend(this, $controller('ContentRestInnerCtrl', { $scope: $scope }));

        /**
         * @inheritdoc
         */
        $scope.draftEnabled = true;

        /**
         * @inheritdoc
         */
        $scope.draftKey = 'company-draft';

        /**
         * @inheritdoc
         */
        $scope.dtm = null;

        /**
         * @memberOf CompanyCtrl
         *
         * @description
         *  The default schedule for the day.
         *
         * @type {Object}
         */
        $scope.defaultSchedule = {
          start: new Date('1970-01-01T00:00:00'),
          end:   new Date('1970-01-01T23:59:00')
        };

        /**
         * @memberOf CompanyCtrl
         *
         * @description
         *  The company object.
         *
         * @type {Object}
         */
        $scope.item = {
          body: '',
          content_type_name: 'company',
          fk_content_type: 19,
          content_status: 0,
          description: '',
          created: null,
          starttime: null,
          endtime: null,
          title: '',
          type: 0,
          related_contents: [],
          tags: [],
        };

        /**
         * @memberOf CompanyCtrl
         *
         * @description
         *  The related service.
         *
         * @type {Object}
         */
        $scope.related = related;

        /**
         * @memberOf CompanyCtrl
         *
         * @description
         *  The list of routes for the controller.
         *
         * @type {Object}
         */
        $scope.routes = {
          createItem:  'api_v1_backend_company_create_item',
          getItem:     'api_v1_backend_company_get_item',
          getPreview:  'api_v1_backend_company_get_preview',
          list:        'backend_companies_list',
          public:      'frontend_company_show',
          redirect:    'backend_company_show',
          saveItem:    'api_v1_backend_company_save_item',
          savePreview: 'api_v1_backend_company_save_preview',
          updateItem:  'api_v1_backend_company_update_item'
        };

        /**
         * @memberOf CompanyCtrl
         *
         * @description
         *  Adds an schedule to one day.
         *
         * @type {Object}
         */
        $scope.addSchedule = function(day) {
          $scope.item.timetable[day].schedules.push(Object.assign({}, $scope.defaultSchedule));
        };

        /**
         * @memberOf CompanyCtrl
         *
         * @description
         *  Removes an schedule to in one day.
         *
         * @type {Object}
         */
        $scope.removeSchedule = function(day, index) {
          $scope.item.timetable[day].schedules.splice(index, 1);
        };

        /**
         * @memberOf CompanyCtrl
         *
         * @description
         *  Gets the sector title based on a name.
         *
         * @type {Object}
         */
        $scope.getSectorTitle = function(name) {
          if (!$scope.data || !name) {
            return '';
          }

          return $scope.data.extra.sectors.filter(function(sector) {
            return name === sector.name;
          }).shift().title;
        };

        /**
         * @memberOf CompanyCtrl
         *
         * @description
         *  Format the dates in the timetable to work with input of type time.
         */
        $scope.formatDates = function() {
          $scope.item.timetable.forEach(function(schedule) {
            schedule.schedules.forEach(function(time) {
              time.start = new Date(time.start);
              time.end   = new Date(time.end);
            });
          });
        };

        /**
         * @inheritdoc
         */
        $scope.buildScope = function() {
          $scope.localize($scope.data.item, 'item', true, [ 'related_contents' ]);
          $scope.expandFields();
          // Check if item is new (created) or existing for use default value or not
          if ($scope.draftKey !== null && $scope.data.item.pk_content) {
            $scope.draftKey = 'company-' + $scope.data.item.pk_content + '-draft';
          }

          $scope.item.timetable = $scope.item.timetable ?
            $scope.item.timetable :
            $scope.data.extra.timetable.slice();

          $scope.checkDraft();
          related.init($scope);
          related.watch();
          translator.init($scope);
        };

        /**
         * @function empty
         * @memberOf CompanyCtrl
         *
         * @description
         *   Shows a modal window to confirm if album has to be emptied.
         */
        $scope.empty = function() {
          $uibModal.open({
            templateUrl: 'modal-delete',
            backdrop: 'static',
            controller: 'ModalCtrl',
            resolve: {
              template: function() {
                return { selected: $scope.photos.length };
              },
              success: function() {
                return function() {
                  return $timeout(function() {
                    $scope.photos      = [];
                    $scope.data.photos = [];

                    // Fake response for ModalCtrl
                    return { response: {}, headers: [], status: 200 };
                  });
                };
              }
            }
          });
        };

        /**
         * @function getFrontendUrl
         * @memberOf CompanyCtrl
         *
         * @description
         *   Generates the public URL basing on the item.
         *
         * @param {String} item  The item to generate route for.
         *
         * @return {String} The URL for the content.
         */
        $scope.getFrontendUrl = function(item) {
          return $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content,
              created: $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
            })
          );
        };

        /**
         * Opens a modal with the preview of the company.
         */
        $scope.preview = function() {
          $scope.flags.http.generating_preview = true;

          // Force ckeditor
          CKEDITOR.instances.body.updateElement();
          CKEDITOR.instances.description.updateElement();

          var status = { starttime: null, endtime: null, content_status: 1 };
          var item   = Object.assign({}, $scope.data.item, status);

          if (item.tags) {
            item.tags = item.tags.filter(function(tag) {
              return Number.isInteger(tag);
            });
          }

          var data = {
            item: JSON.stringify(cleaner.clean(item)),
            locale: $scope.config.locale.selected
          };

          http.put($scope.routes.savePreview, data).then(function() {
            $uibModal.open({
              templateUrl: 'modal-preview',
              windowClass: 'modal-fullscreen',
              controller: 'ModalCtrl',
              resolve: {
                template: function() {
                  return {
                    src: routing.generate($scope.routes.getPreview)
                  };
                },
                success: function() {
                  return null;
                }
              }
            });

            $scope.flags.http.generating_preview = false;
          }, function() {
            $scope.flags.http.generating_preview = false;
          });
        };

        /**
         * @inheritdoc
         */
        $scope.validate = function() {
          if ($scope.form && $scope.form.$invalid) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          if (!$('[name=form]')[0].checkValidity()) {
            $('[name=form]')[0].reportValidity();
            return false;
          }

          return true;
        };
      }
    ]);
})();
