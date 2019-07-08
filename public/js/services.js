/**
 * AngularJS factories
 *
 * @author Niek van den Bogaard
 */

var app = angular.module("app");

/**
 * Service die alle cases regeld
 */
app.factory("CaseService", function($http, localStorageService) {

    var data = [];
    var callback;

    return {
        // Load the data
        promise: function() {
            return $http.get('app/cases').then(function(response) {
                data = response.data;

                if (localStorageService.get('activeCase') === null)
                    localStorageService.set('activeCase', 0);

                return data;
            });
        },
        // Geef alle data terug
        data : function () {
            return data;
        },
        // Get the total score
        score : function() {
            var score = 0;

            angular.forEach(data, function(value, key) {
                score += value.score ? parseInt(value.score) : 0;
            });

            return score;
        },
        // Set the active case
        setActive : function(id) {

            var k = null;

            angular.forEach(data, function(value, key)
            {
                if (value.id == id)
                {
                    k = key;
                }
            });

            localStorageService.set('activeCase', k);

            if (callback) callback();
        },
        // Get the active case
        active : function() {
            return data[localStorageService.get('activeCase')];
        },
        // Get the first case
        first : function() {
            return data[0];
        },
        // Check if we are first time visiting
        firstTimeVisiting : function()
        {
            if (localStorageService.get('visited'))
                return false;

            var first = true;
            angular.forEach(data, function(value, key)
            {
                if (value.status !== null)
                    first = false;
            });
            return first;
        },
        // Call this function when we have visited a case
        firstTimeVisited : function() {
            localStorageService.set('visited', true);
        },
        // Set a case status to 'done'
        done : function(id, score) {
            data[localStorageService.get('activeCase')].status = 'done';
            data[localStorageService.get('activeCase')].score = score;

            if (callback) callback();
        },
        // Set a case status to 'opened'
        opened : function(id) {

            console.log("OPENED");
            console.log(data[localStorageService.get('activeCase')].status);
            if (data[localStorageService.get('activeCase')].status === null)
            {
                data[localStorageService.get('activeCase')].status = 'opened';

                $http.post('app/case/status', {status:'opened', id:id}).then(function(response) {
                    if (callback) callback();
                });
            }
        },
        // Register a callback function that gets called when a change occurred
        register : function(func) {
            callback = func;
            callback();
        }
    };

});

/**
 * This Service handles the overlays (info, help, overview)
 */
app.factory('OverlayService', function() {

    var callback = null;

    var pages = {

        info : {
            color : 'rgb(180, 112, 226)',
            template : 'views/overlay/info.html',
        },
        help : {
            color : 'rgb(226, 194, 112)',
            template : 'views/overlay/help.html'
        },
        overview : {
            color : 'rgb(82, 88, 100)',
            template : 'views/overlay/overview.html'
        },
        prevention : {
            color : 'rgb(226, 194, 112)',
            template : 'views/overlay/source.html'
        }
    }

    var active;

    return {

        page : function(attr) {
            return (active in pages && attr in pages[active]) ? pages[active][attr] : null;
        },
        isVisible : function() {
            return (active in pages);
        },
        show : function(page) {
            active = page;
            if (callback) callback();
        },
        hide : function() {
            active = null;
            if (callback) callback();
        },
        toggle : function(page) {
            active = active != page ? page : null;
            if (callback) callback();
        },
        // Register a callback for when the overlay changes visibility
        register : function(func) {
            callback = func;
            callback();
        }
    }

});

/**
 * This service handles the popup messages (score)
 */
app.factory('PopupService', function() {

    var callback;
    var score = 0;
    var title;

    return {

        score : function() {
            return score;
        },
        title : function() {
            return title;
        },
        show : function(message, points) {
            score = points;
            title = message;
            if (callback) callback();
        },
        // Register a callback for when the the show() method is called
        register : function(func) {
            callback = func;
        }
    }
});

/**
 * Service for the sidebar
 */
app.factory('SidebarService', function(localStorageService) {

    // Get the visibility from localStorage
    var visible = ! ( localStorageService.get('sidebar.visible') == '0' );

    // Helper function to store the visibility to localStorage
    var storeVisibility = function() {
        localStorageService.set('sidebar.visible', visible ? '1' : '0');
    }

    return {

        isVisible : function() {
            return visible;
        },
        toggle : function() {
            visible = !visible;
            storeVisibility();
        },
        show : function() {
            visible = true;
            storeVisibility();
        },
        hide : function() {
            visible = false;
            storeVisibility();
        }
    }
});