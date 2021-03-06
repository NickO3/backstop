const backstop = require('backstopjs');
const request = require('request');
module.exports = {
  runner: function(params) {
    let command = params.command;
    let url = params.jsonUrl;
    let scenario = params.scenario;
    let queryParams = {};
    console.log(params);
    if(params.jsonUrl) {
        var jsonObject = request({ url: url + '/backstop/' + scenario, qs: queryParams},  function (error, response, body) {
          if( "" !== command) {
              return backstop(command, { config: JSON.parse(body)});
          }

      });
    }
    else {
      return backstop(params.command, { config: params.json});
    }
  }
};





