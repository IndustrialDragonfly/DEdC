function Canvas(container, width, height) {
	// Set internal variables
	this.container = container;
	this.width = width;
	this.height = height;
	
	// Create canvas with Raphael
	this.paper = Raphael("tab1", 640, 480);
	this.paper.canvas.style.backgroundColor = '#A8A8A8';
}

// Add a process element to the canvas
Canvas.prototype.addProcess = function(x,y) {
	var c = this.paper.circle(x,y,25);
	c.attr("fill", "#FFF");
	c.attr("stroke", "#000");
	return c;
}

// Add a multi-process element to the canvas
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

// Add a datastore element to the canvas
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
	
	/*st.push(p1,p2,rec);*/
	
	return p1;
}

// Add a external interactor element to the canvas
Canvas.prototype.addExtInteractor = function(x,y) {
	var c = this.paper.rect(x - 25,y - 25,50,50);
	c.attr("fill", "#FFF");
	c.attr("stroke", "#000");
	return c;
}
