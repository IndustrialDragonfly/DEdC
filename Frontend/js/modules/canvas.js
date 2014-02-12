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

    return function Canvas(container, width, height) {
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
    };
});