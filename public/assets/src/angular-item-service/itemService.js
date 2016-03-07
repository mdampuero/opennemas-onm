(function() {
  'use strict';

  /**
   * Service to implement common actions related to item.
   *
   * @param Object $http     Http service
   * @param Object $location Location service
   * @param Object $modal    Modal service
   * @param Object routing   Onm routing service.
   *
   * @return Object The item service.
   */
  angular.module('onm.item', [/*'onm.oqlEncoder'*/])
    .factory('itemService', [
      '$http', '$location', '$modal', 'routing', /*'oqlEncoder',*/
      function($http, $location, $modal, routing/*, oqlEncoder*/) {
        /**
         * The item service.
         *
         * @type Object
         */
        var itemService = {};

        /**
         * Redirects to the listing.
         *
         * @param string route The route name.
         */
        itemService.cancel = function(route) {
          var url = routing.generate(route);
          $location.path(url);
        }

      /**
       * Cleans the criteria for the current listing.
       *
       * @param Object criteria The search criteria.
       *
       * @return Object The cleaned criteria.
       *
       * TODO: Remove cleanFilters as it is replaced by media query functions.
       */
      itemService.cleanFilters = function (criteria) {
        var cleaned = {};

        for (var name in criteria) {
          if (name != 'union') {
            for (var i = 0; i < criteria[name].length; i++) {
              if (criteria[name][i]['value']
                && criteria[name][i]['value'] != -1
                && criteria[name][i]['value'] !== ''
              ){
                var values = criteria[name][i]['value'].split(' ');

                if (name.indexOf('_like') !== -1 ) {
                  var shortName = name.substr(0, name.indexOf('_like'));
                  cleaned[shortName] = [];

                  for (var i = 0; i < values.length; i++) {
                    cleaned[shortName][i] = {
                      value:    '%' + values[i] + '%',
                      operator: 'LIKE'
                    };
                  }
                } else {
                  cleaned[name] = [];
                  for (var i = 0; i < values.length; i++) {
                    if (criteria[name]['operator']) {
                      switch(criteria[name]['operator']) {
                        case 'like':
                          cleaned[name][i] = {
                              value:    '%' + values[i] + '%',
                              operator: 'LIKE'
                          };
                          break;
                        case 'regexp':
                          cleaned[name][i] = {
                              value:    '(^' + values[i] + ',)|('
                                  + ',' + values[i] + ',)|('
                                  + values[i] + '$)',
                              operator: 'REGEXP'
                          };
                          break;
                        default:
                          cleaned[name][i] = {
                              value:    values[i],
                              operator: criteria[name]['operator']
                          };
                      }
                    } else {
                      cleaned[name][i] = {
                          value: values[i],
                      };
                    }
                  }
                }
              }
            }
          } else {
            cleaned[name] = criteria[name];
          }
        };

        return cleaned;
      }
        /**
         * Deletes a plugin given its id.
         *
         * @param string  route The route name.
         * @param integer id    The item id.
         *
         * @return Object The response object.
         */
        itemService.delete = function(route, id) {
          var url = routing.generate(route, {
            id: id
          });

          return $http.delete(url);
        };

        /**
         * Deletes a plugin given its id.
         *
         * @param string route    The route name.
         * @param array  selected The selected items.
         *
         * @return Object The response object.
         */
        itemService.deleteSelected = function(route, selected) {
          var url = routing.generate(route);
          var data = {
            selected: selected
          };

          return $http({
            method: 'DELETE',
            url: url,
            headers: {
              'Content-type': 'application/x-www-form-urlencoded;charset=utf-8'
            },
            data: data,

          });
        };

        /**
         * Parses the current URL and initializes the current filters.
         */
        itemService.decodeFilters = function() {
          var params = $location.search();
          params = angular.copy(params);

          var filters = {};

          if (params.epp) {
            filters.epp = params.epp;
          }
          delete params.epp;

          if (params.page) {
            filters.page = params.page
          }
          delete params.page

          if (params.orderBy) {
            filters.orderBy = [];

            var orders = params.orderBy.split(',');

            for (var i = 0; i < orders.length; i++) {
              var order = orders[i].split(':');

              filters.orderBy.push({
                name: order[0],
                value: order[1]
              });
            };

          }
          delete params.orderBy;

          var union = null;
          if (params.union) {
            union = params.union;
          }
          delete params.union;

          filters.criteria = {};
          var empty = 1;
          var pattern = /[a-z_]\d+/;
          for (var name in params) {
            var target = name;
            if (pattern.test(name)) {
              target = name.substr(0, name.lastIndexOf('_'));
            }

            if (!filters.criteria[target]) {
              filters.criteria[target] = [];
            }

            filters.criteria[target].push({
              value: params[name]
            });
            empty = 0;

            delete params[name];
          }

          if (!empty && union) {
            filters.criteria.union = union;
          }

          return filters;
        }

        /**
         * Parses the current filters and updates the URL.
         *
         * @param Object  criteria The criteria to search by.
         * @param Object  orderBy  The order to use while searching.
         * @param integer epp      The elements per page.
         * @param integer page     The current page.
         * @param integer union    The operator to join filters.
         */
        itemService.encodeFilters = function(criteria, orderBy, epp, page, union) {
          var empty = true;
          for (var name in criteria) {
            empty = false;
            for (var i in criteria[name]) {
              if (criteria[name][i].value != '' && criteria[name][i].value != -1) {
                $location.search(name + '_' + i, criteria[name][i].value);
              } else {
                $location.search(name + '_' + i, null);
              }
            };
          }

          var order = null;
          for (var i = 0; i < orderBy.length; i++) {
            if (!order) {
              order = [];
            }

            order.push(orderBy[i].name + ':' + orderBy[i].value);
          };

          if (order) {
            order = order.join(',');
          }

          $location.search('orderBy', order);

          if (epp) {
            $location.search('epp', epp);
          }

          if (page) {
            $location.search('page', page)
          }
        }

        /**
         * Checks if the given name is available.
         *
         * @param string route The route name.
         * @param string name  The name to check.
         *
         * @return Object The response object.
         */
        itemService.isAvailable = function(route, name) {
          var url = routing.generate(route);
          var data = {
            name: name
          };

          return $http.post(url, data);
        };

        /**
         * Returns a list of items.
         *
         * @param string route    The route name.
         * @param object criteria The parameters to search by.
         *
         * @return Object The response object.
         *
         * TODO: Remove cleanFilters usage and replace by query manager functions.
         */
        itemService.list = function(route, data) {
          // Decode filters from URL and overwrite data
          var filters = itemService.decodeFilters();
          filters.criteria = itemService.cleanFilters(filters.criteria);

          // Merge data with filters from URL
          if (filters.criteria && !data.criteria) {
            data.criteria = {};
          }

          for (var name in filters.criteria) {
            data.criteria[name] = filters.criteria[name];
          }

          // Merge data with filters from URL
          if (filters.orderBy) {
            data.orderBy = filters.orderBy;
          }

          if (filters.page) {
            data.page = filters.page;
          }

          if (filters.epp) {
            data.epp = filters.epp;
          }

          var url = routing.generate(route, data);

          return $http.get(url);
        };

        /**
         * Returns the template parameters to create a new item.
         *
         * @param string  route The route name.
         *
         * @return Object The response object.
         */
        itemService.new = function(route) {
          var url = routing.generate(route);

          return $http.get(url);
        };

        /**
         * Saves an item.
         *
         * @param  string id   The route name.
         * @param  object data The item data.
         *
         * @return Object The response object.
         */
        itemService.save = function(route, data) {
          var url = routing.generate(route);

          return $http.post(url, data);
        };

        /**
         * Enables/disables an item.
         *
         * @param string  route The route name.
         * @param integer id The item id.
         * @param integer id The enabled value.
         *
         * @return Object The response object.
         */
        itemService.patch = function(route, id, data) {
          var url = routing.generate(route, {
            id: id
          });
          return $http.patch(url, data);
        };

        /**
         * Enables/disables a list of elements.
         *
         * @param string  route The route name.
         * @param integer data  The selected items and the changes.
         *
         * @return Object The response object.
         */
        itemService.patchSelected = function(route, data) {
          var url = routing.generate(route);

          return $http.patch(url, data);
        };

        /**
         * Returns an item given its id.
         *
         * @param string  route The route name.
         * @param integer id The item id.
         *
         * @return Object The response object.
         */
        itemService.show = function(route, id) {
          var url = routing.generate(route, {
            id: id
          });

          return $http.get(url);
        };

        /**
         * Updates an item.
         *
         * @param  string id   The route name.
         * @param  mixed  id   The item id.
         * @param  object data The item data.
         *
         * @return Object The response object.
         */
        itemService.update = function(route, id, data) {
          var url = routing.generate(route, {
            id: id
          });

          return $http.put(url, data);
        };

        /**
         * Executes a command and returns its name and output.
         *
         * @param  string route   The route name.
         * @param  mixed  command The name of the command.
         * @param  object data    Additional data to execute the command.
         *
         * @return Object The response object.
         */
        itemService.executeCommand = function(route, command, data) {
          var parameters = {
            command: command,
            data: data
          };

          var url = routing.generate(route, parameters);

          return $http.get(url);
        };

        /**
         * Fetches the Zend Opcache.
         *
         * @param  string id   The route name.
         * @param  mixed  id   The item id.
         * @param  object data The item data.
         *
         * @return Object The response object.
         */
        itemService.fetchOpcacheStatus = function(route) {
          var url = routing.generate(route);

          return $http.get(url);
        };

        return itemService;
      }
    ]
  );

})();
