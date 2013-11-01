/**
 * Create a canvas
 * container HTML element that the canvas will go in
 * width Width of the canvas in pixels
 * height Height of the canvas in pixels
 */
function Canvas(container, width, height) {
	// Set internal variables
	this.container = container;
	this.width = width;
	this.height = height;
	
	// Create canvas with Raphael
	this.paper = Raphael("tab1", 640, 480);
}

/**
 * Set the background color of the canvas
 * color Hex value of the color, i.e., '#A8A8A8'
 */
Canvas.prototype.setBackground = function(color) {
	this.paper.canvas.style.backgroundColor = color;
}

/** 
 * Add a process element to the canvas at the given location
 * x Coordinate in pixels
 * y Coordinate in pixels
 */
Canvas.prototype.addProcess = function(x,y) {
	var c = this.paper.circle(x,y,25);
	c.attr("fill", "#FFF");
	c.attr("stroke", "#000");

	c.drag(onMove, onStart, onEnd);
	return c;
}

/** 
 * Add a multi-process element to the canvas at the given location
 * x Coordinate in pixels
 * y Coordinate in pixels
 */
Canvas.prototype.addMultiProcess = function(x,y) {
	var st = this.paper.set();
	var c1 = this.paper.circle(x,y,25);
	c1.attr("fill", "#FFF");
	c1.attr("stroke", "#000");
	
	var c2 = this.paper.circle(x,y,18);
	c2.attr("fill", "#FFF");
	c2.attr("stroke", "#000");
	
	st.push(c1,c2);
	
	return st;
}

/** 
 * Add a datastore element to the canvas at the given location
 * x Coordinate in pixels
 * y Coordinate in pixels
 */
Canvas.prototype.addDatastore = function(x,y) {
	x = x - 25;
	y = y - 25;
	var st = this.paper.set();
	var p1 = this.paper.path("M" + x + " " + y + " L" + (x+50) + " " + y + " Z");
	p1.attr("stroke", "#000");
	
	var p2 = this.paper.path("M" + x + " " + (y+50) + " L" + (x+50) + " " + (y+50) + " Z");
	p2.attr("stroke", "#000");
	
	var rec = this.paper.rect((x), (y+1), 50, 48);
	rec.attr("stroke-width", 0);
	rec.attr("fill", "#FFF");
	
	st.push(p1,p2,rec);
	
	return st;
}

/** 
 * Add a external interactor element to the canvas at the given location
 * x Coordinate in pixels
 * y Coordinate in pixels
 */
Canvas.prototype.addExtInteractor = function(x,y) {
	var r = this.paper.rect(x - 25,y - 25,50,50);
	r.attr("fill", "#FFF");
	r.attr("stroke", "#000");

	r.drag(onMove, onStart, onEnd);
	return r;
}

// onMove, onStart, and onEnd are outside until I know how to make work as private
// !Important! They should not be called manually
/**
 * Move an object
 */ 
onMove = function(dx,dy) {
	var att = this.type == "rect" ? {x: this.ox + dx, y: this.oy + dy} : {cx: this.ox + dx, cy: this.oy + dy};
	this.attr(att);
}

/**
 * Executed when an object's drag starts
 */
onStart = function() {
	// Mark starting position
	this.ox = this.type == "rect" ? this.attr("x") : this.attr("cx");
	this.oy = this.type == "rect" ? this.attr("y") : this.attr("cy");

	// Fade out the object
	this.animate({"fill-opacity": 0.2}, 100);
}

/**
 * Executed when an object's drag ends
 */
onEnd = function() {
	// Fade in the object
	this.animate({"fill-opacity": 1}, 100);
}