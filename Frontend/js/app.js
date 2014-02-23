requirejs.config({
    // Load modules from Frontend/js/lib by default
    baseUrl: 'Frontend/js',

    // Map shorthand names to file paths relative to baseUrl
    paths: {
        'jquery': 'lib/jquery-1.10.2',
        'jquery-ui': 'lib/jquery-ui-1.10.3',
        'jquery-layout': 'lib/jquery-layout-1.3.0-rc30.79',
        'jquery-layout-resizeTabLayout': 'lib/jquery-layout-resizeTabLayout-1.3',
        'raphael': 'lib/raphael-2.1.2'
    },

    // Configure dependencies for non-AMD scripts for jQuery plugins
    shim: {
        'jquery-ui': ['jquery'],
        'jquery-layout': ['jquery'],
        'jquery-layout-resizeTabLayout': ['jquery-layout'],
    }
});

// Start the main app logic.
requirejs(['modules/dedc'], function (DEdC) {
    $(document).ready(function () {
        DEdC.setupUi("#content", "#sidebar1", "#users", "#tabsContainer", "#process", "#multiprocess", "#datastore", "#extinteractor", "#connect", "#delete", "#load", "#newTab", "#save");
    });
});