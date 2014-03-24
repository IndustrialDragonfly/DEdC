define(["modules/globals", "modules/canvas", "modules/element-factory", "modules/connector", "jquery", "jquery-ui", "jquery-layout", "jquery-layout-resizeTabLayout"], function(globals, Canvas, ElementFactory, Connector, $) {
        var content,
            sidebar,
            users,
            tabContainer,
            process,
            multiprocess,
            datastore,
            extinteractor,
            connect,
            deleteButton,
            load,
            newTab,
            saveButton,
            tabs, // jQuery tab widget
            canvases = []; // Array of all open canvases
        
        /**
         * The the canvas object from the currently selected tab
         */
        var getCurrentCanvas = function () {
            var id = tabs.tabs('option', 'active');
            return canvases[id];
        };

        /**
         * Setup the UI for the browser
         * @param {string} contentText - Center DOM element - tabs
         * @param {string} sidebarText - Sidebar DOM element - accordion
         * @param {string} usersText - Users DOM element - accordion
         * @param {string} tabContainerText - DOM element for the individual tabs
         * @param {string} processText - Process DOM element to be made draggable
         * @param {string} multiprocessText - Multiprocess DOM element to be made draggable
         * @param {string} datastoreText - Datastore DOM element to be made draggable
         * @param {string} extinteractorText - ExtInteractor DOM element to be made draggable
         * @param {string} connectText - Dataflow creation DOM button
         * @param {string} deleteButtonText - Delete element DOM button
         * @param {string} loadText - Load DOM button
         * @param {string} newTabText - New Tab DOM button
         */
        var publicSetupUi = function (
            contentText, sidebarText, usersText, tabContainerText, processText, multiprocessText, datastoreText, extinteractorText, connectText, deleteButtonText, loadText, newTabText, saveButtonText) {
            content = contentText;
            sidebar = sidebarText;
            users = usersText;
            tabContainer = tabContainerText;
            process = processText;
            multiprocess = multiprocessText;
            datastore = datastoreText;
            extinteractor = extinteractorText;
            connect = connectText;
            deleteButton = deleteButtonText;
            load = loadText;
            newTab = newTabText;
            saveButton = saveButtonText;

            /**
             * Resize the currently selected canvas
             */
            var resizeCanvas = function () {
                var width = $(tabContainer).width();
                var height = $(tabContainer).height();

                // Get the current active tab
                var id = tabs.tabs('option', 'active');
                canvases[id].setSize(width, height);
            };

            // Create jQuery tabs widget
            tabs = $(content).tabs();

            // Create the UILayout layout
            // Needs to be created after tabs, but before accordions
            myLayout = $('body').layout({
                west__size: 200,
                west__onresize: $.layout.callbacks.resizePaneAccordions,
                center__onresize: resizeCanvas
            });

            // Create the sidebar accordion
            $(sidebar).accordion({
                heightStyle: "fill",
                collapsible: true
            });

            // Create the users accordion
            $(users).accordion({
                heightStyle: "content",
                collapsible: true
            });

            // Setup drag and drop
            var draggableHelper = function (event, ui) {
                return $(this).clone().appendTo('body').css('zIndex', 5).show();
            };

            // Make the elements in the toolbox draggable
            $(process).draggable({
                helper: draggableHelper
            }).data("type", globals.ELETYPE.PROCESS);

            $(multiprocess).draggable({
                helper: draggableHelper
            }).data("type", globals.ELETYPE.MULTIPROCESS);

            $(datastore).draggable({
                helper: draggableHelper
            }).data("type", globals.ELETYPE.DATASTORE);

            $(extinteractor).draggable({
                helper: draggableHelper
            }).data("type", globals.ELETYPE.EXTINTERACTOR);

            // Setup toolbar
            // Connect Dataflow button
            $(connect).button().click(function () {
                var c = getCurrentCanvas();
                if (c) {
                    c.addDataflowFromSelection();
                } else {
                    console.log("No tab currently selected.");
                }
            });

            // Delete Element button
            $(deleteButton).button().click(function () {
                var c = getCurrentCanvas();
                if (c) {
                                       
                    c.getSelection().forEach(function (entry) {
                        if (entry.getData() && entry.getData().id) {
                            // Element exists in the backend because it has an id
                            var onSuccess = function(response) {
                                c.removeElementById(entry.getData().id);
                            };
                            var onFail = function(response) {
                                console.log("Removing element failed. " + response.getError());
                            };
                            
                            Connector.delete("Controller.php/" + entry.getData().id, onSuccess, onFail);
                        } else {
                            // Element did not exist in the backend
                            c.removeElement(entry);
                        }
                    });
                } else {
                    console.log("No tab currently selected.");
                }
            });

            // Load DFD button
            $(load).button().click(function () {
                // Controller.php is required until the rewrite rules work correctly
                //getDfd("Controller.php/ljGmxv7q3E5E07bbXYjpNpfiM3wr8DeyWo5EZFseujEx");
                $("#dialog-modal").dialog("open");
            });
            
            $("#dialog-modal").dialog({
                autoOpen: false,
                modal: true,
                buttons: {
                    "Open DFDs": function () {
                        // Open button
                        // Go through each DOM element with the class ".ui-selected"
                        $(".ui-selected", this).each(function() {
                            // Get the id of the selected element
                            getDfd("Controller.php/" + this.id);
                        });
                        
                        $(this).dialog("close");
                    },
                    "Cancel": function () {
                        // Cancel button
                        $(this).dialog("close");
                    }
                },
                close: function () {
                    // Clear out the selections
                    $("#selectable").empty();
                },
                open: function () {
                    // On opening, fill the selection
                    var onSuccess = function (response) {
                        // Put the list of DFDs in the selectable
                        response.getData().list.forEach(function (entry) {
                            var itemString = "<li id=\"#{id}\" class=\"ui-widget-content\">#{label}</li>";
                            itemString = itemString
                                    .replace(/#\{id\}/g, entry.id)
                                    .replace(/#\{label\}/g, entry.label);
                            $("#selectable").prepend(itemString);
                        });
                        
                        // Create the selectable
                        $("#selectable").selectable();
                    }
                    
                    var onFail = function (response) {
                        console.log("Error getting DataFlowDiagrams. " + response.getError());
                    }
                    Connector.get("Controller.php/DataFlowDiagram", onSuccess, onFail, false);
                }
            });
            
            // Show the login dialog
            $("#login-dialog").dialog({
                modal: true,
                closeOnEscape: false,
                buttons: {
                    "Login": function () {
                        var onSuccess = function (response) {
                            $("#login-dialog").dialog("close");
                        };
                        
                        var onFail = function (response) {
                            console.log(response.getError());
                            $("#login-dialog").addClass("ui-state-error");
                        };
                        
                        Connector.setCredentials($("#organization").val(), $("#username").val(), $("#password").val())
                        Connector.get("Controller.php/DataFlowDiagram", onSuccess, onFail);
                    },
                },
            });

            // New tab button
            $(newTab).button().click(function () {
                createNewTab();
            });
            
            $(saveButton).button().click(function () {
                saveCurrentDfd(getCurrentCanvas());
            });
        };

        /**
         * Create a new DFD view tab
         * @param {string} name of the tab (optional)
         * @returns {canvas} Canvas that was created in the tab
         */
        var createNewTab = function (name) {
            var tabTemplate = "<li><a href='#{href}'>#{label}</a></li>",
                // Template for the tabs
                label = "Tab" + canvases.length,
                // Name of the tab
                id = "tab" + canvases.length;

            // Support custom tab names
            if (name) {
                label = name;
            }

            // Id of the tabs
            var li = $(tabTemplate.replace(/#\{href\}/g, "#" + id).replace(/#\{label\}/g, label)); // List item HTML

            // Add the new tab to the tab list
            tabs.find(".ui-tabs-nav").append(li);

            // Add the body of the tab to the container
            $(tabContainer).prepend("<div id='" + id + "'></div>");

            // Create the canvas
            var c = new Canvas(id, 640, 480);
            canvases.push(c);
            c.setBackground('#A8A8A8');

            // Update the tab view
            $(content).tabs("refresh");

            // Set the initial size of the canvas
            // Cannot call resizeCanvas because it hasn't been selected yet
            var width = $(tabContainer).width();
            var height = $(tabContainer).height();
            c.setSize(width, height);

            // Setup drop for the new tab
            $("#" + id).droppable({
                drop: function (event, ui) {
                    // Executed when something is dropped onto the tab
                    var posx = event.pageX - $(tabContainer).offset().left,
                        posy = event.pageY - $(tabContainer).offset().top;

                    // Check the type of the dropped element and an element to the canvas
                    if ($(ui.draggable).data("type") === globals.ELETYPE.PROCESS) { 
                        ElementFactory.createElement(getCurrentCanvas(), globals.ELETYPE.PROCESS.name, posx, posy); 
                    } else if ($(ui.draggable).data("type") === globals.ELETYPE.MULTIPROCESS) {
                        ElementFactory.createElement(getCurrentCanvas(), globals.ELETYPE.MULTIPROCESS.name, posx, posy); 
                    } else if ($(ui.draggable).data("type") === globals.ELETYPE.DATASTORE) {
                        ElementFactory.createElement(getCurrentCanvas(), globals.ELETYPE.DATASTORE.name, posx, posy); 
                    } else if ($(ui.draggable).data("type") === globals.ELETYPE.EXTINTERACTOR) {
                        ElementFactory.createElement(getCurrentCanvas(), globals.ELETYPE.EXTINTERACTOR.name, posx, posy); 
                    } else {
                        console.log("Draggable Element was malformed.");
                    }
                }
            });

            return c;
        };

        /**
         * Load a DFD from the given URL
         * @param {string} url Relative URL of the DFD
         */
        var getDfd = function (url) {
            // If GET is successful, load SimpleMediaType DFD
            var onSuccess = function (response) {
                // Create Canvas
                var canvas = createNewTab(response.getData().label);
                ElementFactory.loadDfd(canvas, response);
                
                // Load nodes
                response.getData().nodeList.forEach(function (entry) {
                    ElementFactory.loadElement(canvas, entry);
                });
                
                // Load DiaNodes
                response.getData().DiaNodeList.forEach(function (entry) {
                    ElementFactory.loadElement(canvas, entry);
                });
                
                // Load links
                response.getData().linkList.forEach(function (entry) {
                    ElementFactory.loadDataflow(canvas, entry);
                }); 
            };

            // If GET is not successful
            var onFail = function (response) {
                // TODO: Handle error better
                console.log("Request to get DFD failed. " + response.getError());
            };

            // Execute the GET request
            Connector.get(url, onSuccess, onFail);
        };
               
        /**
         * 
         * @param {Canvas} canvas
         * @returns {undefined}
         */
        var saveCurrentDfd = function (canvas) {
            // Responses
            var onSuccess = function (response) {
                console.log("Request to save DFD was successful.");                
                // ID is only new information from server.
                canvas.setId(response.getData().id);
                
                // Save elements now
                canvas.getElements().forEach(function (entry) {
                    var onSuccess = function (response) {
                        entry.setId(response.getData().id);
                    };
                    
                    var onFail = function (response) {
                        console.log("Failed to save an element. " + response.getError());
                    };
                    
                    var data = {
                        id: entry.getId(),
                        diagramId: canvas.getId(),
                        type: entry.getType().name,
                        label: entry.getText(),
                        x: entry.getPosition().x,
                        y: entry.getPosition().y
                    };

                    Connector.put("Controller.php/" + entry.getId(), data, onSuccess, onFail, false);
                });
                
                // Save dataflows, needs to be executed after all of the elements are saved
                canvas.getDataflows().forEach(function (entry) {
                    var onSuccess = function (response) {
                        entry.setId(response.getData().id);
                    };
                    
                    var onFail = function (response) {
                        console.log("Failed to save a Dataflow. " + response.getError());
                    };
                    
                    var data = {
                        id: entry.getId(),
                        diagramId: canvas.getId(),
                        genericType: "Link",
                        type: "DataFlow",
                        originNode: {
                            id: entry.getSource().getId()
                        },
                        destinationNode: {
                            id: entry.getTarget().getId()
                        }
                    };
                    
                    Connector.put("Controller.php/" + entry.getId(), data, onSuccess, onFail);
                });
            };
            
            var onFail = function(response) {
                console.log("Request to save DFD failed. " + response.getError() + " " + response.getData());
            };
            
            var data = {
                id: canvas.getId(),
                type: "DataFlowDiagram",
                label: "Label 1" // TODO: Get from UI
            };
            
            // Execute the request
            Connector.put("Controller.php/" + canvas.getId(), data, onSuccess, onFail);
        };
        
        // Expose methods to be public
        return {
            setupUi: publicSetupUi
        };
    });