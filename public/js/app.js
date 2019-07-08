/**
 * AngularJS App
 *
 * @author Niek van den Bogaard
 */

var app = angular.module("app", [
    'ui.router',
    //'ui.bootstrap',
    //'ui.bootstrap.tooltip',
    'ngAnimate',
    'ngSanitize',
    'LocalStorageModule']);

app.config([
    '$stateProvider',
    '$urlRouterProvider',
    '$locationProvider',
    'localStorageServiceProvider',
    function($stateProvider, $urlRouterProvider, $locationProvider, localStorageServiceProvider) {

    // Enable html5Mode
    $locationProvider.html5Mode({
        enabled: true,
        requireBase: false
    });

    // LocalStorageService config
    localStorageServiceProvider
        .setPrefix('owasp')
        .setStorageCookie(0, '/')
        .setNotify(true, true);

    // Set a not found route
    $urlRouterProvider.otherwise('/');

    // Helper functions
    var title = function(title){ return 'Owasp \u00b7 '+title; };
    var view = function(name) { return 'views/'+name; };
    var workshopLoaded = false;
    var doehet = false;

    // Set the routes
    $stateProvider

    .state('workshop', {
        abstract: true,
        url: '/',
        templateUrl: view('workshop.html'),
        controller: 'WorkshopController'
    })

    // NEW ROUTE FOR ALL CASES (WE LOAD STUFF FROM THE DATABASE NOW!!!)
    .state('workshop.case', {
        url: ':url',
        params : { case: null, url: null },
        templateProvider: function($http, $rootScope, $state, OverlayService, CaseService, $stateParams) {

            $rootScope.pageTitle = 'Owasp';

            var active, url;

            // async service to recieve case information from DB
            return CaseService.promise().then(function() {

                // We loaded a case!
                if ($stateParams.case) {
                    CaseService.setActive($stateParams.case);
                    CaseService.firstTimeVisited();
                }

                // Show the overview/welkom page
                if (
                    CaseService.firstTimeVisiting() ||
                    ($stateParams.url == 'welkom') && $stateParams.case == null) {

                    // Show the correct URL
                    if ($stateParams.url !== 'welkom') {
                        $state.transitionTo('workshop.case', {case : null, url : 'welkom'});
                        return;
                    }

                    // Show the overview page
                    OverlayService.show('welkom');

                    // Set the page title
                    $rootScope.pageTitle = title('welkom');

                    // Get the active case!
                    active = CaseService.active();

                } else {

                    // Get the active case!
                    active = CaseService.active();

                    // The correct url for this route
                    url = active.name.toLowerCase().replace(/ /g, '-');

                    // Show the correct URL
                    if ($stateParams.url !== url) {
                        $state.transitionTo('workshop.case', {case : active.id, url : url});
                        return;
                    }

                    // Set the page title
                    $rootScope.pageTitle = title(active.name);

                    // Show the info for this case
                    if (active.status === null) {
                        OverlayService.show('info');
                        CaseService.opened(active.id);
                    }
                }

                // Request the template from the server
                return $http.get(view('case/'+active.templateUrl)).then(function(response) {
                    return response.data;
                });
            });
        },
        controllerProvider: function(CaseService) {
            return CaseService.active().controller;
        }
    })

}]);

// A filter to allow unsafe html
app.filter('trust_as_html', function($sce) {
    return function(value) {
        return $sce.trustAsHtml(value);
    };
});

app.filter('trim', function () {
    return function (value) {
        return value.trim();
    };
});

app.run(function($http, $rootScope, $state, CaseService) {

    $http.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

});