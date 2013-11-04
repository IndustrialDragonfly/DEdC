module("Canvas");

test("Canvas Creation", function()
{
	notEqual(canvas, undefined, "Test if canvas is undefined");
	notEqual(canvas, null, "Test if canvas is null");
});

test("Add Process", function() 
{
	var p = canvas.addProcess(100, 100);
	equal(p.x(), 100, "Test process x location");
	equal(p.y(), 100, "Test process y location");
	ok(!p.is('rect'), "Test if process is a rectangle");
	ok(p.is('circle'), "Test if process is a circle");

	p.remove();
});

test("Add External Interactor", function() 
{
	var p = canvas.addExtInteractor(100, 100);
	equal(p.x(), 75, "Test process x location");
	equal(p.y(), 75, "Test process y location");
	ok(p.is('rect'), "Test if process is a rectangle");
	ok(!p.is('circle'), "Test if process is a circle");

	p.remove();
});

test("Advanced Bounding Box", function() 
{
	var x = 50;
	var y = 50;
	var p = canvas.addExtInteractor(50,50);
	var b = p.getABBox();

	equal(b.x, 25, "top left corner x");
	equal(b.y, 25, "top left corner y");
	equal(b.width, 50, "width");
	equal(b.height, 50, "height");

	equal(b.xLeft, 25, "left edge");
	equal(b.xCenter, 50, "center x");
	equal(b.xRight, 75, "right edge");

	equal(b.yTop, 25, "top edge");
	equal(b.yMiddle, 50, "middle y");
	equal(b.yBottom, 75, "bottom edge");

	deepEqual(b.center, {x: 50, y: 50}, "center point");
	deepEqual(b.topLeft, {x: 25, y: 25}, "top left point");
	deepEqual(b.topRight, {x: 75, y: 25}, "top right point");
	deepEqual(b.bottomLeft, {x: 25, y: 75}, "bottom left point");
	deepEqual(b.bottomRight, {x: 75, y: 75}, "bottom right point");

	deepEqual(b.top, {x: 50, y: 25}, "top center point");
	deepEqual(b.bottom, {x: 50, y: 75}, "bottom center point");
	deepEqual(b.left, {x: 25, y: 50}, "left middle point");
	deepEqual(b.right, {x: 75, y: 50}, "right middle point");

	p.remove();
});