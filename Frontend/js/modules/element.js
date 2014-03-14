/**
 * Base Class for all Elements
 * @constructor
 * @param {Cavas} canvas - Canvas Object
 */
define(function () {

    return function Element(canvas) {
        var me = this,
            myCanvas = canvas,
            set = null,
            textBox, 
            hasMoved = false,
            myId = null,
            myType = null,
            myOriginator = null;
        
        /**
         * Get the internal Raphael.Set
         * @returns {Raphael.Set}
         */
        this.getSet = function() {
            return set;
        };

       /**
        * Create a new element with the visual representation defined by a JSON array
        * @param {Object} json JSON array as defined at http://raphaeljs.com/reference.html#Paper.add
        */
        this.setGraphicByJson = function (json) {
            if (set) {
                set.remove();
                set = myCanvas.createFromJsonArray(json);
            } else {
                set = myCanvas.createFromJsonArray(json);
            }
            set.mouseup(onMouseClick);
        };

        /**
         * Make the Element draggable, any elements added after
         * draggable is called will not function properly
         */
        this.draggable = function () {
            set.draggable(myCanvas.calcDataflows, me);
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
         * Get the center point of the Element
         * @returns {Object}
         * {
         *      x: {Number} x coordinate
         *      y: {Number} y coordinate
         * }
         */
        this.getPosition = function () {
            var bb = this.getBBox();
            var point = {
                x: (bb.x + bb.x2) / 2,
                y: (bb.y + bb.y2) / 2
            };
            return point;
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
                return textBox.attr("text");
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
                textBox = null;
            }

            if (set) {
                set.remove();
                set = null;
            }
        };
        
        /**
         * Get the Element type
         * @returns {ELETYPE}
         */
        this.getType = function() {
            return myType;
        };
        
        /**
         * Set the Element type
         * @param {ELETYPE} type
         */
        this.setType = function(type) {
            myType = type;
        };
        
        /**
         * Get the Element's id
         * @returns {id}
         */
        this.getId = function() {
            return myId;
        };
        
        /**
         * Set the id of the Element
         * @param {String} id
         */
        this.setId = function(id) {
            myId = id;
        };
        
        /**
         * Get the Originator
         * @returns {myOriginator|originator}
         */
        this.getOriginator = function () {
            return myOriginator;
        };
        
        /**
         * Set the Originator
         * @param {type} originator
         * @returns {undefined}
         */
        this.setOriginator = function (originator) {
            myOriginator = originator;
        };

    };

});