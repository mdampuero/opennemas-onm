/**
 * onm.queryManager Module.
 *
 * Module to get and set filters in URL.
 */
angular.module('onm.queryManager', [])
  .service('queryManager', ['$location',
    function($location) {
      'use strict';

      return {
        /**
         * Returns the processed filters from the URL.
         *
         * @return object The processed filters.
         */
        getParams: function() {
          var params = $location.search();
          params = angular.copy(params);

          var filters = {};

          if (params.epp) {
            filters.epp = parseInt(params.epp);
            delete params.epp;
          }

          if (params.page) {
            filters.page = parseInt(params.page);
            delete params.page;
          }

          if (params.orderBy) {
            filters.orderBy = [];

            var orders = params.orderBy.split(',');

            for (var i = 0; i < orders.length; i++) {
              var order = orders[i].split(':');

              filters.orderBy.push({
                name: order[0],
                value: order[1]
              });
            }

            delete params.orderBy;
          }

          filters.criteria = {};
          for (var name in params) {
            var target = name;
            if (name.indexOf('[]') != -1) {
              target = name.substring(0, name.indexOf('[]'));
            }

            filters.criteria[target] = params[name];
            delete params[name];
          }

          return filters;
        },

        /**
         * Updates the URL query.
         *
         * @param object criteria The search criteria.
         * @param object orderBy  The order by.
         * @param object epp      The elements per page.
         * @param object page     The current list page.
         */
        setParams: function(criteria, orderBy, epp, page) {
          for (var name in criteria) {
            var target = name;

            if (criteria[name] instanceof Array) {
              target += '[]';
            }

            if (criteria[name] != '' && criteria[name] != -1) {
              $location.search(target, criteria[name]);
            } else {
              $location.search(target, null);
            }
          }

          var order = null;
          for (var i = 0; i < orderBy.length; i++) {
            if (!order) {
              order = [];
            }

            order.push(orderBy[i].name + ':' + orderBy[i].value);
          }

          if (order) {
            order = order.join(',');
          }

          $location.search('orderBy', order);

          if (epp) {
            $location.search('epp', epp);
          }

          if (page) {
            $location.search('page', page);
          }
        }
      };
    }
  ]);
