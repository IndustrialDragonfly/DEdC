/**
 * Create a Connector
 * @constructor
 */
function Connector(canvas)
{
	var myCanvas = canvas;

	/**
	 * Get a DFD, and return it as plain text
	 * @param {String} url - Url of the DFD with no domain
	 * @return {Response} Response from the request
	 */
	this.get = function(url)
	{
		var response = new Response();
		
		$.ajax({
			accepts: "application/json",
			url: url,
			async: false,		// If async is true, response will be returned before query executes
			dataType: "json"	// Do not let jQuery automatically parse the JSON response
		}).done(function(data, textStatus) {
				// Request was successful
				response.setData(data);
				response.setStatus(textStatus);
		}).fail(function(jqXHR,textStatus,errorThrown) {
				// Request failed for some reason
				response.setStatus(textStatus);
				response.setError("GET " + url + " " + jqXHR.status + " (" + jqXHR.statusText + ")");
		});

		return response;
	};

	this.load = function(url)
	{
		//TODO: Handle errors
		//TODO: Use enum for types
		var response = this.get(url);

		response.getData().elements.forEach(function(entry) {
			if (entry.type === "process") {
				canvas.addProcess(entry.x, entry.y);
			}
			else if (entry.type === "multiprocess") {
				canvas.addMultiProcess(entry.x, entry.y);
			}
			else if (entry.type === "datastore") {
				canvas.addDatastore(entry.x, entry.y);
			}
			else if (entry.type === "extinteractor") {
				canvas.addExtInteractor(entry.x, entry.y);
			}
		});
	};

	/**
	 * Base Response for requests
	 * @constructor
	 */
	var Response = function()
	{
		var me = this;
		var myStatus = null;
		var myData = null;
		var myError = null;

		/**
		 * Get the status of the Response
		 * @return {String} Status given from jQuery
		 */
		this.getStatus = function()
		{
			return myStatus;
		};

		/**
		 * Set the status of the Response
		 * @param {String} Status
		 * @returns {Response} this, for chaining
		 */
		this.setStatus = function(status)
		{
			myStatus = status;
			return me;
		};

		/**
		 * Get the data returned from the server
		 * @returns {String} Data as string from server
		 */
		this.getData = function()
		{
			return myData;
		};

		/**
		 * Set the status of the Response
		 * @param {String} data Data
		 * @returns {Response} this, for chaining
		 */
		this.setData = function(data)
		{
			myData = data;
			return me;
		};

		/**
		 * Get the error message if there was an error
		 * @returns {String} Error message
		 */
		this.getError = function()
		{
			return myError;
		};

		/**
		 * Set the error message
		 * @param {String} error Error message
		 * @returns {Response} this, for chaining
		 */
		this.setError = function(error)
		{
			myError = error;
			return me;
		};

	};
}