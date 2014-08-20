/**
 * Service to implement common actions related to item.
 *
 * @param Object $http        Http service
 * @param Object $location    Location service
 * @param Object $modal       Modal service
 * @param Object fosJsRouting Onm routing service.
 *
 * @return Object The item service.
 */
angular.module('onm.item', []).factory('itemService', function ($http, $location, $modal, fosJsRouting) {
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
        var url = fosJsRouting.generate(route);
        $location.path(url);
    }

    /**
     * Opens a moda to confirm action.
     *
     * @param string type The item type.
     * @param Object item The item.
     *
     * @return Object The modal.
     */
    itemService.confirm = function (type, item) {
        return $modal.open({
            templateUrl: '/ws/template/ClientBundle:Modal:plugin.html.twig',
            controller:  'ItemModalCtrl',
            resolve: {
                item: function() {
                    return item;
                },
                type: function() {
                    return type;
                }
            }
        });
    }

    /**
     * Deletes a plugin given its id.
     *
     * @param string  route The route name.
     * @param integer id    The item id.
     *
     * @return Object The response object.
     */
    itemService.delete = function (route, id) {
        var url = fosJsRouting.generate(route, { id: id });

        return $http.post(url).success(function (response) {
            return response;
        });
    };

    /**
     * Checks if the given name is available.
     *
     * @param string route The route name.
     * @param string name  The name to check.
     *
     * @return Object The response object.
     */
    itemService.isAvailable = function(route, name) {
        var url = fosJsRouting.generate(route);
        var data = { name: name };

        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    /**
     * Returns a list of items.
     *
     * @param string route    The route name.
     * @param object criteria The parameters to search by.
     *
     * @return Object The response object.
     */
    itemService.list = function(route, criteria) {
        var url = fosJsRouting.generate(route);

        return $http.post(url, criteria).success(function (response) {
            return response;
        });
    };

    /**
     * Returns the template parameters to create a new item.
     *
     * @param string  route The route name.
     *
     * @return Object The response object.
     */
    itemService.new = function (route) {
        var url = fosJsRouting.generate(route);

        return $http.post(url).success(function (response) {
            return response;
        });
    };

    /**
     * Returns an item given its id.
     *
     * @param string  route The route name.
     * @param integer id The item id.
     *
     * @return Object The response object.
     */
    itemService.show = function (route, id) {
        var url = fosJsRouting.generate(route, { id: id });

        return $http.post(url).success(function (response) {
            return response;
        });
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
        var url = fosJsRouting.generate(route);

        return $http.post(url, data).success(function (response) {
            return response;
        });
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
        var url = fosJsRouting.generate(route, { id: id });

        return $http.post(url, data).success(function (response) {
            return response;
        });
    };

    return itemService;
})
