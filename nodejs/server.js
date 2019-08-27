'use strict'

const express = require('express')
const bodyParser = require('body-parser')
var backstop = require('./runner')
// Create a new instance of express
const app = express()

// Tell express to use the body-parser middleware and to not parse extended bodies
app.use(bodyParser.urlencoded({ extended: false }))
app.use(express.static('backstop_data'));

// Route that receives a POST request to /sms
app.get('/', function (req, res) {
  let response = backstop.runner({
    'jsonUrl': req.query.jsonUrl,
    'command': req.query.command,
    'scenario': req.query.scenario
  });
  res.set('Content-Type', 'text/plain')
  res.send('Patience... robots are doing work. ')
});

// Like an idiot would set on their luggage
app.listen(3000, function (err) {
  if (err) {
    throw err
  }

  console.log('Server started on port 3000')
})