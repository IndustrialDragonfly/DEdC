/**
* Connector will handel all Ajax code
*/
define(["modules/response", "jquery"], function (Response, $) {
    
   /**
    * Ajax GET method
    * @param {String} url Url of resource
    * @param {Function} successCallback Function called if a request executes successfully
    * @param {Function} failCallback Function called if a request does not execute successfully
    */
   var publicGet = function (url, successCallback, failCallback) {
       $.ajax({
           accepts: "application/json",
           url: url,
           dataType: "text"
       }).done(function (data, textStatus) {
           // Request was successful
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
       $.ajax({
           type: "DELETE",
           accepts: "application/json",
           url: url,
           dataType: "text"
       }).done(function (data, textStatus) {
           // Request was successful
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
                response.setError("DELETE " + url + " " + data);
                
                failCallback(response);
           }
       }).fail(function (jqXHR, textStatus, errorThrown) {
           // Request failed for some reason
           var response = new Response();
           
           response.setData(errorThrown);
           response.setStatus(textStatus);
           response.setError("DELETE " + url + " " + jqXHR.status + " (" + jqXHR.statusText + ")");

           failCallback(response);
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
       
       var dataString = JSON.stringify(data);
       console.log("Sending data: " + dataString);
       $.ajax({
           type: "PUT",
           url: url,
           data: dataString,
           processData: false, // Send in body, not as a query string
           accepts: "application/json",
           dataType: "text",
           async: async
       }).done(function (data, textStatus) {
           console.log("Success from jQuery");
           // Request was successful
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
           console.log("Failure from jQuery");
           // Request failed for some reason
           var response = new Response();
           
           response.setData(errorThrown);
           response.setStatus(textStatus);
           response.setError("PUT " + url + " " + jqXHR.status + " (" + jqXHR.statusText + ")");

           failCallback(response);
       });
   };

   return {
       get: publicGet,
       delete: publicDelete,
       put: publicPut
   };
});