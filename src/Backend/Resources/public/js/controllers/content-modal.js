/**
 * Controller to handle modal actions.
 *
 * Note: Don't use array notation to inject dependencies as controller wouldn't
 *       recognize the rest of parameters.
 *
 * @param int    id       Selected item id.
 * @param int    index    Index of the selected content in contents array.
 * @param array  contents Array of contents.
 * @param string title     Selected item title.
 * @param array  selected Array of selected items.
 */
function ContentModalCtrl($http, $scope, $modalInstance, fosJsRouting,
    messenger, sharedVars, index, route
) {
    $scope.route = route;
    $scope.index = index;

    if (index != null) {
        $scope.id    = sharedVars.get('contents')[index].id;

        if (sharedVars.get('contents')[index].title) {
            $scope.title = sharedVars.get('contents')[index].title;
        } else if (sharedVars.get('contents')[index].name) {
            $scope.title = sharedVars.get('contents')[index].name;
        } else {
            $scope.title = sharedVars.get('contents')[index].id;
        }
    };

    $scope.contents = sharedVars.get('contents');
    $scope.selected = sharedVars.get('selected').length;

    /**
     * Closes the current modal.
     */
    $scope.close = function () {
        $modalInstance.dismiss('close');
    };

    /**
     * Deletes a content on confirmation.
     *
     * @param int    id    Item id.
     * @param int    index Index of the item in the array of contents.
     * @param string route Route title.
     */
    $scope.delete = function (id, index, route) {
        // Load shared variable
        var contents = sharedVars.get('contents');

        // Enable spinner
        $scope.deleting = 1;

        var url = fosJsRouting.generate(
            route,
            { contentType: sharedVars.get('contentType'), id: id }
        );

        $http.post(url).success(function(data) {
            if (data.errors.length == 0) {
                contents.splice(index, 1);
                sharedVars.set('total', sharedVars.get('total') - 1);
                $modalInstance.close();
            }

            // Disable spinner
            $scope.deleting = 0;
        }).error(function(data) {
            // Disable spinner
            $scope.deleting = 0;
        });

        // Updated shared variable
        sharedVars.set('contents', contents);
    };

    /**
     * Deletes selected contents on confirmation.
     *
     * @param string route Route title.
     */
    $scope.deleteSelected = function (route) {
        // Load shared variable
        var contents = sharedVars.get('contents');
        var selected = sharedVars.get('selected');

        var ids = [];

        for (var i = 0; i < selected.length; i++) {
            ids.push(contents[selected[i]].id);
        };

        // Enable spinner
        $scope.deleting = 1;

        var url = fosJsRouting.generate(
            route,
            { contentType: sharedVars.get('contentType') }
        );
        $http.post(url, { ids: ids }).success(function(response) {
            // Remove only successfully deleted contents
            for (var i = 0; i < response.success.length; i++) {
                var j = 0;
                while (j < contents.length
                    && contents[j].id != response.success[i].id
                ) {
                    j++;
                }

                if (j < contents.length) {
                    contents.splice(j, 1);
                    sharedVars.set('total', sharedVars.get('total') - 1);
                }
            };

            for (var i = 0; i < response.success.length; i++) {
                var params = {
                    id:      new Date().getTime() + '_' + response.success[i].id,
                    message: response.success[i].message,
                    type:    'success'
                };

                messenger.post(params);
            };

            for (var i = 0; i < response.errors.length; i++) {
                var params = {
                    id:      new Date().getTime() + '_' + response.errors[i].id,
                    message: response.errors[i].message,
                    type:    'error'
                };

                messenger.post(params);
            };

            $modalInstance.close();

            // Disable spinner
            $scope.deleting = 0;
        }).error(function() {
            // Disable spinner
            $scope.deleting = 0;
        });

        // Updated shared variable
        sharedVars.set('contents', contents);
    };

    /**
     * Load the value of shared variables object in scope when it changes.
     *
     * @param  Event  event Event object.
     * @param  Object vars  Shared variables object.
     */
    $scope.$on('SharedVarsChanged', function(event, vars) {
        $scope.shvs = vars;
    });
}

// Register ModalCtrl function as AngularJS controller
angular.module('BackendApp.controllers')
    .controller('ContentModalCtrl', ContentModalCtrl);
