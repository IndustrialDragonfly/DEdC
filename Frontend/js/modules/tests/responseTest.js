define(['modules/response'], function(Response) {
    
    var publicRun = function() {
        test('Response: Test Status Getters/Setters', function() {
            var response = new Response();
            equal(response.getStatus(), "", "Initial status should be empty string");
            
            response.setStatus("test status");
            equal(response.getStatus(), "test status", "Test setting a status");
        });
        
        test('Response: Test Data Getters/Setters', function() {
            var response = new Response();
            equal(response.getData(), null, "Initial data should be null");
            
            response.setData({
                someAttribute: true,
                someOtherAttribute: false
            });
            // Objects are compared by reference, so their attributes must be compared.
            equal(response.getData().someAttribute, true, "Test setting data");
            equal(response.getData().someOtherAttribute, false, "Test setting data 2");
        });
        
        test('Response: Test Error Getters/Setters', function() {
            var response = new Response();
            equal(response.getError(), "", "Initial error should be empty string");
            
            response.setError("test error");
            equal(response.getError(), "test error", "Test setting a status");
        });
    };
    
    return {
        run: publicRun
    };
});