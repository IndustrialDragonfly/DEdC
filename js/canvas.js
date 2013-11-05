/**
 * Make Raphael Set draggable
 */
Raphael.st.draggable = function()
{
	// Cache Set so elements can use it
	var parent = this;

	// Transform location
	var tx = 0,
		ty = 0;

	// Drag origin
	var ox = 0,
		oy = 0;

	var onMove = function(dx, dy)
	{
		// Add delta to transform location
		tx = dx + ox;
		ty = dy + oy;
		parent.transform('t' + tx + ',' + ty);
	};
	
	var onStart = function()
	{
		parent.toFront();
	};
	
	var onEnd = function() 
	{
		// Store for next drag origin
		ox = tx;
		oy = ty;
	};

	this.drag(onMove, onStart, onEnd);

	return this;
};

/**
 * Create a canvas
 * @constructor
 * @param {string} container - DOM element's ID for the parent element of the canvas
 * @param {number} width - The width of the canvas in pixels
 * @param {number} height - The height of the canvas in pixels
 */
function Canvas(container, width, height)
{	
	// Create canvas with Raphael with given arguments
	var paper = Raphael(container, width, height);

	var elements = new Array();

	/**
	 * Set the background color of the canvas
	 * @param {string} color - Hex value of the color, i.e., '#A8A8A8'
	 */
	this.setBackground = function(color) 
	{
		paper.canvas.style.backgroundColor = color;
	};

	/**
	 * Get number of elements on the canvas
	 * @return {number} Number of elements
	 */
	this.getNumberOfElements = function() 
	{
		return elements.length;
	};

	/**
	 * Style a shape with the default styling
	 * @param {Element} shape - The shape to be styled
	 */
	var styleShape = function(shape) 
	{
		shape.attr("fill", "#FFF");
		shape.attr("stroke", "#000");
		shape.attr("stroke-width", "1px");
	};

	/** 
	 * Add a process element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {Element} Element on canvas
	 */
	this.addProcess = function(x,y) 
	{
		var st = paper.set();
		st.push(paper.circle(x,y,25));
		styleShape(st);

		st.draggable();

		elements.push(st);

		return st;
	};

	/** 
	 * Add a multi-process element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {Element} Element on canvas
	 */
	this.addMultiProcess = function(x,y) 
	{
		var st = paper.set();
		var c1 = paper.circle(x,y,25);
		
		var c2 = paper.circle(x,y,18);
		
		st.push(c1,c2);
		styleShape(st);

		st.draggable();

		elements.push(st);
		
		return st;
	};

	/** 
	 * Add a datastore element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {Element} Element on canvas
	 */
	this.addDatastore = function(x,y) 
	{
		x = x - 25;
		y = y - 25;
		var st = paper.set();
		var p1 = paper.path("M" + x + " " + y + " L" + (x+50) + " " + y + " Z");
		styleShape(p1);
		
		var p2 = paper.path("M" + x + " " + (y+50) + " L" + (x+50) + " " + (y+50) + " Z");
		styleShape(p2);
		
		var rec = paper.rect((x), (y+1), 50, 48);
		rec.attr("stroke-width", 0);
		rec.attr("fill", "#FFF");
		
		st.push(p1,p2,rec);

		st.draggable();

		elements.push(st);
		
		return st;
	};

	/** 
	 * Add a external interactor element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {Element} Element on canvas
	 */
	this.addExtInteractor = function(x,y) 
	{
		var st = paper.set();
		st.push(paper.rect(x - 25,y - 25,50,50));
		styleShape(st);

		st.draggable();

		elements.push(st);

		return st;
	};
};