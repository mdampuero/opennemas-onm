(function() {
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
     * @name  oqlEncoder
     *
     * @description
     *   The `oqlEncoder` service creates OQL queries basing on criteria
     *   objects.
     */
    .service('oqlEncoder', function() {
      /**
       * @memberOf oqlEncoder
       *
       * @description
       *  The encoder configuration.
       *
       * @type {Object}
       */
      this.config = {
        defaults: {
          epp:  25,
          page: 1
        },
        placeholder: {
          oql:       '[filter] [orderBy] [limit] [offset]',
          condition: '[key]="[value]"'
        },
      };

      /**
       * @function configure
       * @memberOf oqlEncoder
       *
       * @description
       *   Configures the encoder.
       *
       * @param {Object} config The encoder configuration.
       */
      this.configure = function(config) {
        this.config = angular.merge({}, this.config, config);
      };

      /**
       * @function getCondition
       * @memberOf oqlEncoder
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
       * @memberOf oqlEncoder
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
       * @memberOf oqlEncoder
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
       * @memberOf oqlEncoder
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
       * @memberOf oqlEncoder
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
       * @memberOf oqlEncoder
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

        oql = oql.replace(/\s+/g, ' ').replace(/^\s*|\s*$/g, '');

        return oql;
      };

      /**
       * @function getOrder
       * @memberOf oqlEncoder
       *
       * @description
       *   Returns the order query basing on the criteria.
       *
       * @param {Object} criteria The criteria.
       *
       * @return {String} The order query.
       */
      this.getOrderBy = function(criteria) {
        if (!criteria || !criteria.orderBy ||
            Object.keys(criteria.orderBy).length === 0) {
          return '';
        }

        var order = 'order by ';

        for (var key in criteria.orderBy) {
          order += key + ' ' + criteria.orderBy[key] + ', ';
        }

        return order.replace(/, $/, '');
      };
    })

    /**
     * @ngdoc service
     * @name  oqlDecoder
     *
     * @description
     *   The `oqlDecoder` service creates criteria objects basing on OQL
     *   queries.
     */
    .service('oqlDecoder', function() {
      /**
       * @memberOf oqlDecoder
       *
       * @description
       *  The decoder configuration.
       *
       * @type {Object}
       */
      this.config = {
        ignore: [],
        map:    {},
        match:  {},
        defaults: {
          epp:     25,
          orderBy: {},
          page:    1,
        },
      };

      /**
       * @memberOf oqlDecoder
       *
       * @description
       *  The list of supported operators.
       *
       * @type {Regex}
       */
      this.operators = />=|<=|!=| !in |!~| !regexp |=|>| in | is | !is |<|~| regexp /;

      /**
       * @function configure
       * @memberOf oqlDecoder
       *
       * @description
       *   Configures the decoder.
       *
       * @param {Object} config The decoder configuration.
       */
      this.configure = function(config) {
        this.config = angular.merge({}, this.config, config);
      };

      /**
       * @function getCriteria
       * @memberOf oqlDecoder
       *
       * @description
       *   description
       *
       * @param {type} name description
       *
       * @return {type} description
       */
      this.decode = function(oql) {
        var criteria = {};

        if (!oql || oql === '') {
          return criteria;
        }

        this.oql = oql;

        criteria.epp  = this.decodeLimit();
        criteria.page = this.decodeOffset(criteria.epp);

        var orderBy = this.decodeOrderBy();

        if (orderBy) {
          criteria.orderBy = orderBy;
        }

        return angular.extend(criteria, this.decodeCriteria());
      };

      /**
       * @function decodeCriteria
       * @memberOf oqlDecoder
       *
       * @description
       *   Decodes all filtering conditions from an OQL query.
       *
       * @return {Object} The filtering conditions.
       */
      this.decodeCriteria = function() {
        this.oql = this.oql.replace(/^\s+|\s+$|\(|\)/g, '');

        if (this.oql === '') {
          return {};
        }

        var conditions = this.oql.split(/ and | or /);
        var criteria   = {};

        for (var i = 0; i < conditions.length; i++) {
          var tokens = conditions[i].split(this.operators);
          var field  = tokens[0].replace(/^\s+|\s+$/g, '');
          var value  = tokens[1].replace(/^\s+|\s+$/g, '').replace(/"/g, '');

          if (this.config.ignore.indexOf(field) === -1) {
            if (this.config.map[field]) {
              field = this.config.map[field];
            }

            if (this.config.match[field] &&
                value.match(this.config.match[field])) {
              value = value.match(this.config.match[field])[1];
            }

            if (parseInt(value) > 0) {
              value  = parseInt(value);
            }

            criteria[field] = value;
          }
        }

        return criteria;
      };

      /**
       * @function decodeLimit
       * @memberOf oqlDecoder
       *
       * @description
       *   Decodes the limit condition from the OQL query.
       *
       * @return {Integer} The number of items per page.
       */
      this.decodeLimit = function() {
        var pattern = /limit\s+\d+/;

        if (!pattern.test(this.oql)) {
          return this.config.defaults.epp;
        }

        var epp  = this.oql.match(pattern)[0];

        this.oql = this.oql.replace(pattern, '');

        return parseInt(epp.replace(/limit\s+/, ''));
      };

      /**
       * @function decodeOffset
       * @memberOf oqlDecoder
       *
       * @description
       *   Decodes the offset condition from the OQL query.
       *
       * @param {Integer} epp The number of items per page.
       *
       * @return {Integer} The page number.
       */
      this.decodeOffset = function(epp) {
        var pattern = /offset\s+\d+/;

        if (!pattern.test(this.oql)) {
          return this.config.defaults.page;
        }

        var page = this.oql.match(pattern)[0];

        this.oql = this.oql.replace(pattern, '');

        return Math.ceil(parseInt(page.replace(/offset\s+/, '')) / epp) + 1;
      };

      /**
       * @function decodeOrderBy
       * @memberOf oqlDecoder
       *
       * @description
       *   Decodes the order by condition from an OQL query.
       *
       * @return {Object} The object with order by conditions as
       *                  { field: 'asc'|'desc' }.
       */
      this.decodeOrderBy = function() {
        var pattern = /order by\s+(\w+\s+(asc|desc)(\s*,\s*)?)+/;

        if (!pattern.test(this.oql)) {
          return null;
        }

        var conditions = this.oql.match(pattern)[0];
        var orderBy    = {};

        conditions = conditions.replace(/order by\s+/, '').split(/\s*,\s*/);

        for (var i = 0; i < conditions.length; i++) {
          var condition = conditions[i].split(/\s+/);

          orderBy[condition[0]] = condition[1];
        }

        this.oql = this.oql.replace(pattern, '');

        return orderBy;
      };
    });
})();
