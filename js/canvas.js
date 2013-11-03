// Additions to Raphael
/**
 * Make determining a shape's type more simple
 */ 
Raphael.el.is = function (type) {
	return this.type == (''+type).toLowerCase();
};

/**
 * Get shape's y position no matter which type it is
 */
Raphael.el.x = function () {
	return this.is('circle') ? this.attr('cx') : this.attr('x');
};

/**
 * Get shape's x position no matter which type it is
 */
Raphael.el.y = function () {
	return this.is('circle') ? this.attr('cy') : this.attr('y');
};

/**
 * Store shape's current position
 */
Raphael.el.o = function () {
	this.ox = this.x();
	this.oy = this.y();
	return this;
};

/**
 * Get Advanced Bounding Box
 * returns Regular BBox with a few extra calculations
 */
Raphael.el.getABBox = function ()
{
	// Get regular bounding box
	var b = this.getBBox();

	var o = {
		x: b.x,
		y: b.y,
		width: b.width,
		height: b.height,

		// x coordinates: left edge, center, and right edge
		xLeft: b.x,
		xCenter: b.x + b.width / 2,
		xRight: b.x + b.width,


		// y coordinates: top edge, middle, and bottom edge
		yTop: b.y,
		yMiddle: b.y + b.height / 2,
		yBottom: b.y + b.height
	};


	// produce a 3x3 combination of the above to derive 9 x,y coordinates
	// center
	o.center = {x: o.xCenter, y: o.yMiddle };

	// edges
	o.topLeft = {x: o.xLeft, y: o.yTop };
	o.topRight = {x: o.xRight, y: o.yTop };
	o.bottomLeft = {x: o.xLeft, y: o.yBottom };
	o.bottomRight = {x: o.xRight, y: o.yBottom };

	// corners
	o.top = {x: o.xCenter, y: o.yTop };
	o.bottom = {x: o.xCenter, y: o.yBottom };
	o.left = {x: o.xLeft, y: o.yMiddle };
	o.right = {x: o.xRight, y: o.yMiddle };

	// shortcuts to get the offset of paper's canvas
	o.offset = $(this.paper.canvas).parent().offset();

	return o;
};


/**
 * Make Element draggable
 */
Raphael.el.draggable = function (options)
{
	$.extend(true, this, {
		margin: 0
	},options || {});

	/**
	 * Executed when the shape's drag starts
	 */
	var onStart = function () {
		this.o().toFront();
		this.animate({"fill-opacity": 0.7}, 100);
	};

	/**
	 * Executed when the shape moves
	 */
	var onMove = function (dx, dy, mx, my, ev) {
		var b = this.getABBox();
		var px = mx - b.offset.left,
		py = my - b.offset.top,
		x = this.ox + dx,
		y = this.oy + dy,
		r = this.is('circle') ? b.width / 2 : 0;

		// Clamp shape to canvas
		var x = Math.min(Math.max(0 + this.margin + (this.is('circle') ? r : 0), x),
			this.paper.width - (this.is('circle') ? r : b.width) - this.margin);
		
		var y = Math.min(Math.max(0 + this.margin + (this.is('circle') ? r : 0), y),
			this.paper.height - (this.is('circle') ? r : b.height) - this.margin);

		var pos = { x: x, y: y, cx: x, cy: y };
			this.attr(pos);
	};

	/**
	 * Executed when the shape's drag ends
	 */
	var onEnd = function () {
		this.animate({"fill-opacity": 1.0}, 100);
	};

	this.drag(onMove, onStart, onEnd);

	return this;
};


/**
 * Make Set draggable
 */
Raphael.st.draggable = function (options) { 
    for (var i in this.items) this.items[i].draggable(options); 
    return this;
};

/**
 * Create a canvas
 * container HTML element that the canvas will go in
 * width Width of the canvas in pixels
 * height Height of the canvas in pixels
 */
function Canvas(container, width, height) {	
	// Create canvas with Raphael with given arguments
	var paper = Raphael(container, width, height);

	var elements = new Array();

	/**
	 * Set the background color of the canvas
	 * color Hex value of the color, i.e., '#A8A8A8'
	 */
	this.setBackground = function(color) {
		paper.canvas.style.backgroundColor = color;
	}

	this.getNumberOfElements = function() {
		return elements.length;
	}

	/**
	 * Style a shape with the default styling
	 * shape The shape to be styled
	 */
	var styleShape = function(shape) {
		shape.attr("fill", "#FFF");
		shape.attr("stroke", "#000");
		shape.attr("stroke-width", "1px");
	}

	/** 
	 * Add a process element to the canvas at the given location
	 * x Coordinate in pixels
	 * y Coordinate in pixels
	 */
	this.addProcess = function(x,y) {
		var c = paper.circle(x,y,25);
		styleShape(c);

		c.draggable();

		elements.push(c);

		return c;
	}

	/** 
	 * Add a multi-process element to the canvas at the given location
	 * x Coordinate in pixels
	 * y Coordinate in pixels
	 */
	this.addMultiProcess = function(x,y) {
		var st = paper.set();
		var c1 = paper.circle(x,y,25);
		
		var c2 = paper.circle(x,y,18);
		
		st.push(c1,c2);
		styleShape(st);

		elements.push(st);
		
		return st;
	}

	/** 
	 * Add a datastore element to the canvas at the given location
	 * x Coordinate in pixels
	 * y Coordinate in pixels
	 */
	this.addDatastore = function(x,y) {
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

		elements.push(st);
		
		return st;
	}

	/** 
	 * Add a external interactor element to the canvas at the given location
	 * x Coordinate in pixels
	 * y Coordinate in pixels
	 */
	this.addExtInteractor = function(x,y) {
		var r = paper.rect(x - 25,y - 25,50,50);
		styleShape(r);

		r.draggable();

		elements.push(r);

		return r;
	}
}

