/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('ContentRestInnerCtrl', [
  '$controller', '$uibModal', '$scope', 'cleaner',
  'messenger', 'routing', '$timeout', 'webStorage', '$window', 'translator',
  function($controller, $uibModal, $scope, cleaner,
      messenger, routing, $timeout, webStorage, $window, translator) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('RestInnerCtrl', { $scope: $scope }));

    /**
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *  Flag to enabled or disable drafts
     *
     * @type {Boolean}
     */
    $scope.draftEnabled = false;

    /**
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *  The draft key.
     *
     * @type {String}
     */
    $scope.draftKey = null;

    /**
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *  The timeout function for draft.
     *
     * @type {Function}
     */
    $scope.dtm = null;

    /**
     * @inheritdoc
     */
    $scope.incomplete = true;

    /**
     * @function checkDraft
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Checks if there is a draft from the previous content.
     */
    $scope.checkDraft = function() {
      if (!webStorage.session.has($scope.draftKey)) {
        return;
      }

      $uibModal.open({
        backdrop:    true,
        backdropClass: 'modal-backdrop-transparent',
        controller:  'YesNoModalCtrl',
        openedClass: 'modal-relative-open',
        templateUrl: 'modal-draft',
        windowClass: 'modal-right modal-small modal-top',
        resolve: {
          template: function() {
            return {};
          },
          yes: function() {
            return function(modalWindow) {
              $scope.data.item = webStorage.session.get($scope.draftKey).item;

              if ($scope.config.linkers.item) {
                $scope.config.linkers.item.link(
                  $scope.data.item, $scope.item);
                $scope.config.linkers.item.update();
              } else {
                $scope.item = $scope.data.item;
              }

              if ($scope.related) {
                $scope.data.extra.related_contents = webStorage.session.get($scope.draftKey).related;
                $scope.related.init($scope);
              }

              modalWindow.close({ response: true, success: true });
            };
          },
          no: function() {
            return function(modalWindow) {
              webStorage.session.remove($scope.draftKey);
              modalWindow.close({ response: false, success: true });
            };
          }
        }
      });
    };

    /**
     * @inheritdoc
     */
    $scope.getData = function() {
      var data = angular.extend({}, $scope.data.item);

      if ($scope.item.params && Object.keys($scope.item.params).length > 0) {
        data.params = angular.extend(data.params, $scope.item.params);
      }

      return cleaner.clean(data);
    };

    /**
     * @function getItemId
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Returns the item id.
     *
     * @return {Integer} The item id.
     */
    $scope.getItemId = function() {
      return $scope.item.pk_content;
    };

    /**
     * @function parseCopyData
     * @memberOf RestInnerCtrl
     *
     * @description
     *   description
     *
     * @param {Object} data The data to parse.
     *
     * @return {Object} Parses data before copy.
     */
    $scope.parseCopyData = function(data) {
      data = $scope.unsetItemId(data);
      delete data.urn_source;
      delete data.starttime;
      delete data.endtime;
      delete data.slug;
      data.content_status = 0;
      if (data.title) {
        data.title = 'Copy of ' + data.title;
      }

      return data;
    };

    /**
     * @function unsetItemId
     * @memberOf RestInnerCtrl
     *
     * @description
     *   Unsets the item id.
     *
     * @return {Integer} The item id.
     */
    $scope.unsetItemId = function(data) {
      delete data.pk_content;
      return data;
    };

    /**
     * @inheritdoc
     */
    $scope.hasMultilanguage = function() {
      return $scope.config && $scope.config.locale &&
        $scope.config.locale.multilanguage;
    };

    /**
     * @function extractStrings
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *  Returns all the strings of the content that can be translated.
     *
     * @param {Object} item The item to get strings from.
     *
     * @return {array} The array of strings that can be translated.
     */
    $scope.extractStrings = function(scope) {
      return scope.data.extra.keys.map(function(key) {
        return scope.data.item[key];
      });
    };

    /**
     * @function loadStrings
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *  Loads all the translated strings to the item.
     *
     * @param {array}  strings The array translated strings.
     * @param {Object} item    The item to load strings on.
     */
    $scope.loadStrings = function(strings, scope, locale) {
      scope.data.extra.keys.forEach(function(key, index) {
        if (typeof scope.data.item[key] === 'object') {
          scope.data.item[key][locale] = strings[index];
        }
      });
    };

    /**
     * @function submit
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Saves tags and, then, saves the item.
     */
    $scope.submit = function() {
      if (!$scope.validate()) {
        messenger.post(window.strings.forms.not_valid, 'error');
        return;
      }

      $scope.flags.http.saving = true;

      $scope.$broadcast('onmTagsInput.save', {
        onError: $scope.errorCb,
        onSuccess: function(ids) {
          $scope.item.tags      = ids;
          $scope.data.item.tags = ids;

          $scope.draftEnabled = false;

          $scope.save();
        }
      });
    };

    /**
     * @function duplicate
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Saves tags and, then, duplicates the item.
     */
    $scope.duplicate = function() {
      if (!$scope.validate()) {
        messenger.post(window.strings.forms.not_valid, 'error');
        return;
      }

      $scope.flags.http.saving = true;

      $scope.$broadcast('onmTagsInput.save', {
        onError: $scope.errorCb,
        onSuccess: function(ids) {
          $scope.item.tags      = ids;
          $scope.data.item.tags = ids;

          $scope.draftEnabled = false;

          $scope.copy();
        }
      });
    };

    /**
     * @function validate
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Validates the form and/or the current item in the scope.
     *
     * @return {Boolean} True if the form and/or the item are valid. False
     *                   otherwise.
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

    /**
     * @function getFeaturedMediaUrl
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *  Generates the backend url of the featured media associated to the article.
     *
     * @param {Object} item The featured media object.
     *
     * @return {String} The url for the featured media object.
     */
    $scope.getFeaturedMediaUrl = function(item) {
      var routes = {
        photo: 'backend_photo_show',
        video: 'backend_video_show',
        album: 'backend_album_show'
      };

      return routing.generate(routes[item.content_type_name], { id: item.pk_content });
    };

    // Generates slug when flag changes
    $scope.$watch('flags.generate.slug', function(nv) {
      if ($scope.item.slug || !nv || !$scope.item.title) {
        $scope.flags.generate.slug = false;

        return;
      }

      if ($scope.tm) {
        $timeout.cancel($scope.tm);
      }

      $scope.tm = $timeout(function() {
        $scope.getSlug($scope.item.title, function(response) {
          $scope.item.slug           = response.data.slug;
          $scope.flags.generate.slug = false;
          $scope.flags.block.slug    = true;

          $scope.form.slug.$setDirty(true);
        });
      }, 250);
    }, true);

    // Define watcher to execute translation when locale changes
    $scope.$watch('config.locale.selected', function(nv, ov) {
      if (!nv) {
        return;
      }

      if (nv === ov) {
        return;
      }

      if (translator.isTranslatable(ov, nv)) {
        // Raise a modal to indicate that background translation is being executed
        $uibModal.open({
          backdrop: 'static',
          keyboard: false,
          backdropClass: 'modal-backdrop-dark',
          controller:  'TranslatorCtrl',
          openedClass: 'modal-relative-open',
          templateUrl: 'modal-translate',
          resolve: {
            template: function() {
              return {
                config: translator.config,
                translating: false,
                selectedTranslator: translator.getTranslatorItem(ov, nv),
              };
            },
            callback: function() {
              return function(template) {
                template.confirm = true;
              };
            }
          }
        });
      }
    }, true);

    // Defines a watcher after 5 seconds
    $timeout(function() {
      // Saves a draft 2.5s after the last change
      $scope.$watch(function() {
        var item  = angular.copy($scope.data.item);

        // Removes the uploaded file if exists from the properties watched to avoid errors.
        for (var prop in item) {
          if (item.hasOwnProperty(prop) && item[prop] instanceof File) {
            item[prop] = null;
          }
        }

        return item;
      }, function(nv, ov) {
        if (!$scope.draftEnabled) {
          return;
        }

        if (!nv || ov === nv) {
          return;
        }

        // Show a message when leaving before saving
        $($window).bind('beforeunload', function() {
          if ($scope.form.$dirty) {
            return $window.leaveMessage;
          }
        });

        $scope.form.$setDirty(true);

        if ($scope.draftKey !== null) {
          $scope.draftSaved = null;

          if ($scope.dtm) {
            $timeout.cancel($scope.dtm);
          }

          $scope.dtm = $timeout(function() {
            webStorage.session.set($scope.draftKey, {
              item: nv,
              related: $scope.related ? $scope.related.exportRelated() : []
            });

            $scope.draftSaved = $window.moment().format('HH:mm');
          }, 2500);
        }
      }, true);
    }, 5000);

    /**
     * @function localizeText
     * @memberOf ContentRestInnerCtrl
     *
     * @param {any} String or Object to localize.
     *
     * @return {String} Localized text.
     *
     * @description
     *   Localize and return text
     */
    $scope.localizeText = function(text) {
      if (typeof text === 'object') {
        return text[$scope.config.locale.selected];
      }

      return text;
    };
  }
]);
