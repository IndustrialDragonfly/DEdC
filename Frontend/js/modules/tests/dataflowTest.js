define(['modules/canvas', 'modules/element-factory', 'modules/globals'], function(Canvas, ElementFactory, globals) {
    
    var publicRun = function() {
        var canvas = new Canvas("canvas", 640, 480),
            element = ElementFactory.createElement(canvas, globals.ELETYPE.PROCESS.name, 0, 0),
            element2 = ElementFactory.createElement(canvas, globals.ELETYPE.PROCESS.name, 0, 50),
            dataflow = canvas.addDataflow(element, element2);
              
        test('Datalfow: Create', function() {
            notEqual(dataflow, null, "Dataflow not null");
        });
        
        test('Dataflow: Test Data Getters/Setters', function() {
            equal(dataflow.getData(), null, "Initial data should be null");
            
            dataflow.setData({
                someAttribute: true,
                someOtherAttribute: false
            });
            // Objects are compared by reference, so their attributes must be compared.
            equal(dataflow.getData().someAttribute, true, "Test setting data");
            equal(dataflow.getData().someOtherAttribute, false, "Test setting data 2");
        });
        
        test('Dataflow: Test Text Getters/Setters', function() {            
            // Will call setGraphicByJson, draggable        
            equal(dataflow.getText(), "", "Test initial value.");
            
            dataflow.setText("test text");
            equal(dataflow.getText(), "test text", "Test setting text");
        });
    };
    
    return {
        run: publicRun
    };
});