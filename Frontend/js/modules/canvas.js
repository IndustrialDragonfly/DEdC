/**
* Create a canvas
* @constructor
* @param {string} container - DOM element's ID for the parent element of the canvas
* @param {number} width - The width of the canvas in pixels
* @param {number} height - The height of the canvas in pixels
*/
define(["raphael", "modules/dataflow", "modules/globals", "jquery"], function(Raphael, Dataflow, globals, $) {
    
    /**
    * Make Raphael Set draggable
    * @param {Function} callback - Function to call when an object has been moved
    * @param {Element} element - Element that this Set belongs to
    */
   Raphael.st.draggable = function (callback, element) {
       // Cache Set so elements can use it
       var parent = element.getSet();
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

       parent.drag(onMove, onStart, onEnd);
   };

    return function Canvas(container, width, height) {
       // Create canvas with Raphael with given arguments
    	var myContainer = container,
    		paper = Raphael(container, width, height),
    		me = this;

       // Datamodel
       var myOriginator = "",
           myLabel = "",
           myId = "";

       // Element and Dataflow arrays
       var elements = [];
       var dataflows = [];

       // Curent selection
       var selection = [];
       var dataflowSelection = [];
       
       var doubleClickCallback = null;

       // Register key events
       var ctrlState = globals.KEYSTATE.UP; // Default state for ctrl
       $("body").keydown(function (e) {
           if (e.which === globals.KEYS.CTRL.value) {
               ctrlState = globals.KEYSTATE.DOWN;
           }
       });

       $("body").keyup(function (e) {
           if (e.which === globals.KEYS.CTRL.value) // CTRL
           {
               ctrlState = globals.KEYSTATE.UP;
           }
       });
       
       /**
        * Handle unselecting all of the elements/dataflows when the canvas background is clicked
        */
       $('#' + myContainer).click(function(e) {
    	   if (e.target.nodeName == "svg" || e.target.nodeName == "DIV") {
	    	   me.unselectAllElements();
	    	   me.unselectAllDataflows();
    	   }
       });
       
       /**
        * Return the id of the container
        */
       this.getContainer = function () {
    	   return myContainer;
       };
       
       /**
        * Event called when an Element on the canvas is clicked
        * It handles the selection of elements;
        * @param {Element} element - Element that was clicked
        */
       this.elementClicked = function (element) {
           if (ctrlState === globals.KEYSTATE.UP) {
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
        * Event called when an Element on the canvas is double clicked.
        * @param {type} element
        * @returns {undefined}
        */
       this.elementDoubleClicked = function (element) {
           if (doubleClickCallback) {
               doubleClickCallback(element);
           }
       };
       
       /**
        * Set the double click callback
        * @param Function callback
        */
       this.setElementDoubleClickedCallback = function (callback) {
           doubleClickCallback = callback;
       };
       
       /**
        * Event called when an Dataflow on the canvas is clicked
        * It handles the selection of elements;
        * @param {Dataflow} dataflow - Dataflow that was clicked
        */
       this.dataflowClicked = function (dataflow) {
           if (ctrlState === globals.KEYSTATE.UP) {
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
        * Get an array of the currently selected elements
        * @returns {Array} Array of Elements
        */
       this.getSelection = function () {
           return selection;
       };
       
       /**
        * Get an array of the currently selected DataFlows
        * @returns {Array}
        */
       this.getSelectedDataFlows = function () {
            return dataflowSelection;
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
        * Get Elements on the canvas
        * @return {Array} Elements
        */
       this.getElements = function () {
           return elements;
       };

       /**
        * Get Dataflows on the canvas
        * @return {number} Dataflows
        */
       this.getDataflows = function () {
           return dataflows;
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
        * Add a dataflow to the canvas between the two elements
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

       /**
        * Add a dataflow to the canvas using two Element ids
        * @param {String} sourceId
        * @param {String} targetId
        * @return {Dataflow} Created dataflow if successful, null otherwise
        */
       this.addDataflowById = function (sourceId, targetId) {
           var source = null,
               target = null;
           
           elements.forEach(function (entry) {
               if (entry.getId() === sourceId) {
                   source = entry;
               } else if (entry.getId() === targetId) {
                   target = entry;
               }
           });
           
           if (source == null) {
        	   console.log("DataFlow could not find source Element with id: " + sourceId);
        	   return null;
           } else if (target == null) {
        	   console.log("DataFlow could not find target Element with id: " + targetId);
        	   return null;
           } else {
        	   return this.addDataflow(source, target);
           }
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
               this.unselectAllElements();
               element.remove();
               elements.splice(index, 1);
               return true;
           }
           return false;
       };
       
       /**
        * Remove an element from the canvas using the id
        * @param {type} id Id of the element
        * @return {boolean} True if the element was removed, false otherwise
        */
       this.removeElementById = function (id) {
           var element;

           elements.forEach(function (entry) {
               if (entry.getId() === id) {
                   element = entry;
               }
           });
           
           if (element) {
               return this.removeElement(element);
           }
           
           return false;
       };
       
       /**
        * Remove a Dataflow from the canvas using the id
        * @param {type} id Id of the element
        * @return {boolean} True if the element was removed, false otherwise
        */
       this.removeDataflowById = function (id) {
           var dataflow;

           dataflows.forEach(function (entry) {
               if (entry.getId() === id) {
                   dataflow = entry;
               }
           });
           
           if (dataflow) {
               return this.removeDataflow(dataflow);
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
               this.unselectAllDataflows();
               dataflow.remove();
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
       
       /**
        * Get Canvas's label
        * @returns {String}
        */
       this.getText = function () {
           return myLabel;
       };
       
       /**
        * Set the Canvas's label
        * @param {String} label New label
        */
       this.setText = function (label) {
           myLabel = label;
           
           // Update the containing tab's text
           $('a[href=#' + myContainer + ']').text(label);
       };
       
       /**
        * Get Canvas's originator
        * @returns {String}
        */
       this.getOriginator = function () {
           return myOriginator;
       };
       
       /**
        * Set the Canvas's originator
        * @param {String} originator New originator
        */
       this.setOriginator = function (originator) {
           myOriginator = originator;
       };
       
       /**
        * Get the backend ID
        * @returns {id}
        */
       this.getId = function() {
           return myId;
       };
       
       /**
        * Set the backend ID
        * @param {type} id
        */
       this.setId = function(id) {
           myId = id;
       };

    };
});