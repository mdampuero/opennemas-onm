(function() {
  'use strict';

  angular.module('ManagerApp.controllers')

    /**
     * @ngdoc controller
     * @name  MasterCtrl
     *
     * @requires require
     *
     * @description
     *   description
     */
    .controller('MasterCtrl', [
      '$http', '$location', '$uibModal', '$rootScope',
      '$scope', '$translate', '$timeout', '$window', 'vcRecaptchaService',
      'jwtHelper', 'httpInterceptor', 'authService', 'routing', 'history', 'http',
      'webStorage', 'messenger', 'cfpLoadingBar', 'security',
      function($http, $location, $uibModal, $rootScope, $scope, $translate,
          $timeout, $window, vcRecaptchaService, jwtHelper, httpInterceptor,
          authService, routing, history, http, webStorage, messenger, cfpLoadingBar, security) {
        /**
         * @memberOf MasterCtrl
         *
         * @description
         *  Application configuration options.
         *
         * @type {Object}
         */
        $scope.app = {
          auth: {
            status: true,
            modal: false,
            inprogress: false
          },
          help: true
        };

        /**
         * The routing service.
         *
         * @type Object
         */
        $scope.routing = routing;

        /**
         * @memberOf MasterCtrl
         *
         * @description
         *  The security service.
         *
         * @type {Object}
         */
        $scope.security = security;

        /**
         * The available elements per page
         *
         * @type {Array}
         */
        $scope.views = [ 10, 25, 50, 100 ];

        /**
         * @function addEmptyValue
         * @memberOf MasterCtrl
         *
         * @description
         *   Adds an empty value at key 0 in a map that will be used in a selector
         *   like ui-select to filter.
         *
         * @param {Object} obj      The map.
         * @param {String} property The name of the key when filtering in the
         *                          selector.
         *
         * @return {Object} The map with the empty value.
         */
        $scope.addEmptyValue = function(obj, property) {
          if (!angular.isArray(obj) && !angular.isObject(obj)) {
            return obj;
          }

          var key   = property ? property : 'id';
          var value = { name: name ? name : $scope.any };

          value[key] = null;

          if (!obj[0] || obj[0][key] !== null) {
            if (angular.isArray(obj)) {
              obj.unshift(value);
            } else {
              obj[0] = value;
            }
          }

          return obj;
        };

        /**
         * Removes a class from body and checks if user is authenticated.
         */
        $scope.init = function(language, any) {
          $translate.use(language);
          $scope.any = any;
        };

        /**
         * @function isHelpEnabled
         * @memberOf MasterCtrl
         *
         * @description
         *   Checks if flag for inline help is enabled.
         *
         * @return {Boolean} True if the flag is enabled. False otherwise.
         */
        $scope.isHelpEnabled = function() {
          return $scope.app.help;
        };

        /**
         * Logs an user in.
         */
        $scope.login = function() {
          $scope.loginLoading = 1;

          var data = {
            _username: $scope.username,
            _password: $scope.password,
            _token:    $scope.token,
          };

          /**
           * Submits a fake login form and force browsers to save credentials.
           */
          var fakeLogin = function() {
            var iframe    = document.getElementById('fake-login');
            var iframedoc = iframe.contentWindow ? iframe.contentWindow.document : iframe.contentDocument;

            var fakeForm     = iframedoc.getElementById('fake-login-form');
            var fakeUsername = iframedoc.getElementById('username');
            var fakePassword = iframedoc.getElementById('password');

            fakeUsername.value = $scope.username;
            fakePassword.value = $scope.password;

            // TODO: Remove this when https supported
            if ($scope.password && $scope.password.indexOf('md5:') === -1) {
              fakePassword.value = 'md5:' + hex_md5($scope.password);
            }

            fakeForm.submit();
          };

          authService.login('manager_ws_auth_check', data, $scope.attempts)
            .then(function(response) {
              $scope.loginLoading = 0;

              if (response.status === 200) {
                httpInterceptor.loginConfirmed(response.data);
                fakeLogin();
              } else {
                $scope.token    = response.data.token;
                $scope.attempts = response.data.attempts;
                $scope.message  = response.data.message;

                $scope.loginForm.$setPristine();
              }
            }, function() {
              $scope.loginLoading = 0;
            });
        };

        /**
         * Logs the user out.
         */
        $scope.logout = function() {
          $uibModal.open({
            templateUrl: 'modal-logout',
            controller:  'modalCtrl',
            resolve: {
              template: function() {
                return {
                  name: 'logout'
                };
              },
              success: function() {
                return function(modalWindow) {
                  webStorage.local.remove('security');

                  modalWindow.close({ success: true });
                  $window.location.reload();
                };
              }
            }
          });
        };

        /**
         * Force page reload.
         */
        $scope.reload = function() {
          $window.location.reload();
        };

        /**
         * @function refreshSecurity
         * @memberOf MasterCtrl
         *
         * @description
         *   Refreshes security credentials.
         */
        $scope.refreshSecurity = function() {
          return http.get('manager_ws_auth_refresh').then(function(response) {
            $scope.security.instance    = response.data.instance;
            $scope.security.instances   = response.data.instances;
            $scope.security.permissions = response.data.permissions;
            $scope.security.user        = response.data.user;

            webStorage.local.set('security', {
              instance:    security.instance,
              instances:   security.instances,
              permissions: security.permissions,
              token:       security.token,
              user:        security.user
            });
          });
        };

        /**
         * Scrolls the page to top.
         */
        $scope.scrollTop = function() {
          $('body').animate({ scrollTop: 0 }, 250);
        };

        /**
         * @function toArray
         * @memberOf MasterCtrl
         *
         * @description
         *   Converts a map to an array that can be used in selectors like
         *   ui-select.
         *
         * @param {Object} obj The object map.
         *
         * @return {Array} The array.
         */
        $scope.toArray = function(obj) {
          var arr = [];

          for (var key in obj) {
            arr.push(obj[key]);
          }

          return arr;
        };

        /**
         * @function toggleHelp
         * @memberOf MasterCtrl
         *
         * @description
         *   Enables/disables the flags for inline help.
         */
        $scope.toggleHelp = function() {
          $scope.app.help = !$scope.app.help;
        };

        // Shows the login form when login is required
        $scope.$on('auth-login-required', function(event, args) {
          if (args.config.ignoreAuthModule) {
            messenger.post(args.data);
          }

          $scope.app.auth.status = false;
          $scope.loaded          = true;
          $scope.loading         = false;
          $scope.loginLoading    = false;

          cfpLoadingBar.complete();

          if (!$scope.app.auth.inprogress) {
            $scope.app.auth.inprogress = true;

            if ($scope.app.auth.modal) {
              var modal = $uibModal.open({
                templateUrl: 'modal-login',
                backdrop: 'static',
                controller: 'LoginModalCtrl',
                resolve: {
                  data: function() {
                    var url = routing.generate('manager_ws_auth_login');

                    return $http.post(url).then(function(response) {
                      return response.data;
                    });
                  }
                }
              });

              modal.result.then(function(data) {
                $scope.app.auth.status = data.success;

                if (data.success) {
                  $scope.app.auth.inprogress = false;
                  $scope.user                = data.user;

                  httpInterceptor.loginConfirmed();
                }
              });
            } else {
              var url = routing.generate('manager_ws_auth_login');

              $http.post(url).then(function(response) {
                $scope.token     = response.data.token;
                $scope.attempts  = response.data.attempts;
              });
            }
          }
        });

        /**
         * Updates the user and the auth status on event.
         *
         * @param Object event The event object.
         * @param Object args  The user object.
         */
        $scope.$on('auth-login-confirmed', function(event, args) {
          $http.defaults.headers.common.Authorization = 'Bearer ' + args.token;
          $scope.app.auth.inprogress = false;
          $scope.app.auth.modal      = false;
          $scope.app.auth.status     = true;

          security.instance    = args.instance;
          security.instances   = args.instances;
          security.permissions = args.permissions;
          security.token       = args.token;
          security.user        = jwtHelper.decodeToken(args.token).user;

          webStorage.local.set('security', {
            instance:    security.instance,
            instances:   security.instances,
            permissions: security.permissions,
            token:       security.token,
            user:        security.user
          });
        });

        // Empties ng-view when route changes
        $rootScope.$on('$routeChangeStart', function() {
          if ($location.path().indexOf('framework') !== -1) {
            return;
          }

          var current = $location.search();

          if ($location.search() === {}) {
            history.restore($location.path());
          }

          history.push($location.path(), angular.merge($location.search(), current));
        });

        // Shows a modal to force page reload
        $scope.$on('application-need-upgrade', function() {
          $uibModal.open({
            templateUrl: 'modal-upgrade',
            controller: 'MasterCtrl',
            backdrop: 'static'
          });
        });

        // Redirects to /403
        $scope.$on('error-403', function(event, args) {
          $scope.errors = args.data;
          $location.url('/403');

          $scope.refreshSecurity();
        });

        // Shows a message when an error while sending an Ajax request occurs
        $scope.$on('error-404', function(event, args) {
          messenger.post(args.data);
        });

        webStorage.prefix('ONM-');
        if (webStorage.local.has('security')) {
          var s = webStorage.local.get('security');

          security.user        = s.user;
          security.token       = s.token;
          security.permissions = s.permissions;
          security.instance    = s.instance;
          security.instances   = s.instances;

          $http.defaults.headers.common.Authorization =
            'Bearer ' + security.token;

          $scope.loaded = true;
        }

        // Prevent empty links to change angular route
        $('body').on('click', 'a', function(e) {
          if ($(this).attr('href') === '#') {
            e.preventDefault();
          }
        });
      }
    ]);
})();
