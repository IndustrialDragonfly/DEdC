/**
 * Base Class for all Elements
 * @constructor
 * @param {Cavas} canvas - Canvas Object
 */
define(function () {

    return function Element(canvas) {
            var me = this,
                myCanvas = canvas,
                // Internal reference to canvas
                set = canvas.createSet(),
                // Raphael.Set for shapes
                textBox, hasMoved = false,
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

});