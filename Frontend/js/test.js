requirejs.config({
    // Load modules from Frontend/js by default
    baseUrl: 'Frontend/js',
    // Prevent caching by inserting the current time as a query string
    urlArgs: "bust=" + (new Date()).getTime(),

    // Map shorthand names to file paths relative to baseUrl
    paths: {
        'jquery': 'lib/jquery-1.10.2',
        'jquery-ui': 'lib/jquery-ui-1.10.3',
        'jquery-layout': 'lib/jquery-layout-1.3.0-rc30.79',
        'jquery-layout-resizeTabLayout': 'lib/jquery-layout-resizeTabLayout-1.3',
        'raphael': 'lib/raphael-2.1.2',
        'QUnit': 'lib/qunit-1.12.0'
    },

    // Configure dependencies for non-AMD scripts for jQuery plugins
    shim: {
        'jquery-ui': ['jquery'],
        'jquery-layout': ['jquery'],
        'jquery-layout-resizeTabLayout': ['jquery-layout'],
        'QUnit': {
            exports: 'QUnit',
            init: function () {
                QUnit.config.autoload = false;
                QUnit.config.autostart = false;
            }
        }
    }
});

// Start the main app logic.
requirejs(['QUnit', 'modules/tests/responseTest', 'modules/tests/elementTest', 'modules/tests/dataflowTest', 'modules/tests/elementFactoryTest', 'modules/tests/connectorTest'], function (QUnit, responseTest, elementTest, dataflowTest, elementFactoryTest, connectorTest) {
    responseTest.run();
    elementTest.run();
    dataflowTest.run();
    elementFactoryTest.run();
    connectorTest.run();

    QUnit.load();
    QUnit.start();
});