/**
 * Directive to create a stylish checkbox in list.
 */
angular.module('BackendApp.directives')
    .directive('checkbox', function ($compile, sharedVars) {
        return {
            restrict: 'AE',
            link: function ($scope, $element, $attrs) {
                $scope.shvs = sharedVars.get();
                $scope.index = $attrs.index;

                var select = '<div class="bootstrap-checkbox"'
                    + ' ng-class="{ checked: isSelected(index) }"'
                    + ' ng-click="select(index)">'
                        + '<i class="icon-ok" ng-if="isSelected(index)"></i>'
                    + '</div>';

                var selectAll = '<div class="bootstrap-checkbox"'
                    + ' ng-class="{ checked: areSelected() }"'
                    + ' ng-click="selectAll()">'
                        + '<i class="icon-ok" ng-if="areSelected()"></i>'
                    + '</div>';

                /**
                 * Returns if the given content is selected.
                 *
                 * @return boolean True if the given content is selected.
                 *                 Otherwise, return false.
                 */
                $scope.areSelected = function() {
                    var selected = sharedVars.get('selected') ?
                        sharedVars.get('selected').length : 0;
                    var contents = sharedVars.get('contents') ?
                        sharedVars.get('contents').length : 0;

                    return selected == contents;
                };

                /**
                 * Returns if the given content is selected.
                 *
                 * @param  int     index Checked item id.
                 * @return boolean       True if the given content is selected.
                 *                       Otherwise, return false.
                 */
                $scope.isSelected = function(index) {
                    index = parseInt(index);

                    // Load shared variables
                    var selected = sharedVars.get('selected');

                    for (var i = 0; i < selected.length; i++) {
                        if (selected[i] == index) {
                            return true;
                        }
                    };

                    return false;
                };

                /**
                 * Updates the selected items on click.
                 *
                 * @param int index Index of the selected item in the array of
                 *                  contents.
                 */
                $scope.select = function(index) {
                    updateSelected(index);
                };

                /**
                 * Selects all contents in the list.
                 */
                $scope.selectAll = function () {
                    var contents = sharedVars.get('contents');
                    var allSelected = !$scope.areSelected();

                    sharedVars.set('selected', []);

                    if (allSelected) {
                        for (var i = 0; i < contents.length; i++) {
                            updateSelected(contents[i].id);
                        };
                    }
                };

                /**
                 * Updates the selected items.
                 *
                 * @param int     index   Index of the selected item in the
                 *                        array of contents.
                 * @param boolean enabled Whether to select the item.
                 */
                function updateSelected(index) {
                    index = parseInt(index);

                    // Load shared variables
                    var selected = sharedVars.get('selected');
                    var inArray  = selected.indexOf(index);

                    if (inArray === -1) {
                        selected.push(index);
                    } else {
                        selected.splice(inArray, 1);
                    }

                    // Updated shared variable
                    sharedVars.set('selected', selected);
                }

                // Compile & replace
                var e;

                if ($attrs.selectAll) {
                    e = $compile(selectAll)($scope);
                } else {
                    e = $compile(select)($scope);
                }

                $element.replaceWith(e);
            }
        };
    });

