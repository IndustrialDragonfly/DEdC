/**
 * Make Raphael Set draggable
 * @param {Function} callback - Function to call when an object has been moved
 * @param {Element} element - Element that this Set belongs to
 */
Raphael.st.draggable = function(callback,element)
{
	// Cache Set so elements can use it
	var parent = this;
	var element = element;

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
		element.hasMoved = true;

		// Recalculate the Dataflows
		callback();
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

	// Element and Dataflow arrays
	var elements = new Array();
	var dataflows = new Array();

	// Curent selection
	var selection = new Array();
	var dataflowSelection = new Array();

	// Keyboard events
	var KEYSTATE = {
		DOWN : {value: 0, name: "Down", code: "D"},
		UP: {value:1, name: "Up", code: "U"}
	};

	var ctrlState = KEYSTATE.UP;

	$("body").keydown(function(e) {
		if (e.which == 17) // CTRL
		{
			ctrlState = KEYSTATE.DOWN;
		}
	});

	$("body").keyup(function(e) {
		if (e.which == 17) // CTRL
		{
			ctrlState = KEYSTATE.UP;
		}
	});

	/**
	 * Event called when an Element on the canvas is clicked
	 * It handles the selection of elements;
	 * @param {Element} element - Element that was clicked
	 */
	this.elementClicked = function(element)
	{
		if (ctrlState == KEYSTATE.UP) // CTRL is not pressed
		{
			// Replace selection
			this.unselectAllElements();
			selection.push(element);
			element.setSelected();
		}
		else // CTRL is pressed
		{
			var index = selection.indexOf(element);
			if (index < 0) // Not in selection, add it
			{
				selection.push(element);
				element.setSelected();
			}
			else // Element was selected, remove it
			{
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
	this.dataflowClicked = function(dataflow)
	{
		if (ctrlState == KEYSTATE.UP) // CTRL is not pressed
		{
			// Replace selection
			this.unselectAllDataflows();
			dataflowSelection.push(dataflow);
			dataflow.setSelected();
		}
		else // CTRL is pressed
		{
			var index = selection.indexOf(dataflow);
			if (index < 0) // Not in selection, add it
			{
				dataflowSelection.push(dataflow);
				dataflow.setSelected();
			}
			else // Element was selected, remove it
			{
				dataflowSelection.splice(index, 1);
				dataflow.setUnselected();
			}
		}
	};

	/**
	 * Unselect all of the elements on the canvas
	 */
	this.unselectAllElements = function()
	{
		var e = selection.pop();
		while(e)
		{
			e.setUnselected();
			e = selection.pop();
		}
	};

		/**
	 * Unselect all of the elements on the canvas
	 */
	this.unselectAllDataflows = function()
	{
		var e = dataflowSelection.pop();
		while(e)
		{
			e.setUnselected();
			e = dataflowSelection.pop();
		}
	};

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
	 * Set the size of the canvas
	 * @param {number} width - New width of the canvas in pixels
	 * @param {number} height - New height of the canvas in pixels
	 */
	this.setSize = function(width,height)
	{
		paper.setSize(width,height);
	}

	/** 
	 * Add a process element to the canvas at the given location
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 * @return {Process} Element on canvas
	 */
	this.addProcess = function(x,y)
	{
		var e = new Process(this,x,y);
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
		var e = new MultiProcess(this,x,y);
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
		var e = new Datastore(this,x,y);
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
		var e = new ExtInteractor(this,x,y);
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
		// Cannot connect to self
		if (source == target)
			return null;

		var d = new Dataflow(this,source,target);
		dataflows.push(d);
		return d;
	};

	/**
	 * Connect the current selection with Dataflows
	 */
	this.addDataflowFromSelection = function()
	{
		for (var i = 0; i < selection.length; i++)
		{
			if (selection[i+1])
				this.addDataflow(selection[i], selection[i+1]);
		}

		this.unselectAll();
	};

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
	};

	/**
	 * Recalculate all Dataflows in the canvas
	 * This is a performance killer at this time
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
	 * @param {Cavas} canvas - Canvas Object
	 */
	var Element = function(canvas)
	{
		var me = this;
		var set = paper.set(); // Raphael.Set for shapes
		var canvas = canvas; // Internal reference to canvas
		this.hasMoved = true; // Must initially be true, or dataflows will not draw when added

		/**
		 * Add a shape to the Element
		 * @param {Raphael.Shape} shape - Shape to add
		 */
		this.push = function(shape)
		{
			for (var i = 0; i < arguments.length; i++)
			{
				set.push(arguments[i]);
				arguments[i].mouseup(onMouseClick);
			}
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
			set.draggable(canvas.calcDataflows, me);
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

		/**
		 * Called when the Element is selected
		 */
		this.setSelected = function()
		{
			set.animate({"fill-opacity": 0.2}, 100);
		};

		/**
		 * Called when the Element is unselected
		 */
		this.setUnselected = function()
		{
			set.animate({"fill-opacity": 1.0}, 100);
		};

		/**
		 * Called when any Shape in the set is clicked
		 */
		var onMouseClick = function()
		{
			// Using "this" would result in the wrong object being used
			canvas.elementClicked(me);
		};
	};
	
	/**
	 * Create a Process element
	 * @constructor
	 * @augments Element
	 * @param {Cavas} canvas - Canvas Object
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 */
	var Process = function(canvas,x,y)
	{
		Element.call(this, canvas); // Call parent constructor

		this.push(paper.circle(x,y,25));
		this.applyDefaultStyle();
		this.draggable();
	};
	Process.prototype = new Element(); // Inherit Element
	Process.prototype.constructor = Process; // Fix the constructor pointer

	/**
	 * Create a MultiProcess element
	 * @constructor
	 * @augments Element
	 * @param {Cavas} canvas - Canvas Object
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 */
	var MultiProcess = function(canvas,x,y)
	{
		Element.call(this, canvas);

		this.push(paper.circle(x,y,25),paper.circle(x,y,18));
		this.applyDefaultStyle();
		this.draggable();
	};
	MultiProcess.prototype = new Element();
	MultiProcess.prototype.constructor = MultiProcess;

	/**
	 * Create a Datastore element
	 * @constructor
	 * @augments Element
	 * @param {Cavas} canvas - Canvas Object
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 */
	var Datastore = function(canvas,x,y)
	{
		Element.call(this, canvas);

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
	 * @augments Element
	 * @param {Cavas} canvas - Canvas Object
	 * @param {number} x - Coordinate in pixels
	 * @param {number} y - Coordinate in pixels
	 */
	var ExtInteractor = function(canvas,x,y)
	{
		Element.call(this, canvas);

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
	var Dataflow = function(canvas,source,target)
	{
		var me = this;
		var source = source;
		var target = target;
		var canvas = canvas;
		var path;
		var arrow;

		// Make sure this dataflow will get drawn
		target.hasMoved = true;

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
		 * Called when the Element is selected
		 */
		this.setSelected = function()
		{
			path.animate({"stroke-width": 5}, 100);
		};

		/**
		 * Called when the Element is unselected
		 */
		this.setUnselected = function()
		{
			path.animate({"stroke-width": 3}, 100);
		};

		/**
		 * Called when any Shape in the set is clicked
		 */
		var onMouseClick = function()
		{
			// Using "this" would result in the wrong object being used
			canvas.dataflowClicked(me);
		};

		/**
		 * Calculate Dataflow's path as the minium between the two Elements
		 */
		this.calcPath = function() {
			if (!source.hasMoved || !target.hasMoved)
				return;

			var sourcePoint = getAttachPoints(source);
			var targetPoint = getAttachPoints(target);
			var sourceIndex = 0; // Shortest point index for source
			var targetIndex = 0; // Shortest point index for source
			var min; // Minimum length

			// Loop through all of the attach points for both Elements
			for (var i = 0; i < sourcePoint.length; i++)
			{
				for (var j = 0; j < targetPoint.length; j++)
				{
					// Calculate vector's length (sqrt(a^2 + b^2))
					var dx = Math.abs(sourcePoint[i].x - targetPoint[j].x);
					var dy = Math.abs(sourcePoint[i].y - targetPoint[j].y);
					var length = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2));
					if (min)
					{
						// Check if new vector is minimum
						if (length < min)
						{
							sourceIndex = i;
							targetIndex = j;
							min = length;
						}
					} 
					else // No previous minimum existed
					{
						sourceIndex = i;
						targetIndex = j;
						min = length;
					}
				}
			}
			var pathString = "M" + sourcePoint[sourceIndex].x + " " + sourcePoint[sourceIndex].y + " L" + targetPoint[targetIndex].x + " " + targetPoint[targetIndex].y + " Z";

			var angle = Math.atan2(targetPoint[targetIndex].x - sourcePoint[sourceIndex].x, targetPoint[targetIndex].y - sourcePoint[sourceIndex].y);
			angle = (angle / (2 * Math.PI)) * 360;
			var arrowString = "M" + targetPoint[targetIndex].x + " " + targetPoint[targetIndex].y + " L" + (targetPoint[targetIndex].x - 5) + " " + (targetPoint[targetIndex].y - 5) + " L" + (targetPoint[targetIndex].x - 5) + " " + (targetPoint[targetIndex].y + 5) + " L" + targetPoint[targetIndex].x + " " + targetPoint[targetIndex].y;
			var arrowRotationString = "r" + ((-90+angle)*-1) + "," + targetPoint[targetIndex].x + "," + targetPoint[targetIndex].y;

			if (path && arrow)
			{
				// Path existed, update
				path.attr({path: pathString});
				arrow.attr({path: arrowString});
				arrow.transform(arrowRotationString);
			}
			else
			{
				if (path)
					path.remove();
				if (arrow)
					arrow.remove();

				// Path did not exist, create
				path = paper.path(pathString).attr("stroke-width", 3);
				path.mouseup(onMouseClick);
				arrow = paper.path(arrowString).attr("fill","black");
				arrow.transform(arrowRotationString);
			}
		};

		// Initially calculate the path
		this.calcPath();
	};
};