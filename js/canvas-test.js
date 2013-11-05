module("Canvas");

/**
 * Test to make sure the canvas was created
 */
test("Canvas Creation", function()
{
	notEqual(canvas, undefined, "Test if canvas is undefined");
	notEqual(canvas, null, "Test if canvas is null");
});

/**
 * Test adding a process then removing it
 */
test("Add Process", function() 
{
	equal(canvas.getNumberOfElements(), 0, "Test with no elements");

	var p = canvas.addProcess(100, 100);

	equal(canvas.getNumberOfElements(), 1, "Test with 1 element");

	notEqual(p, undefined, "Test if element is undefined");
	notEqual(p, null, "Test if element is null")

	canvas.remove(p);
	equal(canvas.getNumberOfElements(), 0, "Test after remove");
});

/**
 * Test adding a multi-process then removing it
 */
test("Add Multi-process", function() 
{
	equal(canvas.getNumberOfElements(), 0, "Test with no elements");

	var p = canvas.addMultiProcess(100, 100);

	equal(canvas.getNumberOfElements(), 1, "Test with 1 element");

	notEqual(p, undefined, "Test if element is undefined");
	notEqual(p, null, "Test if element is null")

	canvas.remove(p);
	equal(canvas.getNumberOfElements(), 0, "Test after remove");
});

/**
 * Test adding a datastore then removing it
 */
test("Add Datastore", function() 
{
	equal(canvas.getNumberOfElements(), 0, "Test with no elements");

	var p = canvas.addDatastore(100, 100);

	equal(canvas.getNumberOfElements(), 1, "Test with 1 element");

	notEqual(p, undefined, "Test if element is undefined");
	notEqual(p, null, "Test if element is null")

	canvas.remove(p);
	equal(canvas.getNumberOfElements(), 0, "Test after remove");
});

/**
 * Test adding an external interactor then removing it
 */
test("Add External Interactor", function() 
{
	equal(canvas.getNumberOfElements(), 0, "Test with no elements");

	var p = canvas.addExtInteractor(100, 100);

	equal(canvas.getNumberOfElements(), 1, "Test with 1 element");

	notEqual(p, undefined, "Test if element is undefined");
	notEqual(p, null, "Test if element is null")

	canvas.remove(p);
	equal(canvas.getNumberOfElements(), 0, "Test after remove");
});

/**
 * Test adding and removing multiple elements
 */
test("Num Elements", function()
{
	equal(canvas.getNumberOfElements(), 0, "Test with 0 elements");

	var e1 = canvas.addExtInteractor(100, 100);
	equal(canvas.getNumberOfElements(), 1, "Test with 1 element");

	var e2 = canvas.addDatastore(100, 100);
	equal(canvas.getNumberOfElements(), 2, "Test with 2 elements");

	var e3 = canvas.addMultiProcess(100, 100);
	equal(canvas.getNumberOfElements(), 3, "Test with 3 elements");

	var e4 = canvas.addProcess(100, 100);
	equal(canvas.getNumberOfElements(), 4, "Test with 4 elements");

	canvas.remove(e4);
	equal(canvas.getNumberOfElements(), 3, "Test with 3 elements");
	
	canvas.remove(e3);
	equal(canvas.getNumberOfElements(), 2, "Test with 2 elements");

	canvas.remove(e2);
	equal(canvas.getNumberOfElements(), 1, "Test with 1 elements");
	
	canvas.remove(e1);
	equal(canvas.getNumberOfElements(), 0, "Test with 0 elements");

});