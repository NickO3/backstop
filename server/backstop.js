const _ = require('lodash');
const request = require('request');
const args = require('yargs').argv;
const backstop = require('backstopjs');
let myArgs = process.argv.slice(2);
let commandToRun = myArgs[0];
let scenarioName = myArgs[1];
let queryParams = {};
if (args.testUrl) {
    queryParams.testUrl = args.testUrl;
}
if (args.referenceUrl) {
    queryParams.referenceUrl = args.referenceUrl;
}
var jsonObject = request({ url: args.url + '/backstop/' + scenarioName, qs: queryParams},  function (error, response, body) {
    if( "" !== commandToRun ) {
        backstop(commandToRun, { config: JSON.parse(body)});
    }
});




