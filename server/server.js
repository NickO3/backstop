'use strict'

const express = require('express')
const bodyParser = require('body-parser')
var backstop = require('./runner')
// Create a new instance of express
const app = express()

var port = process.env.PORT || 3000;

// configure the app to use bodyParser()
app.use(bodyParser.urlencoded({
    extended: true
}));
app.use(bodyParser.json());
app.use(express.static('backstop_data'));
var jsonParser = bodyParser.json()
// Route that receives a POST request to /sms
app.get('/', function (req, res) {
  if(req.query.jsonUrl && req.query.command && req.query.scenario) {
    let response = backstop.runner({
      'jsonUrl': req.query.jsonUrl,
      'command': req.query.command,
      'scenario': req.query.scenario
    });
    res.set('Content-Type', 'text/plain')
    res.send('Patience... robots are doing work. ')
  }
  else {
    res.set('Content-Type', 'text/plain')
    res.send('Nothing sent.')
  }

});
// Route that receives a POST request to /sms
app.post('/run/:command', jsonParser, function (req, res) {
  let response = backstop.runner({
    'json': req.body,
    'command': req.params.command
  });
  res.set('Content-Type', 'text/plain')
  res.send('Patience... robots are doing work. ')
});
// Like an idiot would set on their luggage
app.listen(port, function (err) {
  if (err) {
    throw err
  }
  console.log('Server started on port 3000')
})