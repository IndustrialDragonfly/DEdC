define(['modules/connector'], function(Connector) {
    
    var publicRun = function() {
        asyncTest("Connector: Valid GET", function() {
            var onSuccess = function(response) {
                notEqual(response, null, "Test response not null");
                var expectedData = {
                    id:"0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx_id", 
                    label:"New_DFD!", 
                    type:"DataFlowDiagram", 
                    originator:"The Eugene", 
                    genericType:"Diagram", 
                    subDFDNode:"", 
                    nodes:[
                        {
                            id:"cabumEiAZdExZKbHDaumNT9KEoN0lwUJZwgyISIDre4x_id", 
                            type:"ExternalInteractor", 
                            label:"Some Interactor", 
                            x:"100", 
                            y:"100"
                        }, 
                        {
                            id:"mmWyh0gmygRejKr2meuRGSfLAl9oceUAhrG7foCquFox_id",
                            type:"DataStore", 
                            label:"Some Store", 
                            x:"200", 
                            y:"100"
                        }, 
                        {
                            id:"TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx_id",
                            type:"Process", 
                            label:"Some Proc", 
                            x:"100", 
                            y:"200"
                        }
                    ], 
                    links:[
                        {
                            id:"hClaOolenANwol8HZhcIK7ulTfDiEwFtRM2CMo9Ppxgx_id", 
                            type:"DataFlow", 
                            label:"Some Dataflow", 
                            origin_id:"TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx_id", 
                            dest_id:"nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx_id", 
                            x:"100", 
                            y:"200"
                        }
                    ], 
                    subDFDNodes:[
                        {
                            id:"nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx_id",
                            type:"Multiprocess",
                            label:"Some Multiprocess", 
                            x:"200", 
                            y:"200"
                        }
                    ]
                };
                
                ok(JSON.stringify(expectedData) === JSON.stringify(response.getData()), "Test if data was as expected");
                equal(response.getStatus(), "", "Response Status should be as expected.");
                equal(response.getError(), "", "Should be no error message");
                start();
            };
            
            var onFail = function(response) {
                ok(false, "Request should not fail");
                start();
            };
            
            Connector.get("Controller.php/0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx_id", onSuccess, onFail);
        });
        
        asyncTest("Connector: Invalid GET", function() {
            var onSuccess = function(response) {
                ok(false, "Request should not be successful");
                start();
            };
            
            var onFail = function(response) {
                ok(true, "Request should fail");
                notEqual(response, null, "Response should not be null");
                
                equal(response.getStatus(), "error", "Test status text on failure");
                equal(response.getError(), "GET Controller.php/some_id_thats_not_an_id 500 (Internal Server Error)", "Test error message on failure");
                start();
            };
            
            Connector.get("Controller.php/some_id_thats_not_an_id", onSuccess, onFail);
        });
        
        asyncTest("Connector: Valid PUT", function() {
            var onSuccess = function(response) {
                ok(true, "Request should be successful");
                start();
            };
            
            var onFail = function(response) {
                console.log(response.getError());
                ok(false, "Request should not fail");
                start();
            };
            
            var data = {
                id:"0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx_id", 
                label:"New_DFD!", 
                type:"DataFlowDiagram", 
                originator:"The Eugene", 
                genericType:"Diagram", 
                subDFDNode:"", 
                nodes:[
                    {
                        id:"cabumEiAZdExZKbHDaumNT9KEoN0lwUJZwgyISIDre4x_id", 
                        type:"ExternalInteractor", 
                        label:"Some Interactor", 
                        x:"100", 
                        y:"100"
                    }, 
                    {
                        id:"mmWyh0gmygRejKr2meuRGSfLAl9oceUAhrG7foCquFox_id",
                        type:"DataStore", 
                        label:"Some Store", 
                        x:"200", 
                        y:"100"
                    }, 
                    {
                        id:"TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx_id",
                        type:"Process", 
                        label:"Some Proc", 
                        x:"100", 
                        y:"200"
                    }
                ], 
                links:[
                    {
                        id:"hClaOolenANwol8HZhcIK7ulTfDiEwFtRM2CMo9Ppxgx_id", 
                        type:"DataFlow", 
                        label:"Some Dataflow", 
                        origin_id:"TgRGVyTIh0srk2zw7OndjUx9AcyNx4AymEWiOMnDMPwx_id", 
                        dest_id:"nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx_id", 
                        x:"100", 
                        y:"200"
                    }
                ], 
                subDFDNodes:[
                    {
                        id:"nDnIae2poYlZu6x87lYuoSg7XYZ8jmxpx6xthnrp3qcx_id",
                        type:"Multiprocess",
                        label:"Some Multiprocess", 
                        x:"200", 
                        y:"200"
                    }
                ]
             };
             
            Connector.put("Controller.php/0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx_id", JSON.stringify(data), onSuccess, onFail);
        });
    };
    
    return {
        run: publicRun
    };
});