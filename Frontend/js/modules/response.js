/**
* Base Response for requests
* @constructor
*/
define(function() {

    return function Response() {
       var me = this;
       var myStatus = "";
       var myData = null;
       var myError = "";

       /**
        * Get the status of the Response
        * @return {String} Status given from jQuery
        */
       this.getStatus = function () {
           return myStatus;
       };

       /**
        * Set the status of the Response
        * @param {String} status Status text
        * @returns {Response} this, for chaining
        */
       this.setStatus = function (status) {
           myStatus = status;
           return me;
       };

       /**
        * Get the data returned from the server
        * @returns {String} Data as string from server
        */
       this.getData = function () {
           return myData;
       };

       /**
        * Set the status of the Response
        * @param {String} data Data
        * @returns {Response} this, for chaining
        */
       this.setData = function (data) {
           myData = data;
           return me;
       };

       /**
        * Get the error message if there was an error
        * @returns {String} Error message
        */
       this.getError = function () {
           return myError;
       };

       /**
        * Set the error message
        * @param {String} error Error message
        * @returns {Response} this, for chaining
        */
       this.setError = function (error) {
           myError = error;
           return me;
       };
    };
});