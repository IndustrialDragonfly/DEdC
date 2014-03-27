/**
 * Create a Dataflow from source to target
 * @constructor
 * @param {Canvas} canvas - Canvas Object
 * @param {Element} source - Source of the Dataflow
 * @param {Element} target - Target of the Dataflow
 */
define(function() {

    return function Dataflow(canvas, source, target) {
        var me = this,
                mySource = source,
                myTarget = target,
                myCanvas = canvas,
                path,
                arrow,
                textBox,
                sourcePoint,
                targetPoint,
                myId = "",
                myOriginator = "";
        
        /**
         * Remove the element from the canvas
         */
        this.remove = function () {
            if (textBox) {
                textBox.remove();
                textBox = null;
            }

            if (path) {
                path.remove();
                path = null;
            }
            
            if (arrow) {
                arrow.remove();
                arrow = null;
            }
        };
        
        /**
         * Get the Dataflow's Id
         * @returns {String} id
         */
        this.getId = function () {
            return myId;
        };
        
        /**
         * Set the Dataflow's Id
         * @param {String} id
         */
        this.setId = function (id) {
            myId = id;
        };
        
        /**
         * Get the Dataflow's Originator
         * @returns {String} originator
         */
        this.getOriginator = function () {
        	return myOriginator;
        };
        
        /**
         * Set the Dataflow's Originator
         * @param {String} originator
         */
        this.setOriginator = function (originator) {
        	myOriginator = originator;
        }
        
        /**
         * Get the Source Element
         * @returns {Element}
         */
        this.getSource = function () {
            return mySource;
        };
        
        /**
         * Get the Target Element
         * @returns {Element}
         */
        this.getTarget = function() {
            return myTarget;
        };

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
                return textBox.attr("text");
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
        * Called when any Shape in the set is double clicked
        */
        var onMouseDoubleClick = function() {
            myCanvas.elementDoubleClicked(me);
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
                path.dblclick(onMouseDoubleClick);
                arrow = canvas.createPath(arrowString).attr("fill", "black");
                arrow.transform(arrowRotationString);
                arrow.mouseup(onMouseClick);
                arrow.dblclick(onMouseDoubleClick);
            }

            // Update text position
            this.updateTextPosition();

        };

        // Initially calculate the path
        this.calcPath();
    };

});