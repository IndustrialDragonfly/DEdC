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
           dataType: "json" // Do not let jQuery automatically parse the JSON response
       }).done(function (data, textStatus) {
           // Request was successful
           successCallback(parseJson(data));
       }).fail(function (jqXHR, textStatus, errorThrown) {
           // Request failed for some reason
           var response = new Response();

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
           dataType: "json"
       }).done(function (data, textStatus) {
           successCallback(parseJson(data));
       }).fail(function (jqXHR, textStatus, errorThrown) {
           // Request failed for some reason
           var response = new Response();

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
       $.ajax({
           type: "PUT",
           url: url,
           data: dataString,
           dataType: "json",
           processData: false,
           contentType: "application/json"
       }).done(function (data, textStatus) {
           successCallback(parseJson(data));
       }).fail(function (jqXHR, textStatus, errorThrown) {
           // Request failed for some reason
           var response = new Response();

           response.setStatus(textStatus);
           response.setError("DELETE " + url + " " + jqXHR.status + " (" + jqXHR.statusText + ")");

           failCallback(response);

       });

   }

   /**
    * Parse a JSON object
    * @param {jsonObject} jsonObject JSON document that has been translated by jQuery to an Object
    * @param {String} textStatus Status text from jQuery.ajax
    */
   var parseJson = function (jsonObject, textStatus) {
       var response = new Response();

       response.setData(jsonObject);
       if (textStatus) {
            response.setStatus(textStatus);
       }

       return response;
   };

   return {
       get: publicGet,
       delete: publicDelete,
       put: publicPut
   };
});