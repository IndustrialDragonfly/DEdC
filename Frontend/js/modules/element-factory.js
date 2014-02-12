/**
 * This model handles the dirty part of connecting the datamodel to the visual representation
 */
define(["modules/globals", "modules/element"], function (globals, Element) {
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
    var publicCreateElement = function (canvas, type, x, y) {
            var e;
            if (type === globals.ELETYPE.PROCESS.name) {
                // Create process
                e = new Element(canvas);
                e.setGraphicByJson([{
                    type: "circle",
                    cx: x,
                    cy: y,
                    r: 25,
                    fill: "#FFF",
                    stroke: "#000",
                    "stroke-width": "2px"
                }]);
                e.draggable();
                canvas.pushElement(e);
            } else if (type === globals.ELETYPE.MULTIPROCESS.name) {
                // Create multiprocess
                e = new Element(canvas);
                e.setGraphicByJson([{
                    type: "circle",
                    cx: x,
                    cy: y,
                    r: 25,
                    fill: "#FFF",
                    stroke: "#000",
                    "stroke-width": "2px"

                }, {
                    type: "circle",
                    cx: x,
                    cy: y,
                    r: 18,
                    fill: "#FFF",
                    stroke: "#000",
                    "stroke-width": "2px"
                }]);

                e.draggable();
                canvas.pushElement(e);
            } else if (type === globals.ELETYPE.DATASTORE.name) {
                // Create datastore
                e = new Element(canvas);

                // Top left instead of center for boxes
                var shiftedX = x - 25;
                var shiftedY = y - 25;

                e.setGraphicByJson([{
                    type: "path",
                    path: "M" + shiftedX + " " + shiftedY + " L" + (shiftedX + 50) + " " + shiftedY + " Z",
                    stroke: "#000",
                    "stroke-width": 2
                }, {
                    type: "path",
                    path: "M" + shiftedX + " " + (shiftedY + 50) + " L" + (shiftedX + 50) + " " + (shiftedY + 50) + " Z",
                    stroke: "#000",
                    "stroke-width": 2
                }, {
                    type: "rect",
                    x: shiftedX,
                    y: shiftedY + 1,
                    width: 50,
                    height: 48,
                    stroke: "#000",
                    "stroke-width": 0,
                    fill: "#FFF"
                }]);
                e.draggable();
                canvas.pushElement(e);
            } else if (type === globals.ELETYPE.EXTINTERACTOR.name) {
                // Create external interactor
                e = new Element(canvas);

                e.setGraphicByJson([{
                    type: "rect",
                    x: x - 25,
                    y: y - 25,
                    width: 50,
                    height: 50,
                    fill: "#FFF",
                    stroke: "#000",
                    "stroke-width": "2px"
                }]);
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
    var publicLoadElement = function (canvas, entry) {
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
    var publicLoadDataflow = function (canvas, entry) {
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
});