define(['modules/canvas', 'modules/element-factory', 'modules/globals'], function(Canvas, ElementFactory, globals) {
    
    var publicRun = function() {
        var canvas = new Canvas("canvas", 640, 480);
        
        // Creation of valid types are tested elsewhere in the test suite
        test('ElementFactory: Create Unknown Element', function() {
            equal(ElementFactory.createElement(canvas, "unknown-type"), false, "Test creating an unknown element type");
        });
    };
    
    return {
        run: publicRun
    };
});