define(['modules/element', 'modules/canvas', 'modules/element-factory', 'modules/globals'], function(Element, Canvas, ElementFactory, globals) {
    
    var publicRun = function() {
        var canvas = new Canvas("canvas", 640, 480);
        
        test('Element: Test Data Getters/Setters', function() {
            var element = new Element();
            equal(element.getData(), null, "Initial data should be null");
            
            element.setData({
                someAttribute: true,
                someOtherAttribute: false
            });
            // Objects are compared by reference, so their attributes must be compared.
            equal(element.getData().someAttribute, true, "Test setting data");
            equal(element.getData().someOtherAttribute, false, "Test setting data 2");
        });
        
        test('Element: Test HasMoved Getters/Setters', function() {
            var element = new Element();
            equal(element.getHasMoved(), false, "Initial value should be false");
            
            element.setHasMoved(true);
            equal(element.getHasMoved(), true, "Test value after setting");
        });
        
        test('Element: Test getBBox', function() {
            // Create an element with a graphic
            
            // Will call setGraphicByJson, draggable
            var element = ElementFactory.createElement(canvas, globals.ELETYPE.PROCESS.name, 0, 0);
            var bbox = {
                x:-25.037297307578548,
                y:-25, x2:25.037297307578548,
                y2:25,
                width:50.074594615157096,
                height:50
            };
            
            notEqual(element.getSet(), null, "Test that the graphic set exits");
            equal(element.getBBox().x, bbox.x, "Test bbox x");
            equal(element.getBBox().y, bbox.y, "Test bbox y");
            equal(element.getBBox().y2, bbox.y2, "Test bbox y2");
            equal(element.getBBox().width, bbox.width, "Test bbox width");
            equal(element.getBBox().height, bbox.height, "Test bbox height");
        });
        
        test('Element: Test Selection', function() {
            // Create an element with a graphic
            
            // Will call setGraphicByJson, draggable
            var element = ElementFactory.createElement(canvas, globals.ELETYPE.PROCESS.name, 0, 0);
            
            element.getSet().forEach(function(e) {
                equal(e.attr("fill-opacity"), 1.0, "Test fill opacity.");
            });
            
            // Cannot test for selection because the animation takes time, and
            // as far as I know, JavaScript does not have a blocking sleep function.
        });
        
        test('Element: Test getAttachPoints', function() {
            // Create an element with a graphic
            
            // Will call setGraphicByJson, draggable
            var element = ElementFactory.createElement(canvas, globals.ELETYPE.PROCESS.name, 0, 0);
            var points = [
                {x:-25.037297307578548, y:0}, 
                {x:0, y:-25}, 
                {x:25.037297307578548, y:0}, 
                {x:0, y:25}
            ];
            
            for (var i = 0; i < points.length; i++) {
                equal(element.getAttachPoints()[i].x, points[i].x, "Test bbox x[" + i + "]");
                equal(element.getAttachPoints()[i].y, points[i].y, "Test bbox y[" + i + "]");
            }
        });
        
        test('Element: Test Text Getters/Setters', function() {
            // Create an element with a graphic
            
            // Will call setGraphicByJson, draggable
            var element = ElementFactory.createElement(canvas, globals.ELETYPE.PROCESS.name, 0, 0);
        
            equal(element.getText(), "", "Test initial value.");
            
            element.setText("test text");
            equal(element.getText(), "test text", "Test setting text");
        });
        
        test('Element: Test Remove from Canvas', function() {
            var element = ElementFactory.createElement(canvas, globals.ELETYPE.PROCESS.name, 0, 0);
        
            element.remove();
            equal(element.getSet(), null, "Test getSet after remove");
            equal(element.getText(), "", "Test getText after remove");
        });
    };
    
    return {
        run: publicRun
    };
});