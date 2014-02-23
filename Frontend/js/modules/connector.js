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
                response.setError("GET " + url + " " + data);
                
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
    * @param {type} url Url of resource
    * @param {type} data String or plain object that will be sent
    * @param {type} successCallback Callback to execute on success
    * @param {type} failCallback Callback to execute on failure
    */
   var publicPut = function(url, data, successCallback, failCallback) {
       var dataString = JSON.stringify(data);
       console.log("Sending data: " + dataString);
       $.ajax({
           type: "PUT",
           url: url,
           data: dataString,
           processData: false,
           dataType: "applications/json"
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