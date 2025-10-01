(function() {
  'use strict';

  angular.module('ManagerApp.controllers')
    .controller('SecurityTwoFactorCtrl', [
      '$controller', '$location', '$scope', '$timeout', '$uibModal', '$q', 'http', 'messenger', 'oqlDecoder', 'oqlEncoder', 'webStorage',
      function($controller, $location, $scope, $timeout, $uibModal, $q, http, messenger, oqlDecoder, oqlEncoder, webStorage) {
        $.extend(this, $controller('ListCtrl', {
          $scope: $scope,
          $timeout: $timeout
        }));

        $scope.columns = {
          collapsed: 1,
          selected: [
            'name',
            'internal_name',
            'two_factor_enabled',
            'delete_session'
          ]
        };

        $scope.criteria = { epp: 25, page: 1 };

        $scope.list = function() {
          $scope.loading = 1;

          oqlEncoder.configure({
            placeholder: {
              name: 'name ~ "[value]" or internal_name ~ "[value]" or contact_mail ~ "[value]" or domains ~ "[value]"'
            }
          });

          var oql = oqlEncoder.getOql($scope.criteria);
          var route = {
            name: 'manager_ws_security_two_factor',
            params: { oql: oql }
          };

          $location.search('oql', oql);

          return http.get(route).then(function(response) {
            $scope.loading = 0;
            $scope.items = response.data.results;
            $scope.total = response.data.total;
            $scope.extra = response.data.extra || {};
            $scope.filteredItems = $scope.items;

            $('body').animate({ scrollTop: '0px' }, 1000);
          }, function(response) {
            $scope.loading = 0;
            messenger.post(response.data);
          });
        };

        $scope.resetFilters = function() {
          $scope.criteria = { epp: 25, page: 1 };
          $scope.list();
        };

        $scope.$watch('columns', function(newValues, oldValues) {
          if (newValues !== oldValues) {
            webStorage.local.set('security-two-factor-columns', $scope.columns);
          }
        }, true);

        if (webStorage.local.get('security-two-factor-columns')) {
          $scope.columns = webStorage.local.get('security-two-factor-columns');
        }

        if (!$scope.columns.selected) {
          $scope.columns.selected = [];
        }

        if ($scope.columns.selected.indexOf('delete_session') === -1) {
          $scope.columns.selected.push('delete_session');
        }

        oqlDecoder.configure({
          ignore: [ 'internal_name', 'contact_mail', 'domains' ]
        });

        if ($location.search().oql) {
          $scope.criteria = angular.extend(
            {},
            $scope.criteria,
            oqlDecoder.decode($location.search().oql)
          );
        }

        $scope.getItemId = function(item) {
          return item.id;
        };

        function getSelectedInstances() {
          if (!$scope.items || !$scope.selected || !$scope.selected.items) {
            return [];
          }

          return $scope.items.filter(function(instance) {
            return $scope.selected.items.indexOf(instance.id) !== -1;
          });
        }

        $scope.bulkTwoFactorLoading = false;
        $scope.bulkDeleteSessionLoading = false;

        $scope.toggleTwoFactor = function(item) {
          if (item.twoFactorLoading) {
            return;
          }

          var targetState = !item.two_factor_enabled;

          var modal = $uibModal.open({
            templateUrl: '/managerws/template/common:modal_confirm.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  icon: 'fa-shield',
                  name: targetState ? 'enable-two-factor' : 'disable-two-factor',
                  item: item
                };
              },
              success: function() {
                return function(modalWindow) {
                  item.twoFactorLoading = true;

                  var route = {
                    name: 'manager_ws_security_two_factor_save',
                    params: {
                      id: item.id,
                      enabled: targetState ? 1 : 0
                    }
                  };

                  http.put(route).then(function(response) {
                    item.twoFactorLoading = false;
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    item.twoFactorLoading = false;
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response && response.data) {
              messenger.post(response.data);
            }

            if (response && response.success) {
              item.two_factor_enabled = targetState;
            }
          }, angular.noop);
        };

        $scope.deleteSession = function(item) {
          if (item.deleteSessionLoading) {
            return;
          }

          var modal = $uibModal.open({
            templateUrl: '/managerws/template/common:modal_confirm.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  icon: 'fa-trash',
                  name: 'delete-session',
                  item: item
                };
              },
              success: function() {
                return function(modalWindow) {
                  item.deleteSessionLoading = true;

                  var route = 'manager_ws_security_two_factor_delete_session';
                  var data = { ids: [ item.id ] };

                  http.delete(route, data).then(function(response) {
                    item.deleteSessionLoading = false;
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    item.deleteSessionLoading = false;
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response && response.data) {
              messenger.post(response.data);
            }
          }, angular.noop);
        };

        $scope.toggleTwoFactorSelected = function(targetState) {
          if ($scope.bulkTwoFactorLoading) {
            return;
          }

          var selectedInstances = getSelectedInstances();

          if (!selectedInstances.length) {
            return;
          }

          var modal = $uibModal.open({
            templateUrl: '/managerws/template/common:modal_confirm.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  icon: 'fa-shield',
                  name: targetState ? 'enable-two-factor' : 'disable-two-factor',
                  selected: selectedInstances
                };
              },
              success: function() {
                return function(modalWindow) {
                  $scope.bulkTwoFactorLoading = true;

                  selectedInstances.forEach(function(instance) {
                    instance.twoFactorLoading = true;
                  });

                  var requests = selectedInstances.map(function(instance) {
                    var route = {
                      name: 'manager_ws_security_two_factor_save',
                      params: {
                        id: instance.id,
                        enabled: targetState ? 1 : 0
                      }
                    };

                    return http.put(route).then(function(response) {
                      return { success: true, response: response, instance: instance };
                    }, function(response) {
                      return $q.when({ success: false, response: response, instance: instance });
                    });
                  });

                  $q.all(requests).then(function(results) {
                    var successCount = 0;
                    var failureCount = 0;

                    results.forEach(function(result) {
                      result.instance.twoFactorLoading = false;

                      if (result.success) {
                        successCount += 1;
                        result.instance.two_factor_enabled = targetState;

                        var index = $scope.selected.items.indexOf(result.instance.id);

                        if (index !== -1) {
                          $scope.selected.items.splice(index, 1);
                        }
                      } else {
                        failureCount += 1;

                        if (result.response && result.response.data) {
                          messenger.post(result.response.data);
                        } else {
                          messenger.post('Unexpected error while updating two-factor authentication.', 'error');
                        }
                      }
                    });

                    if (successCount > 0) {
                      var successMessage = 'Two-factor authentication disabled successfully';

                      if (targetState) {
                          successMessage = 'Two-factor authentication enabled successfully';
                      }

                      if (successCount > 1) {
                        successMessage += ' for ' + successCount + ' instances.';
                      } else {
                        successMessage += '.';
                      }

                      messenger.post(successMessage, 'success');
                    }

                    $scope.selected.all = false;
                    $scope.bulkTwoFactorLoading = false;

                    modalWindow.close({ success: failureCount === 0 });
                  });
                };
              }
            }
          });

          modal.result.then(angular.noop, angular.noop);
        };

        $scope.deleteSessionsSelected = function() {
          if ($scope.bulkDeleteSessionLoading || $scope.bulkTwoFactorLoading) {
            return;
          }

          var selectedInstances = getSelectedInstances();

          if (!selectedInstances.length) {
            return;
          }

          var modal = $uibModal.open({
            templateUrl: '/managerws/template/common:modal_confirm.' + appVersion + '.tpl',
            backdrop: 'static',
            controller: 'modalCtrl',
            resolve: {
              template: function() {
                return {
                  icon: 'fa-trash',
                  name: 'delete-sessions',
                  selected: selectedInstances
                };
              },
              success: function() {
                return function(modalWindow) {
                  $scope.bulkDeleteSessionLoading = true;

                  var data = {
                    ids: selectedInstances.map(function(instance) {
                      return instance.id;
                    })
                  };

                  http.delete('manager_ws_security_two_factor_delete_session', data).then(function(response) {
                    $scope.bulkDeleteSessionLoading = false;
                    modalWindow.close({ data: response.data, success: true });
                  }, function(response) {
                    $scope.bulkDeleteSessionLoading = false;
                    modalWindow.close({ data: response.data, success: false });
                  });
                };
              }
            }
          });

          modal.result.then(function(response) {
            if (response && response.data) {
              messenger.post(response.data);
            }

            if (response && response.success) {
              $scope.selected = { all: false, items: [] };
            }
          }, angular.noop);
        };

        $scope.list();
      }
    ]);
})();
