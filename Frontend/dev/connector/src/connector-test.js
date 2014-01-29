module("Connector");

/**
 * Test to make sure the Connector was created
 */
test("Connector Creation", function()
{
	notEqual(connector, undefined, "Test if connector is undefined");
	notEqual(connector, null, "Test if connector is null");
});

/**
 * Test getting an empty DFD
 */
test("Get Empty DFD", function()
{
	var response = connector.getDfd("example_data/null.json");

	equal(response.getData(), "{}", "Test data of response");
	equal(response.getStatus(), "success", "Test status of response");
	notEqual(response, undefined, "Test if response is undefined");
	notEqual(response, null, "Test if response is null");
});

/**
 * Test getting a DFD at a path that doesn't exist
 */
test("Get DFD (404 Error)", function()
{
	var response = connector.getDfd("some/path/that/doesnt/exist.json");
	equal(
		response.getError(),
		"GET some/path/that/doesnt/exist.json 404 (Not Found)",
		"Test if response is undefined"
	);
});