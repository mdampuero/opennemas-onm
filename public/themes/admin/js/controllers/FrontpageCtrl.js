/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('FrontpageCtrl', [
  '$controller', 'http', '$uibModal', '$scope', '$interval', 'routing', 'messenger',
  function($controller, http, $uibModal, $scope, $interval, routing, messenger) {
    'use strict';

    // Initialize the super class and extend it.
    $.extend(this, $controller('InnerCtrl', { $scope: $scope }));

    /**
     * The list of selected elements.
     *
     * @type array
     */
    $scope.selected = {
      contents: []
    };

    $scope.frontpageInfo = {
      lastSaved:       null,
      publish_date:    window.moment.tz(new Date(), 'UTC'),
      checkNewVersion: true
    };

    $scope.init = function(frontpages, versions, categoryId, versionId, time, frontpageLastSaved) {
      $scope.categoryId              = parseInt(categoryId);
      $scope.frontpages              = frontpages;
      $scope.frontpage               = frontpages[$scope.categoryId];
      $scope.frontpageInfo.lastSaved = frontpageLastSaved;
      $scope.time                    = time;
      $scope.time.diff               = new Date().getTime() - time.timestamp;
      $scope.scheduledFFuture        = [];

      if (versions.length === 0) {
        $scope.version = {
          id:           0,
          frontpage_id: null,
          name:         window.moment.tz($scope.time.timestamp, 'UTC')
            .tz($scope.time.timezone).format('YYYY-MM-DD HH:mm:ss'),
          type:         'manual',
          created:      null,
          publish_date: window.moment.tz($scope.time.timestamp, 'UTC'),
          category_id:  $scope.categoryId,

        };
        $scope.versions  = [ $scope.version ];
        $scope.versionId = 0;
      } else {
        $scope.versions = versions;
        $scope.getReloadVersionStatus();
        $scope.version   = $scope.getCurrentVersion(versionId);
        $scope.versionId = $scope.version.id;
      }
      $scope.frontpageInfo.publish_date = $scope.version.publish_date === null ?
        '' :
        window.moment.tz($scope.version.publish_date, 'UTC')
          .tz($scope.time.timezone).format('YYYY-MM-DD HH:mm:ss');
      $interval($scope.getReloadVersionStatus, 60000);
      $interval($scope.checkAvailableNewVersion, 10000);
    };

    $scope.getReloadVersionStatus = function() {
      var currentServerTime = new Date().getTime() - $scope.time.diff;
      var currentVer        = { versionId: null, diff: null };
      var diffCurrToVer     = null;
      var scheduledFFuture  = [];

      $scope.versions.forEach(function(version) {
        if (version.publish_date !== null && version.id !== 0) {
          diffCurrToVer =
            window.moment.tz(version.publish_date, 'UTC').toDate().getTime() -
            currentServerTime;
          if (diffCurrToVer <= 0 &&
            (currentVer.versionId === null || diffCurrToVer > currentVer.diff)
          ) {
            currentVer = { versionId: version.id, diff: diffCurrToVer };
          }
          if (diffCurrToVer > 0) {
            scheduledFFuture.push(version.id);
          }
        }
      });

      $scope.publishVersionId = currentVer.versionId;
      $scope.scheduledFFuture = scheduledFFuture;
    };

    $scope.deselectAll = function() {
      $scope.selected.contents = [];
    };

    /**
     * Removes the selected contents from this frontpage
     */
    $scope.removeSelectedContents = function() {
      var modal = $uibModal.open({
        templateUrl: 'modal-drop-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return true;
          }
        }
      });

      modal.result.then(function(response) {
        if (response) {
          var selected =
            $('.content-provider-element input[type="checkbox"]:checked')
              .closest('.content-provider-element');

          selected.each(function() {
            $(this).fadeTo('slow', 0.01, function() {
              $(this).slideUp('slow', function() {
                $(this).remove();
              });
            });
          });

          $scope.showMessage(frontpage_messages.remember_save_positions, 'info', 5);

          $scope.selected.contents = [];
        }
      });
    };

    /**
     * Archives the selected contents from all the frontpages
     */
    $scope.archiveSelectedContents = function() {
      var modal = $uibModal.open({
        templateUrl: 'modal-archive-selected',
        backdrop: 'static',
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              selected: $scope.selected
            };
          },
          success: function() {
            return function() {
              var url = frontpage_urls.set_arquived;

              return http.get(url, { params: { 'ids[]': $scope.selected.contents } })
                .success(function(response) {
                  $scope.showMessage(response, 'success', 5);
                }).error(function(response) {
                  $scope.showMessage(response.responseText, 'error', 5);
                });
            };
          }
        }
      });

      modal.result.then(function(response) {
        if (response.success) {
          var selected =
            $('.content-provider-element input[type="checkbox"]:checked')
              .closest('.content-provider-element');

          selected.each(function() {
            $(this).fadeTo('slow', 0.01, function() {
              $(this).slideUp('slow', function() {
                $(this).remove();
              });
            });
          });

          $scope.selected.contents = [];
        }
      });
    };

    $scope.showMessage = function(message, type, time) {
      messenger.options = {
        extraClasses: 'messenger-fixed messenger-on-bottom',
      };

      messenger().post({
        message: message,
        type: type,
        hideAfter: time,
        showCloseButton: true,
        id: new Date().getTime()
      });
    };

    $scope.changeCategory = function(id) {
      window.location = routing.generate('admin_frontpage_list',
        { category: id });
    };

    $scope.changeVersion = function(versionId) {
      window.location = routing.generate('admin_frontpage_list',
        {
          category: $scope.categoryId,
          version: versionId
        });
    };

    $scope.preview = function(category) {
      $scope.loading = true;

      var contents = $scope.getContentsInFrontpage();
      var encoded  = JSON.stringify(contents);
      var data     = {
        contents: encoded,
        category_name: category
      };

      var url = routing.generate('admin_frontpage_preview',
        { category: category });

      http.post(url, data).success(function() {
        $uibModal.open({
          templateUrl: 'modal-preview',
          windowClass: 'modal-fullscreen',
          controller: 'modalCtrl',
          resolve: {
            template: function() {
              return {
                src: routing.generate('admin_frontpage_get_preview',
                  { category: category })
              };
            },
            success: function() {
              return null;
            }
          }
        });

        $scope.loading = false;
      }).error(function() {
        $scope.loading = false;
      });
    };

    $scope.utcToTimezone = function(date) {
      return date === '' ? '' : window.moment.tz(date, 'UTC')
        .tz($scope.time.timezone).format('YYYY-MM-DD HH:mm');
    };

    $scope.saveLiveNow = function() {
      $scope.frontpageInfo.publish_date =
        window.moment.tz(new Date().getTime() - $scope.time.diff, 'UTC')
          .tz($scope.time.timezone).format('YYYY-MM-DD HH:mm:ss');

      $uibModal.open({
        backdrop:      true,
        backdropClass: 'modal-backdrop-transparent',
        controller:    'YesNoModalCtrl',
        openedClass:   'modal-relative-open',
        templateUrl:   'modal-publish-now',
        windowClass:   'modal-right modal-small modal-top',
        resolve: {
          template: function() {
            return {};
          },
          yes: function() {
            return function(modalWindow) {
              modalWindow.close({ response: false, success: true });
              $scope.saveWithoutCheckPD();
            };
          },
          no: function() {
            return function(modalWindow) {
              modalWindow.close({ response: false, success: true });
            };
          }
        }
      });
      $scope.save();
    };

    $scope.saveVersion = function() {
      $scope.version.id = null;
      $scope.save();
    };

    $scope.save = function() {
      if ($scope.frontpageInfo.publish_date === '') {
        $scope.saveWithoutCheckPD();
        return null;
      }
      var currentServerTime = new Date().getTime() - $scope.time.diff;
      var diffCurrToVer     =
        window.moment.tz(
          $scope.frontpageInfo.publish_date,
          $scope.time.timezone
        ).toDate().getTime() - currentServerTime;

      if (diffCurrToVer <= 0 || diffCurrToVer > 3600000) {
        $scope.saveWithoutCheckPD();
        return null;
      }

      $uibModal.open({
        backdrop:      true,
        backdropClass: 'modal-backdrop-transparent',
        controller:    'YesNoModalCtrl',
        openedClass:   'modal-relative-open',
        templateUrl:   'modal-publish-check',
        windowClass:   'modal-right modal-small modal-top',
        resolve: {
          template: function() {
            return {};
          },
          yes: function() {
            return function(modalWindow) {
              modalWindow.close({ response: false, success: true });
              $scope.saveWithoutCheckPD();
            };
          },
          no: function() {
            return function(modalWindow) {
              modalWindow.close({ response: false, success: true });
            };
          }
        }
      });
      return null;
    };

    $scope.saveWithoutCheckPD = function() {
      var els              = $scope.getContentsInFrontpage();
      var numberOfContents = els.length - els.filter(function(el) {
        return el.content_type === 'Advertisement';
      }).length;

      // If there is a new version available for this frontpage avoid to save
      if (numberOfContents > 100) {
        var message = frontpage_messages.frontpage_too_long.replace('%number%', 100);

        messenger.post(message);
      } else {
        var version = JSON.parse(JSON.stringify($scope.version));

        version.publish_date = $scope.frontpageInfo.publish_date === '' ?
          null :
          window.moment.tz(
            $scope.frontpageInfo.publish_date,
            $scope.time.timezone
          ).tz('UTC').format('YYYY-MM-DD HH:mm:ss');

        if (version.id === 0) {
          version.id = null;
        }
        http.post({
          name: 'admin_frontpage_savepositions'
        }, {
          contents_positions: els.length > 0 ? els : null,
          last_version:       $scope.frontpageInfo.lastSaved,
          contents_count:     els.length,
          version:            version,
          category:           $scope.categoryId
        }).then(function(response) {
          if (!response.data.frontpage_last_saved) {
            return false;
          }
          messenger.post(response.data.message);
          $scope.frontpageInfo.lastSaved = response.data.frontpage_last_saved;
          if (
            response.data.versionId &&
            version.id !== parseInt(response.data.versionId)
          ) {
            window.location = routing.generate(
              'admin_frontpage_list',
              {
                category: $scope.categoryId,
                version: response.data.versionId
              }
            );
          } else {
            $scope.version.publish_date = $scope.frontpageInfo.publish_date === '' ?
              null :
              window.moment.tz(
                $scope.frontpageInfo.publish_date,
                $scope.time.timezone
              ).tz('UTC').format('YYYY-MM-DD HH:mm:ss');
            $scope.versions.sort($scope.comparePublishDates);
            $scope.getReloadVersionStatus();
          }
          return null;
        }, function(response) {
          messenger.post(response.data.responseText);
          return null;
        });
      }
    };

    $scope.comparePublishDates = function(versionA, versionB) {
      if (versionA.publish_date === null) {
        return 1;
      }

      if (versionB.publish_date === null) {
        return -1;
      }

      return versionA.publish_date < versionB.publish_date ? 1 : -1;
    };

    $scope.getContentsInFrontpage = function() {
      var els   = [];
      var index = 0;

      document.querySelectorAll('div.placeholder').forEach(function(placeholderEle) {
        var placeholder = placeholderEle.getAttribute('data-placeholder');

        index = 0;
        placeholderEle.querySelectorAll('div.content-provider-element').forEach(
          function(element) {
            els.push({
              id:           element.getAttribute('data-content-id'),
              content_type: element.getAttribute('data-class'),
              placeholder:  placeholder,
              position:     index,
              params:       {}
            });
            index++;
          }
        );
      });

      return els;
    };

    $scope.checkAvailableNewVersion = function() {
      if (!$scope.frontpageInfo.checkNewVersion) {
        return null;
      }
      http.get({
        name:   'admin_frontpage_last_version',
        params: {
          category:  $scope.categoryId,
          versionId: $scope.versionId,
          date:      $scope.frontpageInfo.lastSaved
        }
      }).then(function(response) {
        if (response.data !== 'true') {
          return null;
        }
        $scope.frontpageInfo.checkNewVersion = false;

        $uibModal.open({
          backdrop:      true,
          backdropClass: 'modal-backdrop-transparent',
          controller:    'YesNoModalCtrl',
          openedClass:   'modal-relative-open',
          templateUrl:   'modal-new-version',
          windowClass:   'modal-right modal-small modal-top',
          resolve: {
            template: function() {
              return {};
            },
            yes: function() {
              return function() {
                location.reload();
              };
            },
            no: function() {
              return function(modalWindow) {
                modalWindow.close({ response: false, success: true });
              };
            }
          }
        });
        return null;
      }, function() {
        return null;
      });
      return null;
    };

    $scope.getCurrentVersion = function(versionId) {
      if (versionId === null && $scope.publishVersionId !== null) {
        versionId = $scope.publishVersionId;
      }

      if (versionId !== null) {
        for (var i = 0; i < $scope.versions.length; i++) {
          if ($scope.versions[i].id === versionId) {
            return $scope.versions[i];
          }
        }
      }

      var version = $scope.versions[0];

      for (var j = 1; j < $scope.versions.length; j++) {
        if ($scope.versions[j].publish_date > version.publish_date) {
          version = $scope.versions[j];
        }
      }
      return version;
    };

    $scope.deleteVersion = function(versionId) {
      http.delete({
        name:   'admin_frontpage_delete',
        params: {
          categoryId: $scope.categoryId,
          versionId:  versionId ? versionId : $scope.versionId
        }
      }).then(function(response) {
        messenger.post(response.data.message);
        window.location = routing.generate('admin_frontpage_list', {
          category: $scope.categoryId
        });
      }, function(response) {
        messenger.post(response.data.responseText);
      });
    };
  }
]);
