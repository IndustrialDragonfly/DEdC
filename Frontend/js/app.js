requirejs.config({
    // Load modules from Frontend/js/lib by default
    baseUrl: 'Frontend/js/lib',

    // Map shorthand names to file paths relative to baseUrl
    paths: {
        'app': '../app',
        'jquery': 'jquery-1.10.2',
        'jquery-ui': 'jquery-ui-1.10.3',
        'jquery-layout': 'jquery-layout-1.3.0-rc30.79',
        'jquery-layout-resizeTabLayout': 'jquery-layout-resizeTabLayout-1.3',
        'raphael': 'raphael-2.1.2',
        'canvas': '../canvas'
    },

    // Configure dependencies for non-AMD scripts
    shim: {
        'jquery-ui': ['jquery'],
        'jquery-layout': ['jquery-ui'],
        'jquery-layout-resizeTabLayout': ['jquery-layout'],
        'canvas': ['jquery-layout-resizeTabLayout']
    }
});

// Start the main app logic.
requirejs(['jquery', 'jquery-ui', 'jquery-layout', 'jquery-layout-resizeTabLayout', 'raphael', 'canvas'], function () {
    $(document).ready(function () {
        DEdC.setupUi("#content", "#sidebar1", "#users", "#tabsContainer", "#process", "#multiprocess", "#datastore", "#extinteractor", "#connect", "#delete", "#load", "#newTab");
    });
});