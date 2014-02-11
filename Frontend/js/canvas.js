/**
 * Make Raphael Set draggable
 * @param {Function} callback - Function to call when an object has been moved
 * @param {Element} element - Element that this Set belongs to
 */
Raphael.st.draggable = function (callback, element) {
    // Cache Set so elements can use it
    var parent = this;
    var myElement = element;

    // Transform location
    var tx = 0,
        ty = 0;

    // Drag origin
    var ox = 0,
        oy = 0;

    var onMove = function (dx, dy) {
        // Add delta to transform location
        tx = dx + ox;
        ty = dy + oy;
        parent.transform('t' + tx + ',' + ty);
        myElement.updateTextPosition();
        myElement.setHasMoved(true);

        // Recalculate the Dataflows
        callback();
    };

    var onStart = function () {
        parent.toFront();
    };

    var onEnd = function () {
        // Store for next drag origin
        ox = tx;
        oy = ty;
    };

    this.drag(onMove, onStart, onEnd);

    return this;
};

(function () {
    var DEdC = (function () {
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
            contentText, sidebarText, usersText, tabContainerText, processText, multiprocessText, datastoreText, extinteractorText, connectText, deleteButtonText, loadText, newTabText) {
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
            }).data("type", ELETYPE.PROCESS);

            $(multiprocess).draggable({
                helper: draggableHelper
            }).data("type", ELETYPE.MULTIPROCESS);

            $(datastore).draggable({
                helper: draggableHelper
            }).data("type", ELETYPE.DATASTORE);

            $(extinteractor).draggable({
                helper: draggableHelper
            }).data("type", ELETYPE.EXTINTERACTOR);

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
                    c.removeElementFromSelection();
                } else {
                    console.log("No tab currently selected.");
                }
            });

            // Load DFD button
            $(load).button().click(function () {
                // Controller.php is required until the rewrite rules work correctly
                getDfd("Controller.php/0SrNZZv12jdsHcdS10ztKGnXDLq9236REL2qCjnjHnUx_id");
            });

            // New tab button
            $(newTab).button().click(function () {
                createNewTab();
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
                    if ($(ui.draggable).data("type") === ELETYPE.PROCESS) { 
                        ElementFactory.createElement(getCurrentCanvas(), ELETYPE.PROCESS.name, posx, posy); 
                    } else if ($(ui.draggable).data("type") === ELETYPE.MULTIPROCESS) {
                        ElementFactory.createElement(getCurrentCanvas(), ELETYPE.MULTIPROCESS.name, posx, posy); 
                    } else if ($(ui.draggable).data("type") === ELETYPE.DATASTORE) {
                        ElementFactory.createElement(getCurrentCanvas(), ELETYPE.DATASTORE.name, posx, posy); 
                    } else if ($(ui.draggable).data("type") === ELETYPE.EXTINTERACTOR) {
                        ElementFactory.createElement(getCurrentCanvas(), ELETYPE.EXTINTERACTOR.name, posx, posy); 
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
                var canvas = createNewTab(response.getData().label);
                ElementFactory.loadDfd(canvas, response);
            };

            // If GET is not successful
            var onFail = function (response) {
                // TODO: Handle error better
                console.log("Request to get DFD failed. " + response.getError());
            };

            // Execute the GET request
            Connector.get(url, onSuccess, onFail);
        };
        
        // Expose methods to be public
        return {
            setupUi: publicSetupUi
        };
    })();
    
    // Element types
    var ELETYPE = {
        PROCESS: {
            value: 0,
            name: "Process",
            code: "P"
        },
        MULTIPROCESS: {
            value: 1,
            name: "Multiprocess",
            code: "MP"
        },
        DATASTORE: {
            value: 1,
            name: "DataStore",
            code: "D"
        },
        EXTINTERACTOR: {
            value: 1,
            name: "ExternalInteractor",
            code: "EI"
        }
    };

    // Keyboard events
    var KEYSTATE = {
        DOWN: {
            value: 0,
            name: "Down",
            code: "D"
        },
        UP: {
            value: 1,
            name: "Up",
            code: "U"
        }
    };

    // Keyboard keys
    var KEYS = {
        CTRL: {
            value: 17,
            name: "Ctrl",
            code: "C"
        }
    };
    
    /**
     * Create a canvas
     * @constructor
     * @param {string} container - DOM element's ID for the parent element of the canvas
     * @param {number} width - The width of the canvas in pixels
     * @param {number} height - The height of the canvas in pixels
     */
    function Canvas(container, width, height) {
        // Create canvas with Raphael with given arguments
        var paper = Raphael(container, width, height);
        
        // Datamodel
        var myData;
        
        // Element and Dataflow arrays
        var elements = [];
        var dataflows = [];

        // Curent selection
        var selection = [];
        var dataflowSelection = [];

        // Register key events
        var ctrlState = KEYSTATE.UP; // Default state for ctrl
        $("body").keydown(function (e) {
            if (e.which === KEYS.CTRL.value) {
                ctrlState = KEYSTATE.DOWN;
            }
        });

        $("body").keyup(function (e) {
            if (e.which === KEYS.CTRL.value) // CTRL
            {
                ctrlState = KEYSTATE.UP;
            }
        });
        
        /**
         * Set a data object
         * @param {Object} data object
         */
        this.setData = function (data) {
            myData = data;
        };
        
        /**
         * Get the set data object
         * @returns {Object}
         */
        this.getData = function () {
            return data;
        };

        /**
         * Event called when an Element on the canvas is clicked
         * It handles the selection of elements;
         * @param {Element} element - Element that was clicked
         */
        this.elementClicked = function (element) {
            if (ctrlState === KEYSTATE.UP) {
                // CTRL is not pressed
                // Replace selection
                this.unselectAllElements();
                selection.push(element);
                element.setSelected();
            } else {
                // CTRL is pressed
                var index = selection.indexOf(element);
                if (index < 0) {
                    // Not in selection, add it
                    selection.push(element);
                    element.setSelected();
                } else {
                    // Element was selected, remove it
                    selection.splice(index, 1);
                    element.setUnselected();
                }
            }
        };

        /**
         * Event called when an Dataflow on the canvas is clicked
         * It handles the selection of elements;
         * @param {Dataflow} dataflow - Dataflow that was clicked
         */
        this.dataflowClicked = function (dataflow) {
            if (ctrlState === KEYSTATE.UP) {
                // CTRL is not pressed
                // Replace selection
                this.unselectAllDataflows();
                dataflowSelection.push(dataflow);
                dataflow.setSelected();
            } else {
                // CTRL is pressed
                var index = selection.indexOf(dataflow);
                if (index < 0) {
                    // Not in selection, add it
                    dataflowSelection.push(dataflow);
                    dataflow.setSelected();
                } else {
                    // Element was selected, remove it
                    dataflowSelection.splice(index, 1);
                    dataflow.setUnselected();
                }
            }
        };

        /**
         * Unselect all of the elements on the canvas
         */
        this.unselectAllElements = function () {
            var e = selection.pop();
            while (e) {
                e.setUnselected();
                e = selection.pop();
            }
        };

        /**
         * Unselect all of the elements on the canvas
         */
        this.unselectAllDataflows = function () {
            var e = dataflowSelection.pop();
            while (e) {
                e.setUnselected();
                e = dataflowSelection.pop();
            }
        };

        /**
         * Set the background color of the canvas
         * @param {string} color - Hex value of the color, i.e., '#A8A8A8'
         */
        this.setBackground = function (color) {
            paper.canvas.style.backgroundColor = color;
        };

        /**
         * Get number of Elements on the canvas
         * @return {number} Number of Elements
         */
        this.getNumberOfElements = function () {
            return elements.length;
        };

        /**
         * Get number of Dataflows on the canvas
         * @return {number} Number of Dataflows
         */
        this.getNumberOfDataflows = function () {
            return dataflows.length;
        };

        /**
         * Set the size of the canvas
         * @param {number} width - New width of the canvas in pixels
         * @param {number} height - New height of the canvas in pixels
         */
        this.setSize = function (width, height) {
            paper.setSize(width, height);
        };

        /**
         * Add a dataflow to the cavas between the two elements
         * @param {Element} source - Source of the Dataflow
         * @param {Element} target - Target of the Dataflow
         * @return {Dataflow} Dataflow on the canvas
         */
        this.addDataflow = function (source, target) {
            // Cannot connect to self
            if (source === target) return null;

            var d = new Dataflow(this, source, target);
            dataflows.push(d);
            return d;
        };
        
        this.addDataflowById = function (sourceId, targetId) {
            var source,
                target;
        
            elements.forEach(function (entry) {
                if (entry.getData().id === sourceId) {
                    source = entry;
                } else if (entry.getData().id === targetId) {
                    target = entry;
                }
            });
            
            return this.addDataflow(source, target);
        };

        /**
         * Connect the current selection with Dataflows
         */
        this.addDataflowFromSelection = function () {
            for (var i = 0; i < selection.length; i++) {
                if (selection[i + 1]) this.addDataflow(selection[i], selection[i + 1]);
            }

            this.unselectAllElements();
        };

        /**
         * Remove an element from the canvas
         */
        this.removeElementFromSelection = function () {
            // TODO: Handle floating dataflows
            for (var i = 0; i < selection.length; i++) {
                this.removeElement(selection[i]);
            }
            this.unselectAllElements();
        };

        /**
         * Remove an element from the canvas
         * @param {Element} element - Element to remove from the canvas
         * @return {boolean} TRUE if the element was removed, FALSE otherwise
         */
        this.removeElement = function (element) {
            var index = elements.indexOf(element);
            if (index > -1) {
                element.remove();
                elements.splice(index, 1);
                return true;
            }
            return false;
        };

        /**
         * Remove a Dataflow from the canvas
         * @param {Dataflow} dataflow - Dataflow to remove from the canvas
         * @return {boolean} TRUE if the element was removed, FALSE otherwise
         */
        this.removeDataflow = function (dataflow) {
            var index = dataflows.indexOf(dataflow);
            if (index > -1) {
                dataflows.splice(index, 1);
                return true;
            }
            return false;
        };

        /**
         * Recalculate all Dataflows in the canvas
         * This is a performance killer at this time
         */
        this.calcDataflows = function () {
            for (var i = 0; i < dataflows.length; i++) {
                dataflows[i].calcPath();
            }
        };
        
        /**
         * Create a new element with the visual representation defined by a JSON array
         * @param {Object} json JSON array as defined at http://raphaeljs.com/reference.html#Paper.add
         * @returns {Raphael.Set} Resulting set of elements
         */
        this.createFromJsonArray = function (json)
        {
            return paper.add(json);
        };
        
        /**
         * Push a new element onto the DFD view
         * @param {Element} element Element to add
         */
        this.pushElement = function (element) {
            elements.push(element);
        };
        
        /**
         * Create a Set for graphical objects in the Canvas
         * @returns {Raphael.Set}
         */
        this.createSet = function () {
            return paper.set();
        };
        
        /**
         * Create a text label at the given coordinates and text
         * @param {Number} x X position in pixels
         * @param {Number} y Y position in pixels
         * @param {String} text Text of the label
         * @returns {Raphael.Text}
         */
        this.createText = function (x, y, text) {
            return paper.text(x, y, text);
        };
        
        /**
         * Create a path using the given path string as specified:
         * http://raphaeljs.com/reference.html#Paper.path
         * @param {String} pathString SVG formatted path string
         * @returns {Raphael.Path}
         */
        this.createPath = function (pathString) {
            return paper.path(pathString);
        };
    }

    /**
     * Base Class for all Elements
     * @constructor
     * @param {Cavas} canvas - Canvas Object
     */
    function Element(canvas) {
        var me = this,
            myCanvas = canvas, // Internal reference to canvas
            set = canvas.createSet(), // Raphael.Set for shapes
            textBox,
            hasMoved = false,
            myData;
    
        /**
         * Set a data object
         * @param {Object} data object
         */
        this.setData = function (data) {
            myData = data;
        };
        
        /**
         * Get the set data object
         * @returns {Object}
         */
        this.getData = function () {
            return myData;
        };

        /**
         * Add a shape to the Element
         * @param {Raphael.Shape} shape - Shape to add
         */
        this.push = function (shape) {
            for (var i = 0; i < arguments.length; i++) {
                set.push(arguments[i]);
                arguments[i].mouseup(onMouseClick);
            }
        };

        /**
         * Get Set of Shapes
         * @returns {Raphael.Set} Set of Shapes
         */
        this.getSet = function () {
            return set;
        };

        /**
         * Make the Element draggable, any elements added after
         * draggable is called will not function properly
         */
        this.draggable = function () {
            set.draggable(myCanvas.calcDataflows, me);
        };

        /**
         * Style all of the Shapes with the default styling
         */
        this.applyDefaultStyle = function () {
            set.attr("fill", "#FFF");
            set.attr("stroke", "#000");
            set.attr("stroke-width", "2px");
        };

        /**
         * Get the bounding box for the Element
         * @returns {Raphael.BBox}
         */
        this.getBBox = function () {
            return set.getBBox();
        };

        /**
         * Called when the Element is selected
         */
        this.setSelected = function () {
            set.animate({
                "fill-opacity": 0.2
            }, 100);
        };

        /**
         * Called when the Element is unselected
         */
        this.setUnselected = function () {
            set.animate({
                "fill-opacity": 1.0
            }, 100);
        };

        /**
         * Get whether the element has moved
         * @returns {Boolean} True if element has moved, false otherwise
         */
        this.getHasMoved = function () {
            return hasMoved;
        };

        /**
         * Get whether the element has moved
         * @param {Boolean} moved - Set to true when moved, false when updated
         */
        this.setHasMoved = function (moved) {
            hasMoved = moved;
        };

        /**
         * Get the attach points for the Element
         * @returns {Array} Array of four points (x,y)
         */
        this.getAttachPoints = function () {
            var bb = this.getBBox();
            var points = [];

            points.push({
                x: bb.x,
                y: bb.y + bb.height / 2
            }); // Left
            points.push({
                x: bb.x + bb.width / 2,
                y: bb.y
            }); // Top
            points.push({
                x: bb.x + bb.width,
                y: bb.y + bb.height / 2
            }); // Right
            points.push({
                x: bb.x + bb.width / 2,
                y: bb.y + bb.height
            }); // Bottom
            return points;
        };

        /**
         * Set the text label for the element
         * @param {String} text - Text to set label to
         */
        this.setText = function (text) {
            if (!textBox) {
                var points = this.getAttachPoints();
                textBox = myCanvas.createText(points[3].x, points[3].y + 10, text);
            } else {
                textBox.attr("text", text);
            }
        };
        
        /**
         * Get the text of the label for the element
         * @returns {String} Text
         */
        this.getText = function () {
            if (textBox) {
                textBox.attr("text");
            } else {
                return "";
            }
        };

        /**
         * Called when any Shape in the set is clicked
         */
        var onMouseClick = function () {
            // Using "this" would result in the wrong object being used
            myCanvas.elementClicked(me);
        };

        /**
         * Update the position of the text label
         */
        this.updateTextPosition = function () {
            if (textBox) {
                var points = this.getAttachPoints();
                textBox.attr("x", points[3].x);
                textBox.attr("y", points[3].y + 10);
            }
        };

        /**
         * Remove the element from the canvas
         */
        this.remove = function () {
            if (textBox) {
                textBox.remove();
            }

            if (set) {
                set.remove();
            }
        };
    };

    /**
     * Create a Dataflow from source to target
     * @constructor
     * @param {Canvas} canvas - Canvas Object
     * @param {Element} source - Source of the Dataflow
     * @param {Element} target - Target of the Dataflow
     */
    function Dataflow(canvas, source, target) {
        var me = this,
                mySource = source,
                myTarget = target,
                myCanvas = canvas,
                path,
                arrow,
                myData,
                textBox,
                sourcePoint,
                targetPoint;

        /**
         * Update the position of the text label
         */
        this.updateTextPosition = function () {
            // If the dataflow actually has text
            if (textBox) {
                // Calculate mid point
                var newX = sourcePoint.x + targetPoint.x;
                var newY = sourcePoint.y + targetPoint.y;
                
                newX = newX / 2;
                newY = newY / 2;
                
                // Move textbox to point
                textBox.attr("x", newX);
                textBox.attr("y", newY);
            }
        };
        
        /**
         * Set a data object
         * @param {Object} data object
         */
        this.setData = function (data) {
            myData = data;
        };
        
        /**
         * Get the set data object
         * @returns {Object}
         */
        this.getData = function () {
            return myData;
        };
       
       /**
         * Set the text label for the element
         * @param {String} text - Text to set label to
         */
        this.setText = function (text) {
            if (!textBox) {
                // Create the textbox if it doesn't exist
                var newX = sourcePoint.x + targetPoint.x;
                var newY = sourcePoint.y + targetPoint.y;
                
                newX = newX / 2;
                newY = newY / 2;
                textBox = myCanvas.createText(newX, newY, text);
            } else {
                // Update the text in the box
                textBox.attr("text", text);
            }
        };
        
        /**
         * Get the text label of the Dataflow
         * @returns {String} Text label
         */
        this.getText = function () {
            if (textBox) {
                textBox.attr("text");
            } else {
                return "";
            }
        };
       
        // Make sure this dataflow will get drawn
        myTarget.setHasMoved(true);
        mySource.setHasMoved(true);

        /**
         * Called when the Element is selected
         */
        this.setSelected = function () {
            path.animate({
                "stroke-width": 5
            }, 100);
        };

        /**
         * Called when the Element is unselected
         */
        this.setUnselected = function () {
            path.animate({
                "stroke-width": 3
            }, 100);
        };

        /**
         * Called when any Shape in the set is clicked
         */
        var onMouseClick = function () {
            // Using "this" would result in the wrong object being used
            myCanvas.dataflowClicked(me);
        };

        /**
         * Calculate Dataflow's path as the minium between the two Elements
         */
        this.calcPath = function () {
            if (!mySource.getHasMoved() || !myTarget.getHasMoved()) {
                return;
            }

            var sourcePoints = mySource.getAttachPoints();
            var targetPoints = myTarget.getAttachPoints();
            var sourceIndex = 0; // Shortest point index for source
            var targetIndex = 0; // Shortest point index for source
            var min; // Minimum length

            // Loop through all of the attach points for both Elements
            for (var i = 0; i < sourcePoints.length; i++) {
                for (var j = 0; j < targetPoints.length; j++) {
                    // Calculate vector's length (sqrt(a^2 + b^2))
                    var dx = Math.abs(sourcePoints[i].x - targetPoints[j].x);
                    var dy = Math.abs(sourcePoints[i].y - targetPoints[j].y);
                    var length = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2));
                    if (min) {
                        // Check if new vector is minimum
                        if (length < min) {
                            sourceIndex = i;
                            targetIndex = j;
                            min = length;
                        }
                    } else // No previous minimum existed
                    {
                        sourceIndex = i;
                        targetIndex = j;
                        min = length;
                    }
                }
            }
            
            // TODO: Change things below to use this instead of targetPoints and sourcePoints.
            sourcePoint = sourcePoints[sourceIndex];
            targetPoint = targetPoints[targetIndex];
            
            var pathString = "M#{sourceX} #{sourceY} L#{targetX} #{targetY} Z";
            pathString = pathString.replace(/#\{sourceX\}/g, sourcePoints[sourceIndex].x)
                    .replace(/#\{sourceY\}/g, sourcePoints[sourceIndex].y)
                    .replace(/#\{targetX\}/g, targetPoints[targetIndex].x)
                    .replace(/#\{targetY\}/g, targetPoints[targetIndex].y);

            var angle = Math.atan2(targetPoints[targetIndex].x - sourcePoints[sourceIndex].x, targetPoints[targetIndex].y - sourcePoints[sourceIndex].y);
            angle = (angle / (2 * Math.PI)) * 360;
            var arrowString = "M#{targetX1} #{targetY1} L#{targetX2} #{targetY2} L#{targetX3} #{targetY3} L#{targetX4} #{targetY4}";
            arrowString = arrowString.replace(/#\{targetX1\}/g, targetPoints[targetIndex].x)
                    .replace(/#\{targetY1\}/g, targetPoints[targetIndex].y)
                    .replace(/#\{targetX2\}/g, targetPoints[targetIndex].x - 5)
                    .replace(/#\{targetY2\}/g, targetPoints[targetIndex].y - 5)
                    .replace(/#\{targetX3\}/g, targetPoints[targetIndex].x - 5)
                    .replace(/#\{targetY3\}/g, targetPoints[targetIndex].y + 5)
                    .replace(/#\{targetX4\}/g, targetPoints[targetIndex].x)
                    .replace(/#\{targetY4\}/g, targetPoints[targetIndex].y);

            var arrowRotationString = "r #{angle},#{targetX},#{targetY}";
            arrowRotationString = arrowRotationString.replace(/#\{angle\}/g, (-90 + angle) * -1)
                    .replace(/#\{targetX\}/g, targetPoints[targetIndex].x)
                    .replace(/#\{targetY\}/g, targetPoints[targetIndex].y);

            if (path && arrow) {
                // Path existed, update
                path.attr({
                    path: pathString
                });
                arrow.attr({
                    path: arrowString
                });
                arrow.transform(arrowRotationString);
            } else {
                if (path) {
                    path.remove();
                }
                
                if (arrow) {
                    arrow.remove();
                }

                // Path did not exist, create
                path = canvas.createPath(pathString).attr("stroke-width", 3);
                path.mouseup(onMouseClick);
                arrow = canvas.createPath(arrowString).attr("fill", "black");
                arrow.transform(arrowRotationString);
            }
            
            // Update text position
            this.updateTextPosition();
            
        };

        // Initially calculate the path
        this.calcPath();
    };

    /**
     * Base Response for requests
     * @constructor
     */
    function Response() {
        var me = this;
        var myStatus = null;
        var myData = null;
        var myError = null;

        /**
         * Get the status of the Response
         * @return {String} Status given from jQuery
         */
        this.getStatus = function () {
            return myStatus;
        };

        /**
         * Set the status of the Response
         * @param {String} status Status text
         * @returns {Response} this, for chaining
         */
        this.setStatus = function (status) {
            myStatus = status;
            return me;
        };

        /**
         * Get the data returned from the server
         * @returns {String} Data as string from server
         */
        this.getData = function () {
            return myData;
        };

        /**
         * Set the status of the Response
         * @param {String} data Data
         * @returns {Response} this, for chaining
         */
        this.setData = function (data) {
            myData = data;
            return me;
        };

        /**
         * Get the error message if there was an error
         * @returns {String} Error message
         */
        this.getError = function () {
            return myError;
        };

        /**
         * Set the error message
         * @param {String} error Error message
         * @returns {Response} this, for chaining
         */
        this.setError = function (error) {
            myError = error;
            return me;
        };
    };
    
    /**
     * This model handles the dirty part of connecting the datamodel to the visual representation
     */
    var ElementFactory = (function() {
        /**
         * Load a DFD from the given Response object
         * @param {Canvas} canvas object to load Dfd into
         * @param {Response} response Response from the GET Dfd request
         */
        var publicLoadDfd = function (canvas, response) {
            // TODO: Check type to make sure it's a Dfd
            
            // Set the DFD view's datamodel
            canvas.setData({
                "id": response.getData().id,
                "label": response.getData().label,
                "type": response.getData().type,
                "originator": response.getData().originator,
                "genericType": response.getData().genericType,
                "subDFDNode": response.getData().subDFDNode
            });

            response.getData().nodes.forEach(function (entry) {
                publicLoadElement(canvas, entry);
            });

            response.getData().subDFDNodes.forEach(function (entry) {
                // TODO: Handle subDFDNodes
                publicLoadElement(canvas, entry);
            });

            response.getData().links.forEach(function (entry) {
                publicLoadDataflow(canvas, entry);
            });
        };
        
        /**
         * Create a new Element on canvas
         * @param {Canvas} canvas Canvas to add element to
         * @param {String} type The type of the element as a String, ELETYPE.name
         * @param {type} x
         * @param {type} y
         * @returns {Element} Created element
         */
        var publicCreateElement = function(canvas, type, x, y) {
            var e;
            if (type === ELETYPE.PROCESS.name) {
                // Create process
                e = new Element(canvas);
                e.push(canvas.createFromJsonArray([
                    {
                        type: "circle",
                        cx: x,
                        cy: y,
                        r: 25,
                        fill: "#FFF",
                        stroke: "#000",
                        "stroke-width": "2px"
                    }
                ]));
                e.draggable();
                canvas.pushElement(e);
            } else if (type === ELETYPE.MULTIPROCESS.name) {
                // Create multiprocess
                e = new Element(canvas);
                e.push(canvas.createFromJsonArray([
                    {
                        type: "circle",
                        cx: x,
                        cy: y,
                        r: 25,
                        fill: "#FFF",
                        stroke: "#000",
                        "stroke-width": "2px"

                    },
                    {
                        type: "circle",
                        cx: x,
                        cy: y,
                        r: 18,
                        fill: "#FFF",
                        stroke: "#000",
                        "stroke-width": "2px"
                    }
                ]));
                
                e.draggable();
                canvas.pushElement(e);            
            } else if (type === ELETYPE.DATASTORE.name) {
                // Create datastore
                e = new Element(canvas);

                // Top left instead of center for boxes
                var shiftedX = x - 25;
                var shiftedY = y - 25;

                e.push(canvas.createFromJsonArray([
                    {
                        type: "path",
                        path: "M" + shiftedX + " " + shiftedY + " L" + (shiftedX + 50) + " " + shiftedY + " Z",
                        stroke: "#000",
                        "stroke-width": 2
                    },
                    {
                        type: "path",
                        path: "M" + shiftedX + " " + (shiftedY + 50) + " L" + (shiftedX + 50) + " " + (shiftedY + 50) + " Z",
                        stroke: "#000",
                        "stroke-width": 2
                    },
                    {
                        type: "rect",
                        x: shiftedX,
                        y: shiftedY + 1,
                        width: 50,
                        height: 48,
                        stroke: "#000",
                        "stroke-width": 0,
                        fill: "#FFF"
                    }
                ]));
                e.draggable();
                canvas.pushElement(e);
            } else if (type === ELETYPE.EXTINTERACTOR.name) {
                // Create external interactor
                e = new Element(canvas);
                
                e.push(canvas.createFromJsonArray([
                    {
                        type: "rect",
                        x: x - 25,
                        y: y - 25,
                        width: 50,
                        height: 50,
                        fill: "#FFF",
                        stroke: "#000",
                        "stroke-width": "2px"
                    }
                ]));
                e.draggable();
                canvas.pushElement(e);
            } else {
                // The type was not recognized
                console.log("\"" + type + "\" was not a recognized element type.");
                return;
            }
            
            // Return the element
            return e;
        };
                
        /**
         * Load an Element from the data model object
         * @param {Canvas} canvas The DFD view to add the element to
         * @param {Object} entry Datamodel representation of element
         */
        var publicLoadElement = function(canvas, entry) {
            // TODO: Check type to make sure it's an element
            var e = publicCreateElement(canvas, entry.type, entry.x, entry.y);    

            // Set the text label and id for the element
            e.setText(entry.label);
            e.setData({
                "id": entry.id,
                "type": entry.type
                //"label": entry.label, // These will be pulled from the graphical representation
                //"x": entry.x,
                //"y": entry.y
            });
            
            return e;
        };
        
        /**
         * Load a Dataflow into the given canvas
         * @param {Canvas} canvas The DFD view to add the Dataflow to
         * @param {Object} entry Datamodel representation of element
         */
        var publicLoadDataflow = function(canvas, entry) {
            // Connect Dataflow by the Elements' ids
            var d = canvas.addDataflowById(entry.origin_id, entry.dest_id);
            
            // Set the Dataflow's id and text label
            d.setData({
                "id": entry.id,
                "type": entry.type
                //"label": entry.label, // These will be pulled from the graphical representation
                //"x": entry.x,
                //"y": entry.y
                //"origin_id": entry.origin_id,
                //"dest_id": entry.dest_id
            });
            
            // Set the text label
            d.setText(entry.label);
        };
         
        return {
            loadElement: publicLoadElement,
            loadDataflow: publicLoadDataflow,
            loadDfd: publicLoadDfd,
            createElement: publicCreateElement
        };
    })();

    /**
     * Connector will handel all Ajax code
     */
    var Connector = (function () {

        /**
         * Ajax GET method
         * @param {String} url Url of resource
         * @param {Function} successCallback Function called if a request executes successfully
         * @param {Function} failCallback Function called if a request does not execute successfully
         */
        var publicGet = function (url, successCallback, failCallback) {
            $.ajax({
                accepts: "application/json",
                url: url,
                dataType: "json" // Do not let jQuery automatically parse the JSON response
            }).done(function (data, textStatus) {
                // Request was successful
                successCallback(parseJson(data));
            }).fail(function (jqXHR, textStatus, errorThrown) {
                // Request failed for some reason
                var response = new Response();

                response.setStatus(textStatus);
                response.setError("GET " + url + " " + jqXHR.status + " (" + jqXHR.statusText + ")");

                failCallback(response);
            });
        };

        /**
         * Parse a JSON object
         * @param {jsonObject} jsonObject JSON document that has been translated by jQuery to an Object
         * @param {String} textStatus Status text from jQuery.ajax
         */
        var parseJson = function (jsonObject, textStatus) {
            var response = new Response();

            response.setData(jsonObject);
            response.setStatus(textStatus);

            return response;
        };

        return {
            get: publicGet
        };
    })();

    // Expose the DEdC module
    window.DEdC = DEdC;
})();