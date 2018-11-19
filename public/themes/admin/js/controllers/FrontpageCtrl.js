/**
 * Controller to use in inner sections.
 */
angular.module('BackendApp.controllers').controller('FrontpageCtrl', [
  '$controller', 'http', '$uibModal', '$scope', '$interval', 'routing',
  'messenger', '$window',
  function($controller, http, $uibModal, $scope, $interval, routing, messenger, $window) {
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
      lastSaved:           null,
      publish_date:        window.moment.tz(new Date(), 'UTC'),
      checkNewVersion:     true,
      originalVersionName: null
    };

    $scope.init = function(frontpages, versions, categoryId, versionId, time,
        frontpageLastSaved, layouts, layout) {
      $scope.categoryId              = parseInt(categoryId);
      $scope.frontpages              = frontpages;
      $scope.frontpage               = frontpages[$scope.categoryId];
      $scope.frontpageInfo.lastSaved = frontpageLastSaved;
      $scope.time                    = time;
      $scope.time.diff               = new Date().getTime() - time.timestamp;
      $scope.scheduledFFuture        = [];
      $scope.layouts                 = layouts;
      $scope.layout                  = layout;

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

      var copyVersionName = (' ' + $scope.version.name).slice(1);

      $scope.frontpageInfo.originalVersionName = copyVersionName;

      $interval($scope.getReloadVersionStatus, 60000);
      $interval($scope.checkAvailableNewVersion, 10000);

      $($window).bind('beforeunload', function() {
        $scope.frontpageForm.$setPristine(true);
      });
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
              return http.get({
                name: 'admin_content_set_archived',
                params: { ids: $scope.selected.contents }
              })
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

      messenger.post({
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

    $scope.preview = function() {
      $scope.loading = true;

      var contents = $scope.getContentsInFrontpage();
      var encoded  = JSON.stringify(contents);
      var data     = {
        category: $scope.categoryId,
        contents: encoded,
        version:  $scope.version.id
      };

      http.post({
        name: 'admin_frontpage_preview',
        params: { category: $scope.categoryId }
      }, data).success(function() {
        $uibModal.open({
          templateUrl: 'modal-preview',
          windowClass: 'modal-fullscreen',
          controller: 'modalCtrl',
          resolve: {
            template: function() {
              return {
                src: routing.generate('admin_frontpage_get_preview',
                  {
                    category: $scope.categoryId
                  }
                )
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

      $scope.liveNowModal();
    };

    $scope.saveVersion = function() {
      $scope.version.id = null;

      var date = window.moment.tz(
        $scope.frontpageInfo.publish_date,
        $scope.time.timezone
      ).tz('UTC').format('YYYY-MM-DD HH:mm:ss');

      if ($scope.frontpageInfo.originalVersionName === $scope.version.name) {
        $scope.version.name = $scope.getAutoVersionName($scope.version.name);
      }

      if (date === $scope.version.publish_date) {
        $scope.frontpageInfo.publish_date = '';
      }
      $scope.save();
    };

    $scope.getAutoVersionName = function(versionName) {
      var haveVersionRegex = /-v[0-9]+$/;

      var number  = 0;
      var newName = versionName;

      if (haveVersionRegex.test(versionName)) {
        var matchVersion = versionName.match(haveVersionRegex)[0];

        number  = parseInt(matchVersion.slice(2));
        newName = versionName.slice(0, -1 * matchVersion.length);
      }

      return $scope.getVersionForName(newName, number);
    };

    $scope.getVersionForName = function(versionName, versionNumber) {
      var versionNames = [];

      for (var i = 0; i < $scope.versions.length; i++) {
        versionNames.push($scope.versions[i].name);
      }

      versionNumber++;
      var newName = versionName + '-v' + versionNumber;

      while (versionNames.indexOf(newName) > -1) {
        versionNumber++;
        newName = versionName + '-v' + versionNumber;
      }

      return newName;
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

      if (diffCurrToVer <= 0) {
        var publishDateUTC = window.moment.tz(
          $scope.frontpageInfo.publish_date,
          $scope.time.timezone
        ).toDate().getTime();

        var currentVerTime = window.moment.tz(
          $scope.getCurrentVersion().publish_date,
          'UTC'
        ).toDate().getTime();

        if (publishDateUTC > currentVerTime) {
          $scope.liveNowModal();
          return null;
        }
      }

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
          $scope.showMessage(response.data.message, 'success', 1);
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
          $scope.showMessage(response.data.message, 'error', 5);
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

      $(document).find('div.placeholder').each(function(index, placeholderEle) {
        var placeholder = $(placeholderEle).data('placeholder');

        index = 0;
        $(placeholderEle).find('div.content-provider-element').each(
          function(index, element) {
            var el = $(element);

            els.push({
              id:           el.data('content-id'),
              content_type: el.data('class'),
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
        if (response.data !== true) {
          return null;
        }

        $scope.frontpageInfo.checkNewVersion = false;

        var modal = $uibModal.open({
          templateUrl: 'modal-new-version',
          backdrop: 'static',
          controller: 'modalCtrl',
          resolve: {
            template: function() {
              return {
              };
            },
            success: function() {
              return null;
            }
          }
        });

        modal.result.then(function(response) {
          if (response) {
            location.reload();
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

    $scope.deleteVersion = function($event, versionId) {
      $event.stopPropagation();
      http.delete({
        name:   'admin_frontpage_delete',
        params: {
          categoryId: $scope.categoryId,
          versionId:  versionId ? versionId : $scope.versionId
        }
      }).then(function(response) {
        if (versionId === $scope.version.id) {
          window.location = routing.generate('admin_frontpage_list', {
            category: $scope.categoryId
          });
          return null;
        }
        var index = $scope.versions.length - 1;

        for (index; index >= 0; index--) {
          if ($scope.versions[index].id === versionId) {
            break;
          }
        }
        if (index > -1) {
          $scope.versions.splice(index, 1);
        }
        messenger.post(response.data.message);
        return null;
      }, function(response) {
        if (response.data) {
          messenger.post(response.data.responseText);
        }
      });
    };

    /**
     * Opens layout modal window.
     *
     * @param {String} name The modal name.
     */
    $scope.openLayoutModal = function() {
      $uibModal.open({
        templateUrl: 'modal-layout',
        backdrop: true,
        controller: 'modalCtrl',
        resolve: {
          template: function() {
            return {
              layouts:      $scope.layouts,
              layout:       $scope.layout,
              changeLayout: function(layoutKey) {
                window.location = routing.generate(
                  'admin_frontpage_pick_layout',
                  {
                    category:  $scope.categoryId,
                    versionId: $scope.version.id,
                    layout:    layoutKey
                  }
                );
              },
              toArray: function(obj) {
                return $scope.toArray(obj);
              },
            };
          },
          success: function() {
            return null;
          }
        }
      });
    };

    $scope.liveNowModal = function() {
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
    };
  }
]);
