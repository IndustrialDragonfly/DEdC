/**
* Connector will handel all Ajax code
*/
define(["modules/response", "jquery"], function (Response, $) {
    
    var myOrganization = "",
        myUsername = "",
        myPassword = "",
        queryStringFormat = "?orgUser=#{org}/#{username}&password=#{password}&authType=StandardUsernamePassword";

   /**
    * Ajax GET method
    * @param {String} url Url of resource
    * @param {Function} successCallback Function called if a request executes successfully
    * @param {Function} failCallback Function called if a request does not execute successfully
    * @param {Bool} async (Optional) If true, the request will be sent asynchronously, false otherwise. Defaults to true.
    */
   var publicGet = function (url, successCallback, failCallback, async) {
        // Handle optional argument, defaults to true
        async = (typeof async === "undefined") ? true : async;
       
       // Create authentication query string
        var queryString = queryStringFormat
            .replace(/#\{org\}/g, myOrganization)
            .replace(/#\{username\}/g, myUsername)
            .replace(/#\{password\}/g, myPassword);

       $.ajax({
           accepts: "application/json",
           url: url + queryString,
           dataType: "text",
           async: async
       }).done(function (data, textStatus) {
           // Request was successful
           console.log("Received data: " + data);
           var jsonData = false;
           var exception = false;
           try {
               jsonData = JSON.parse(data);
           } catch (e) {
               exception = e;
           }
           
           if (jsonData && !exception) {
               // Data received was successfully parsed as JSON
                var response = new Response();

                response.setData(jsonData);
                if (textStatus) {
                     response.setStatus(textStatus);
                }
                successCallback(response);
           } else {
               // JSON was not parsed successfully
                var response = new Response();

                // Data was received, but was not JSON
                if (data) {
                    response.setData(data);
                }
                response.setStatus(textStatus);
                response.setError("GET " + url + " " + data);
                
                failCallback(response);
           }
       }).fail(function (jqXHR, textStatus, errorThrown) {
           // Request failed for some reason
           var response = new Response();
           
           response.setData(errorThrown);
           response.setStatus(textStatus);
           response.setError("GET " + url + " " + jqXHR.status + " (" + jqXHR.statusText + ")");

           failCallback(response);
       });
   };

   /**
    * Ajax DELETE method
    * @param {type} url Url of resource
    * @param {type} successCallback Callback to execute on success
    * @param {type} failCallback Callback to execute on failure
    */
   var publicDelete = function (url, successCallback, failCallback) {
        // Create authentication query string
        var queryString = queryStringFormat
            .replace(/#\{org\}/g, myOrganization)
            .replace(/#\{username\}/g, myUsername)
            .replace(/#\{password\}/g, myPassword);

       $.ajax({
           type: "DELETE",
           accepts: "application/json",
           url: url + queryString,
           dataType: "text"
       }).done(function (data, textStatus) {
           // Request was successful
           console.log("Received data: " + data);
           var jsonData = false;
           var exception = false;
           try {
               jsonData = JSON.parse(data);
           } catch (e) {
               exception = e;
           }
                      
           if (jsonData) {
               // Data received was successfully parsed as JSON
                var response = new Response();

                response.setData(jsonData);
                if (textStatus) {
                     response.setStatus(textStatus);
                }
                if (successCallback) {
                	successCallback(response);
                }
           } else {
               // JSON was not parsed successfully
                var response = new Response();

                // Data was received, but was not JSON
                if (data) {
                    response.setData(data);
                }
                response.setStatus(textStatus);
                
                // This is not a failure case because we don't expect anything 
                // back from the request.
                if (successCallback) {
                	successCallback(response);
                }
           }
       }).fail(function (jqXHR, textStatus, errorThrown) {
           // Request failed for some reason
           var response = new Response();
           
           response.setData(errorThrown);
           response.setStatus(textStatus);
           response.setError("DELETE " + url + " " + jqXHR.status + " (" + jqXHR.statusText + ")");

           if (failCallback) {
        	   failCallback(response);
           }
       });
   };

   /**
    * Ajax PUT method
    * @param {String} url Url of resource
    * @param {String} data String or plain object that will be sent
    * @param {Function} successCallback Callback to execute on success
    * @param {Funtion} failCallback Callback to execute on failure
    * @param {Bool} async (Optional) If true, the request will be sent asynchronously, false otherwise. Defaults to true.
    */
   var publicPut = function(url, data, successCallback, failCallback, async) {
       // Handle optional argument, defaults to true
       async = (typeof async === "undefined") ? true : async;
          // Create authentication query string
        var queryString = queryStringFormat
            .replace(/#\{org\}/g, myOrganization)
            .replace(/#\{username\}/g, myUsername)
            .replace(/#\{password\}/g, myPassword);
     
       var dataString = JSON.stringify(data);
       console.log("Sending data: " + dataString);
       $.ajax({
           type: "PUT",
           url: url + queryString,
           data: dataString,
           processData: false, // Send in body, not as a query string
           accepts: "application/json",
           dataType: "text",
           async: async
       }).done(function (data, textStatus) {
           // Request was successful
           console.log("Received data: " + data);
           var jsonData = false;
           var exception = false;
           try {
               jsonData = JSON.parse(data);
           } catch (e) {
               exception = e;
           }
           
           if (jsonData && !exception) {
               // Data received was successfully parsed as JSON
                var response = new Response();

                response.setData(jsonData);
                if (textStatus) {
                     response.setStatus(textStatus);
                }
                successCallback(response);
           } else {
               // JSON was not parsed successfully
                var response = new Response();

                // Data was received, but was not JSON
                if (data) {
                    response.setData(data);
                }
                response.setStatus(textStatus);
                response.setError("PUT " + url + " " + data);
                
                failCallback(response);
           }
       }).fail(function (jqXHR, textStatus, errorThrown) {
           // Request failed for some reason
           var response = new Response();
           
           var jsonData = false;
           var exception = false;
           try {
               jsonData = JSON.parse(jqXHR.responseText);
           } catch (e) {
               exception = e;
           }
           if (exception) {
        	   response.setData(jqXHR.responseText);
           } else {
               response.setData(jsonData.Error);
           }

           response.setStatus(textStatus);
           response.setError("PUT " + url + " " + jqXHR.status + " (" + jqXHR.statusText + ")");

           failCallback(response);
       });
   };
   
   var publicSetCredentials = function (organization, username, password) {
       myOrganization = organization;
       myUsername = username;
       myPassword = password;
   };

   return {
       get: publicGet,
       delete: publicDelete,
       put: publicPut,
       setCredentials: publicSetCredentials
   };
});