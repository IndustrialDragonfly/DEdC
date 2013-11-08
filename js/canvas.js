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

		// Recalculate all of the Dataflows for the moved element
		canvas.calcDataflows();
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
	var dataflows = new Array();

	/**
	 * Set the background color of the canvas
	 * @param {string} color - Hex value of the color, i.e., '#A8A8A8'
	 */
	this.setBackground = function(color) 
	{
		paper.canvas.style.backgroundColor = color;
	};

	/**
	 * Get number of Elements on the canvas
	 * @return {number} Number of Elements
	 */
	this.getNumberOfElements = function()
	{
		return elements.length;
	};

	/**
	 * Get number of Dataflows on the canvas
	 * @return {number} Number of Dataflows
	 */
	this.getNumberOfDataflows = function()
	{
		return dataflows.length;
	};

	/** 
	 * Add a process element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {Process} Element on canvas
	 */
	this.addProcess = function(x,y)
	{
		var e = new Process(x,y);
		elements.push(e);

		return e;
	};

	/** 
	 * Add a multi-process element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {MultiProcess} Element on canvas
	 */
	this.addMultiProcess = function(x,y)
	{
		var e = new MultiProcess(x,y);
		elements.push(e);

		return e;
	};

	/** 
	 * Add a datastore element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {Datastore} Element on canvas
	 */
	this.addDatastore = function(x,y)
	{
		var e = new Datastore(x,y);
		elements.push(e);

		return e;
	};

	/** 
	 * Add a external interactor element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {ExtInteractor} Element on canvas
	 */
	this.addExtInteractor = function(x,y)
	{
		var e = new ExtInteractor(x,y);
		elements.push(e);

		return e;
	};

	/**
	 * Add a dataflow to the cavas between the two elements
	 * @param {Element} source - Source of the Dataflow
	 * @param {Element} target - Target of the Dataflow
	 * @return {Dataflow} Dataflow on the canvas
	 */
	this.addDataflow = function(source, target)
	{
		var d = new Dataflow(source,target);
		dataflows.push(d);

		return d;
	}

	/**
	 * Remove an element from the canvas
	 * @param {Element} element - Element to remove from the canvas
	 * @return {boolean} TRUE if the element was removed, FALSE otherwise
	 */
	this.removeElement = function(element)
	{
		var index = elements.indexOf(element);
		if (index > -1)
		{
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
	this.removeDataflow = function(dataflow)
	{
		var index = dataflows.indexOf(dataflow);
		if (index > -1)
		{
			dataflows.splice(index, 1);
			return true;
		}
		return false;
	}

	/**
	 * Recalculate all Dataflows in the canvas
	 */
	this.calcDataflows = function()
	{
		for (var i = 0; i < dataflows.length; i++)
		{
			dataflows[i].calcPath();
		}
	};

	/**
	 * Base Class for all Elements
	 * @constructor
	 */
	var Element = function()
	{
		var set = paper.set();

		/**
		 * Add a shape to the Element
		 * @param {Raphael.Shape} shape - Shape to add
		 */
		this.push = function(shape)
		{
			set.push(shape);
		};

		/**
		 * Get Set of Shapes
		 * @returns {Raphael.Set} Set of Shapes
		 */
		this.getSet = function()
		{
			return set;
		};

		/**
		 * Make the Element draggable, any elements added after 
		 * draggable is called will not function properly
		 */
		this.draggable = function()
		{
			set.draggable();
		};

		/**
		 * Style all of the Shapes with the default styling
		 */
		this.applyDefaultStyle = function()
		{
			set.attr("fill", "#FFF");
			set.attr("stroke", "#000");
			set.attr("stroke-width", "1px");
		};

		/**
		 * Get the bounding box for the Element
		 * @returns {Raphael.BBox}
		 */
		this.getBBox = function()
		{
			return set.getBBox();
		};
	};
	
	/**
	 * Create a Process element
	 * @constructor
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 */
	var Process = function(x,y)
	{
		Element.call(this); // Call parent constructor

		this.push(paper.circle(x,y,25));
		this.applyDefaultStyle();
		this.draggable();
	};
	Process.prototype = new Element(); // Inherit Element
	Process.prototype.constructor = Process; // Fix the constructor pointer

	/**
	 * Create a MultiProcess element
	 * @constructor
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 */
	var MultiProcess = function(x,y)
	{
		Element.call(this);

		this.push(paper.circle(x,y,25),paper.circle(x,y,18));
		this.applyDefaultStyle();
		this.draggable();
	};
	MultiProcess.prototype = new Element();
	MultiProcess.prototype.constructor = MultiProcess;

	/**
	 * Create a Datastore element
	 * @constructor
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 */
	var Datastore = function(x,y)
	{
		Element.call(this);

		x = x - 25;
		y = y - 25;

		this.push(
			paper.path("M" + x + " " + y + " L" + (x+50) + " " + y + " Z"),
			paper.path("M" + x + " " + (y+50) + " L" + (x+50) + " " + (y+50) + " Z")
			);
		this.applyDefaultStyle();
		
		var rec = paper.rect((x), (y+1), 50, 48);
		rec.attr("stroke-width", 0);
		rec.attr("fill", "#FFF");
		this.push(rec);
		
		this.draggable();
	};
	Datastore.prototype = new Element();
	Datastore.prototype.constructor = Datastore;

	/**
	 * Create a External-interactor element
	 * @constructor
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 */
	var ExtInteractor = function(x,y)
	{
		Element.call(this);

		this.push(paper.rect(x - 25,y - 25,50,50));
		this.applyDefaultStyle();		
		this.draggable();
	};
	ExtInteractor.prototype = new Element();
	ExtInteractor.prototype.constructor = ExtInteractor;

	/**
	 * Create a Dataflow from source to target
	 * @constructor
	 * @param {Element} source - Source of the Dataflow
	 * @param {Element} target - Target of the Dataflow
	 */
	var Dataflow = function(source,target)
	{
		var source = source;
		var target = target;
		var path;

		/**
		 * Get the attach points for an Element
		 * @param {Element} element - The element to get the attach points off
		 * @returns {Array} Array of four points (x,y)
		 */
		var getAttachPoints = function(element)
		{
			var bb = element.getBBox();
			var points = new Array();
			points.push({x: bb.x, y: bb.y + bb.height / 2}); // Left
			points.push({x: bb.x + bb.width / 2, y: bb.y}); // Top
			points.push({x: bb.x + bb.width, y: bb.y + bb.height / 2}); // Right
			points.push({x: bb.x + bb.width / 2, y: bb.y + bb.height}); // Bottom

			return points;
		};

		/**
		 * Calculate Dataflow's path as the minium between the two Elements
		 */
		this.calcPath = function() {
			var sP = getAttachPoints(source);
			var tP = getAttachPoints(target);
			var sIndex = 0; // Shortest point index for source
			var tIndex = 0; // Shortest point index for source
			var min; // Minimum length

			// Loop through all of the attach points for both Elements
			for (var i = 0; i < sP.length; i++)
			{
				for (var j = 0; j < tP.length; j++)
				{
					// Calculate vector's length (sqrt(a^2 + b^2))
					var dx = Math.abs(sP[i].x - tP[j].x);
					var dy = Math.abs(sP[i].y - tP[j].y);
					var length = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2));
					if (min)
					{
						// Check if new vector is minimum
						if (length < min)
						{
							sIndex = i;
							tIndex = j;
							min = length;
						}
					} 
					else // No previous minimum existed
					{
						sIndex = i;
						tIndex = j;
						min = length;
					}
				}
			}
			var pathString = "M" + sP[sIndex].x + " " + sP[sIndex].y + " L" + tP[tIndex].x + " " + tP[tIndex].y + " Z";

			if (path)
			{
				// Path existed, update
				path.attr({path: pathString});
			}
			else
			{
				// Path did not exist, create
				path = paper.path(pathString);
			}
		};

		// Initially calculate the path
		this.calcPath();
	};
};