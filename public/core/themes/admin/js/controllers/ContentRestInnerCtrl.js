/**
 * Handle actions for article inner.
 */
angular.module('BackendApp.controllers').controller('ContentRestInnerCtrl', [
  '$controller', '$uibModal', '$scope', 'cleaner', 'http',
  'messenger', 'routing', '$timeout', 'webStorage', '$window', 'translator',
  function($controller, $uibModal, $scope, cleaner, http,
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
     *   Saves tags, send notifications if  needed and, then, saves the item.
     */
    $scope.submit = function(item) {
      if (item && $scope.hasPendingNotifications() && !$scope.hasAutomaticNotifications()) {
        if (item.starttime <= $window.moment().format('YYYY-MM-DD HH:mm:ss')) {
          $scope.openNotificationModal(item, false);
        } else {
          $scope.sendWPNotification(item, false);
        }
      } else if ($scope.hasAutomaticNotifications()) {
        $scope.sendWPNotification(item, true);
      } else {
        $scope.saveItem();
      }
    };

    /**
     * @function saveItem
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Saves tags, generate slug and, then, saves the item.
     */
    $scope.saveItem = function() {
      if (!$scope.validate()) {
        messenger.post(window.strings.forms.not_valid, 'error');
        return;
      }

      $scope.draftEnabled      = false;
      $scope.flags.http.saving = true;

      if ($scope.form.tags) {
        $scope.$broadcast('onmTagsInput.save', {
          onError: $scope.errorCb,
          onSuccess: function(ids) {
            $scope.item.tags      = ids;
            $scope.data.item.tags = ids;

            if ($scope.item.slug && $scope.form.slug && $scope.form.slug.$dirty) {
              // Force slug to be valid
              $scope.getSlug($scope.data.item.slug, function(response) {
                $scope.data.item.slug           = response.data.slug;

                $scope.flags.generate.slug = false;
                $scope.flags.block.slug    = true;

                $scope.save();
              });
            } else {
              $scope.save();
            }
          }
        });
      } else if ($scope.data.item.slug && $scope.form.slug && $scope.form.slug.$dirty) {
        $scope.getSlug($scope.data.item.slug, function(response) {
          $scope.data.item.slug      = response.data.slug;
          $scope.flags.generate.slug = false;
          $scope.flags.block.slug    = true;

          $scope.save();
        });
      } else {
        $scope.save();
      }
    };

    /**
     * @function openNotificationModal
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Send webpush notification to all subscribers
     */
    $scope.openNotificationModal = function(item, createNotification) {
      var status = 2;

      if (createNotification) {
        status = 1;
      }

      var modal = $uibModal.open({
        templateUrl: 'modal-webpush',
        backdrop: 'static',
        controller: 'ModalCtrl',
        resolve: {
          template: function() {
            return { status: status };
          },
          success: function() {
            return null;
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          $scope.sendWPNotification(item, createNotification);
        }
      });
    };

    /**
     * @function sendPressClipping
     * @memberof ContentRestInnerCtrl
     *
     * @description
     *  Send a PressClipping item for CEDRO
     */
    $scope.sendPressClipping = function(item) {
      // Ensure pressclipping is defined as an array and hasn't been corrupted
      if (!Array.isArray($scope.data.item.pressclipping)) {
        $scope.data.item.pressclipping = [];
      }

      // Determine the appropriate publication date
      var date = $scope.item.starttime < $window.moment().format('YYYY-MM-DD HH:mm:ss') ?
        $window.moment().format('YYYY-MM-DD HH:mm:ss') :
        $scope.item.starttime;

      var featured = $scope.getFeaturedMedia($scope.item, 'featured_frontpage');

      // Add the new press clipping to the array
      $scope.data.item.pressclipping.push({
        title: item.title,
        subtitle: item.description,
        author: item.fk_author,
        pubDate: date,
        body: item.body,
        category: item.categories,
        image: $scope.data.extra.base_url + '/' + featured.path,
        articleID: item.pk_content,
        articleURL: $scope.getFrontendUrl(item)
      });

      // Define the API route
      var route = {
        name: 'api_v1_backend_pressclipping_upload_data',
      };

      // Send the data to the API
      var data = $scope.data.item.pressclipping;
      var date = new Date();

      http.post(route, data).then(
        function() {
          $scope.data.item.pressclipping_sended = $window.moment.utc($window.moment(date)).format('YYYY-MM-DD HH:mm:ss');
          $scope.data.item.pressclipping_status = 'Sended';
          $scope.save();
        },
        function() {
          $scope.data.item.pressclipping_sended = $window.moment.utc($window.moment(date)).format('YYYY-MM-DD HH:mm:ss');
          $scope.save();
        }
      );

      delete $scope.data.item.pressclipping;
    };

    /**
     * @function removePressClipping
     * @memberof ContentRestInnerCtrl
     *
     * @description
     *  Remove a PressClipping item for CEDRO
     */
    $scope.removePressClipping = function(articleID) {
      // Ensure pressclipping is defined as an array and hasn't been corrupted
      if (!Array.isArray($scope.data.item.pressclipping)) {
        $scope.data.item.pressclipping = [];
      }

      // Add the new press clipping to the array
      $scope.data.item.pressclipping.push({
        articleID: articleID,
      });

      // Define the API route
      var route = {
        name: 'api_v1_backend_pressclipping_remove_data',
      };

      // Send the data to the API
      var data = $scope.data.item.pressclipping;

      http.post(route, data).then(
        function() {
          $scope.data.item.pressclipping_sended = null;
          $scope.data.item.pressclipping_status = null;
          $scope.save();
        },
        function() {
          $scope.statusPressclipping = false;
        }
      );

      delete $scope.data.item.pressclipping;
    };

    /**
     * @function sendWPNotification
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Send webpush notification to all subscribers
     */
    $scope.sendWPNotification = function(item) {
      if (!$scope.validate()) {
        messenger.post(window.strings.forms.not_valid, 'error');
        return;
      }
      if (!item) {
        return;
      }

      if ($scope.hasPendingNotifications()) {
        $scope.removePendingNotification(false);
      }

      // When publishing created content, if it does not have the start time date settled, it will give it one
      if ($scope.itemHasId()) {
        if ($scope.item.content_status === 1 && !$scope.item.starttime) {
          $scope.item.starttime = $window.moment().format('YYYY-MM-DD HH:mm:ss');
        }
      }

      var date = $scope.item.starttime < $window.moment().format('YYYY-MM-DD HH:mm:ss') ? $window.moment().format('YYYY-MM-DD HH:mm:ss') : $scope.item.starttime;

      $scope.data.item.webpush_notifications.push(
        {
          status: 0,
          body: null,
          title: $scope.item.title,
          send_date: $window.moment.utc($window.moment(date)).format('YYYY-MM-DD HH:mm:ss'),
          image: null,
          transaction_id: null,
          impressions: 0,
          clicks: 0,
          closed: 0
        }
      );

      if (!$scope.item.content_status) {
        $scope.removePendingNotification(false);
      }

      $scope.saveItem();
    };

    /**
     * @function hasPendingNotifications
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *  Check if items has pending notifications
     */

    $scope.hasPendingNotifications = function() {
      for (var i = 0; i < $scope.item.webpush_notifications.length; i++) {
        if ($scope.item.webpush_notifications[i].status === 0) {
          return true;
        }
      }
      return false;
    };

    /**
     * @function removePendingNotification
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *  Generates the backend url of the featured media associated to the article.
     */
    $scope.removePendingNotification = function(saveItem) {
      var notifications = $scope.data.item.webpush_notifications;

      for (var i = 0; i < notifications.length; i++) {
        if (notifications[i].status === 0) {
          notifications.splice(i, 1);
        }
      }

      $scope.data.item.webpush_notifications = notifications;
      if (saveItem) {
        $scope.saveItem();
      }
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
     * @function hasAutomaticNotifications
     * @memberOf ContentRestInnerCtrl
     *
     * @description
     *   Check if auto webpush setting is enabled.
     *
     * @return {Boolean} True if enabled. False
     *                   otherwise.
     */
    $scope.hasAutomaticNotifications = function() {
      if ($scope.data &&
        $scope.data.extra.auto_webpush &&
        $scope.data.extra.auto_webpush === '1') {
        return true;
      }

      return false;
    };

    $scope.onmIAModal = function(field, AIFieldType, AIFieldTitle) {
      const input  = field in $scope ? $scope[field] : $scope.item[field];
      const locale = $scope.config.locale.selected ? $scope.config.locale.available[$scope.config.locale.selected] : 'Español (España)';

      $uibModal.open({
        templateUrl: 'modal-onmai',
        backdrop: 'static',
        windowClass: 'modal-onmai',
        controller: 'OnmAIModalCtrl',
        resolve: {
          template: function() {
            return {
              lastTemplate: $scope.lastTemplate,
              step: 1,
              AIFieldType: AIFieldType,
              AIFieldTitle: AIFieldTitle || '',
              input: input,
              locale: locale
            };
          },
          success: function() {
            return function(modal, template) {
              $scope.lastTemplate = template;
              if (field in $scope) {
                $scope[field] = template.response;
              } else {
                $scope.item[field] = template.response;
              }
              $timeout(function() {
                $scope.flags.generate.slug = true;
              }, 250);
            };
          }
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

    /**
     * @function getTextComplexity
     * @memberOf ContentRestInnerCtrl
     *
     * @param {any} String or Object to localize.
     *
     * @return {String} Localized text.
     *
     * @description
     *   Get text and return its complexity by Fernández Huerta formula
     */
    $scope.getTextComplexity = function(text) {
      if (typeof text !== 'string') {
        text = '';
      }
      // Regex to replace url in sentences
      var regex = /(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#/%=~_|$?!:,.]*\)|[-A-Z0-9+&@#/%=~_|$?!:,.])*(?:\([-A-Z0-9+&@#/%=~_|$?!:,.]*\)|[A-Z0-9+&@#/%=~_|$])/igm;

      // Replace url in sentences
      var text = text.replace(regex, 'enlace');

      // Remove HTML tags and add one space after every ".", "!" or "?".
      var text = text.replace(/(<([^>]+)>)/ig, ' ').replace(/&nbsp;/ig, ' ').replace(/\.+|!+|\?+/g, '. ');

      // Split the text into sentences and remove any empty sentences that might result from the split.
      var sentences = text.split(/[.!?\n]/)
        .filter(function(sentence) {
          return sentence.trim().length > 0;
        });

      // Replace several spaces for only one space
      text = text.replace(/\s\s+/g, ' ');

      // Calculate number of syllables (by using silabajs library)
      var syllables = silabaJS.getSilabas(text).numeroSilaba;

      // Split the text into words.
      var words = text.split(/\s+/);

      // Remove any empty words that might result from the split.
      words = words
        .filter(function(word) {
          return word.trim().length > 0 && word.trim()[0].match(/[a-zA-Z#0-9(]/);
        });

      // Calculate the averages.
      var avgSyllablesPerWord = syllables / words.length;
      var avgWordsPerSentence = words.length / sentences.length;

      // Apply the Fernández-Huerta formula to get text complexity (rounded)
      var textComplexity =  Math.round(206.84 - 1.02 * avgWordsPerSentence - 60 * avgSyllablesPerWord);

      textComplexity = Math.min(Math.max(textComplexity, 1), 100);

      return {
        textComplexity: textComplexity,
        wordsCount: words.length
      };
    };
  }
]);
