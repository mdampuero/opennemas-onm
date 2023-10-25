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
          tags: []
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
         * @function list
         * @memberOf CompanyCtrl
         *
         * @description
         *   Returns a sorted elements based on a query and name property.
         *
         * @param {String} query The query string.
         * @param {Object} model the model
         *
         * @return {Object} The sorted model
         */
        $scope.list = function(query, model) {
          var finalModel = model.sort(function(a, b) {
            return $scope.orderFunction(query, b.name) - $scope.orderFunction(query, a.name);
          });

          var finalData = [];

          finalModel.forEach(function(item) {
            finalData.push(item.name);
          });
          return finalData;
        };

        $scope.checkTag = function(tag) {
          $scope.getSlug(tag.name, function(response) {
            tag.value = response.data.slug;
          });
          return tag;
        };

        $scope.orderFunction = function(query, compareString) {
          var queryChars = query.split('');
          var stringChars = compareString.split('');

          for (var iterator = 0; iterator < queryChars.length; iterator++) {
            if (!stringChars[iterator] || stringChars[iterator].toLowerCase() !== queryChars[iterator].toLowerCase()) {
              break;
            }
          }
          return iterator;
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
          var dontLocalize = [ 'related_contents' ];

          if ($scope.extraFields && $scope.hasMultilanguage()) {
            $scope.extraFields.forEach(function(element) {
              if ($scope.item[element.key.value] && !(element.key.value in dontLocalize)) {
                dontLocalize.push(element.key.value);
              }
            });
          }
          $scope.localize($scope.data.item, 'item', true, dontLocalize);
          $scope.expandFields();

          if (!$scope.data.item.pk_content) {
            $scope.item.with_comment = $scope.data.extra.comments_enabled ? 1 : 0;
          }

          // Check if item is new (created) or existing for use default value or not
          if ($scope.draftKey !== null && $scope.data.item.pk_content) {
            $scope.draftKey = 'company-' + $scope.data.item.pk_content + '-draft';
          }
          $scope.checkDraft();
          $scope.draftEnabled = false;
          if ($scope.extraFields && typeof $scope.extraFields === 'object') {
            $scope.extraFields.forEach(function(element) {
              if ($scope.item[element.key.value] && typeof $scope.item[element.key.value] === 'string') {
                $scope.item[element.key.value] = JSON.parse($scope.item[element.key.value]);
              }
            });
          }
          $scope.draftEnabled = true;

          $scope.item.timetable = $scope.item.timetable ?
            $scope.item.timetable :
            $scope.data.extra.timetable.slice();

          related.init($scope);
          related.watch();
          translator.init($scope);
        };

        /**
         * @function configure
         * @memberOf CompanyCtrl
         *
         * @description
         *   Configures the extra data for the current section.
         *
         * @param {Object} data The data to configure the section.
         */
        $scope.configure = function(data) {
          $scope.draftEnabled = false;
          if (!data) {
            return;
          }
          if (data.extraFields) {
            $scope.extraFields = data.extraFields;
          }
          if (data.localities) {
            $scope.localities = data.localities;
            if (typeof $scope.localities === 'string') {
              $scope.localities = JSON.parse($scope.localities);
            }
          }
          if (data.provinces) {
            $scope.provinces = data.provinces;
            if (typeof $scope.provinces === 'string') {
              $scope.provinces = JSON.parse($scope.provinces);
            }
          }
          if (data.locale) {
            $scope.config.locale = data.locale;
          }

          if ($scope.forcedLocale && Object.keys(data.locale.available)
            .indexOf($scope.forcedLocale) !== -1) {
            // Force localization
            $timeout(function() {
              $scope.config.locale.selected = $scope.forcedLocale;
            });
          }
          $scope.draftEnabled = true;
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
         * @inheritdoc
         */
        $scope.parseData = function(data) {
          if ($scope.extraFields) {
            $scope.extraFields.forEach(function(element) {
              if (data[element.key.value] && typeof data[element.key.value] !== 'string') {
                data[element.key.value] = JSON.stringify($scope.item[element.key.value]);
              }
            });
          }
          if (data.province && data.province.nm && typeof data.province !== 'string') {
            data.province = data.province.nm;
          }
          if (data.locality && data.locality.nm && typeof data.locality !== 'string') {
            data.locality = data.locality.nm;
          }
          return data;
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
          if (!item.pk_content) {
            return '';
          }

          return $scope.data.extra.base_url + $scope.getL10nUrl(
            routing.generate($scope.routes.public, {
              id: item.pk_content.toString().padStart(6, '0'),
              created: item.urldatetime || $window.moment(item.created).format('YYYYMMDDHHmmss'),
              slug: item.slug,
            })
          );
        };

        /**
         * @function filterLocality
         * @memberOf CompanyCtrl
         *
         * @description
         *   Filter localities array by province id
         *
         * @param {String} id  The province id.
         *
         * @return {Array} The array of localities.
         */
        $scope.filterLocality = function(id) {
          var result = $scope.localities.filter(function(element) {
            return element.id.startsWith(id);
          });

          return result;
        };

        /**
         * @function findProvince
         * @memberOf CompanyCtrl
         *
         * @description
         *   Find province object by its name
         *
         * @param {String} name  The province name.
         *
         * @return {Object} The province object.
         */
        $scope.findProvince = function(name) {
          var result = $scope.provinces.filter(function(element) {
            return element.nm === name;
          });

          return result.pop();
        };

        /**
         * @function findLocality
         * @memberOf CompanyCtrl
         *
         * @description
         *   Find province object by its name
         *
         * @param {String} id    The province id.
         * @param {String} name  The locality name.
         *
         * @return {Object} The province object.
         */
        $scope.findLocality = function(id, name) {
          var result = $scope.localities.filter(function(element) {
            return element.nm === name && element.id.startsWith(id);
          });

          return result.pop();
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
          if (item.locality && typeof item.locality !== 'string') {
            item.locality = item.locality.nm;
          }
          if (item.province && typeof item.province !== 'string') {
            item.province = item.province.nm;
          }
          if ($scope.extraFields) {
            $scope.extraFields.forEach(function(element) {
              if (item[element.key.value] && typeof item[element.key.value] === 'object') {
                item[element.key.value] = JSON.stringify(item[element.key.value]);
              }
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

        $scope.$watch('item.province', function(nv, ov) {
          if (!nv) {
            return;
          }
          if (ov && !nv) {
            $scope.item.locality = '';
            delete $scope.filteredLocalities;
          }
          if (nv.id && nv.nm) {
            $scope.filteredLocalities = $scope.filterLocality(nv.id);
          }
          if (typeof nv === 'string') {
            $scope.item.province = $scope.findProvince(nv);
            if (typeof $scope.item.locality === 'string') {
              $scope.item.locality = $scope.findLocality($scope.item.province.id, $scope.item.locality);
            }
          }
        });
      }
    ]);
})();
