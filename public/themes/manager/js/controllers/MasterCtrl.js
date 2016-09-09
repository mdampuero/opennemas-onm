(function () {
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
    .controller('MasterCtrl', [ '$http', '$location', '$uibModal', '$rootScope',
      '$scope', '$translate', '$timeout', '$window', 'vcRecaptchaService',
      'jwtHelper', 'httpInterceptor', 'authService', 'routing', 'history', 'http',
      'webStorage', 'messenger', 'cfpLoadingBar', 'security',
      function ($http, $location, $uibModal, $rootScope, $scope, $translate,
          $timeout, $window, vcRecaptchaService, jwtHelper, httpInterceptor,
          authService, routing, history, http, webStorage, messenger, cfpLoadingBar, security) {
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
         * Flag to show modal window for login only once.
         *
         * @type boolean
         */
        $scope.auth = {
          status: true,
          modal: false,
          inprogress: false
        };

        /**
         * The available elements per page
         *
         * @type {Array}
         */
        $scope.views = [ 10, 25, 50, 100 ];

        /**
         * Removes a class from body and checks if user is authenticated.
         */
        $scope.init = function(language) {
          $translate.use(language);
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

          authService.login('manager_ws_auth_check', data, $scope.attempts)
            .then(function (response) {
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
            }, function () {
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
                  $scope.auth = {
                    status:     false,
                    modal:      false,
                    inprogress: true
                  };

                  $scope.security.reset();
                  webStorage.local.remove('security');

                  modalWindow.close({ success: true });
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
         * Scrolls the page to top.
         */
        $scope.scrollTop = function() {
          $('body').animate({ scrollTop: 0 }, 250);
        };

        /**
         * Shows the login form when login is required.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $scope.$on('auth-login-required', function (event, args) {
          if (args.config.ignoreAuthModule) {
            messenger.post(args.data);
          }

          $scope.auth.status  = false;
          $scope.loaded       = true;
          $scope.loading      = false;
          $scope.loginLoading = false;

          cfpLoadingBar.complete();

          if (!$scope.auth.inprogress) {
            $scope.auth.inprogress = true;

            if ($scope.auth.modal) {
              var modal = $uibModal.open({
                templateUrl: 'modal-login',
                backdrop: 'static',
                controller: 'LoginModalCtrl',
                resolve: {
                  data: function() {
                    var url = routing.generate('manager_ws_auth_login');

                    return $http.post(url).then(function (response) {
                      return response.data;
                    });
                  }
                }
              });

              modal.result.then(function (data) {
                $scope.auth.status = data.success;

                if (data.success) {
                  $scope.auth.inprogress = false;
                  $scope.user            = data.user;

                  httpInterceptor.loginConfirmed();
                }
              });
            } else {
              var url = routing.generate('manager_ws_auth_login');

              $http.post(url).then(function (response) {
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
        $scope.$on('auth-login-confirmed', function (event, args) {
          $http.defaults.headers.common.Authorization = 'Bearer ' + args.token;
          $scope.auth.inprogress = false;
          $scope.auth.modal      = false;
          $scope.auth.status     = true;

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

        /**
         * Submits a fake login form and force browsers to save credentials.
         */
        function fakeLogin() {
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
        }

        /**
         * Empties ng-view when route changes.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $rootScope.$on('$routeChangeStart', function (event, next, current) {
          if ($location.path().indexOf('framework') !== -1) {
            return false;
          }

          history.restore($location.path());
          history.push($location.path(), $location.search());
        });

        /**
         * Shows a modal to force page reload.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $scope.$on('application-need-upgrade', function (event, args) {
          $uibModal.open({
            templateUrl: 'modal-upgrade',
            controller: 'MasterCtrl',
            backdrop: 'static'
          });
        });

        // Redirects to /403
        $scope.$on('error-403', function (event, args) {
          $scope.errors = args.data;
          $location.url('/403');

          http.get('manager_ws_auth_refresh').then(function(response) {
            security.instance    = response.data.instance;
            security.instances   = response.data.instances;
            security.permissions = response.data.permissions;
            security.user        = response.data.user;

            webStorage.local.set('security', {
              instance:    security.instance,
              instances:   security.instances,
              permissions: security.permissions,
              token:       security.token,
              user:        security.user
            });
          });
        });

        /**
         * Shows a message when an error while sending an Ajax request occurs.
         *
         * @param Object event The event object.
         * @param array  args  The list of arguments.
         */
        $scope.$on('error-404', function (event, args) {
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
        $('body').on('click', 'a', function (e) {
          if ($(this).attr('href') === '#') {
            e.preventDefault();
          }
        });
      }
  ]);
})();
