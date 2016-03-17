(function () {
  'use strict';

  /**
   * @ngdoc module
   * @name  onm.oql
   *
   * @description
   *   The `onm.oql` module provider OQL-related services.
   */
  angular.module('onm.oql', [])
    /**
     * @ngdoc service
     * @name  oqlBuilder
     *
     * @description
     *   The `oqlBuilder` module creates OQL queries basing on objects.
     */
    .service('oqlBuilder', function() {
      /**
       * @memberOf oqlBuilder
       *
       * @description
       *  The builder configuration.
       *
       * @type {Object}
       */
      this.config = {
        defaults: {
          epp: 25
        },
        placeholder: {
          oql:       '[filter] [orderBy] [limit] [offset]',
          condition: '[key]="[value]"'
        },
      };

      /**
       * @function configure
       * @memberOf oqlBuilder
       *
       * @description
       *   Configures the builder.
       *
       * @param {Object} config The builder configuration.
       */
      this.configure = function(config) {
        this.config = angular.merge({}, this.config, config);
      };

      /**
       * @function getCondition
       * @memberOf oqlBuilder
       *
       * @description
       *   Returns a condition basing on the key and value.
       *
       * @param {String} key   The condition field name.
       * @param {String} value The condition field value.
       *
       * @return {String} The condition.
       */
      this.getCondition = function(key, value) {
        // If placeholder
        if (this.config.placeholder[key]) {
          var condition = this.config.placeholder[key];

          condition = condition.split('[key]').join(key);
          condition = condition.split('[value]').join(value);

          return condition;
        }

        return this.config.placeholder.condition
          .split('[key]').join(key).split('[value]').join(value);
      };

      /**
       * @function getConditions
       * @memberOf oqlBuilder
       *
       * @description
       *   Returns the list of conditions basing on the criteria.
       *
       * @param {Object} criteria The criteria.
       *
       * @return {Array} The list of conditions.
       */
      this.getConditions = function(criteria) {
        var conditions = {};
        for (var key in criteria) {
          // Only for conditions
          if (key !== 'page' && key !== 'epp' && key !== 'orderBy' &&
              criteria[key] !== null && criteria[key] !== '') {
            conditions[key] = this.getCondition(key, criteria[key]);
          }
        }

        return conditions;
      };

      /**
       * @function getFilter
       * @memberOf oqlBuilder
       *
       * @description
       *   Returns the filter query basing on the criteria.
       *
       * @param {Object} criteria The criteria.
       *
       * @return {String} The filter query.
       */
      this.getFilter = function(criteria) {
        var conditions = this.getConditions(criteria);

        if (this.config.placeholder.filter) {
          var filter = this.config.placeholder.filter;
          for (var key in conditions) {
            filter = filter.split('[' + key + ']').join(conditions[key]);
          }

          return filter;
        }

        var filter = [];
        for (var key in conditions) {
          filter.push(conditions[key]);
        }

        return filter.join(' and ');
      };

      /**
       * @function getLimit
       * @memberOf oqlBuilder
       *
       * @description
       *   Returns the limit query basing on the criteria.
       *
       * @param {Object} criteria The criteria.
       *
       * @return {String} The limit query.
       */
      this.getLimit = function(criteria) {
        if (criteria.epp > 0) {
          return 'limit ' + criteria.epp;
        }

        return ' limit ' + this.config.defaults.epp;
      };

      /**
       * @function getOffset
       * @memberOf oqlBuilder
       *
       * @description
       *   Returns the offset query basing on the criteria.
       *
       * @param {Object} criteria The criteria.
       *
       * @return {String} The offset query.
       */
      this.getOffset = function(criteria) {
        var offset = (criteria.page - 1) * criteria.epp;

        if (offset > 0) {
          return 'offset ' + offset;
        }

        return '';
      };

      /**
       * @function getOql
       * @memberOf oqlBuilder
       *
       * @description
       *   Returns the OQL query basing on the criteria.
       *
       * @param {Object} criteria The criteria.
       *
       * @return {String} The OQL query.
       */
      this.getOql = function(criteria) {
        var oql = this.config.placeholder.oql;

        oql = oql.replace('[filter]', this.getFilter(criteria));
        oql = oql.replace('[orderBy]', this.getOrderBy(criteria));
        oql = oql.replace('[limit]', this.getLimit(criteria));
        oql = oql.replace('[offset]', this.getOffset(criteria));

        oql = oql.replace(/\s+/g, ' ').trim();

        return oql;
      };

      /**
       * @function getOrder
       * @memberOf oqlBuilder
       *
       * @description
       *   Returns the order query basing on the criteria.
       *
       * @param {Object} criteria The criteria.
       *
       * @return {String} The order query.
       */
      this.getOrderBy = function(criteria) {
        if (!criteria || !criteria.orderBy || criteria.orderBy.length === 0) {
          return '';
        }

        var order = 'order by ';
        for(var key in criteria.orderBy) {
          order += key + ' ' + criteria.orderBy[key];
        }

        return order;
      };
    });
})();
