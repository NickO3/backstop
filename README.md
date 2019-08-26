# Overview 
This module is designed to help make it quick, and easy to create backstop tests for Drupal sites. Some major features: 
1. Ability to create multiple tests using a combination of menus, node types, and custom paths. 
2. Ability to override default backstop json globally, and per test. See options: 
3. Ability to override test and reference urls globally or per test. 
4. Tests are config entities, and 100% exportable. 

# Quickstart
1. Install module and navigate to /admin/config/development/backstop/backstop_scenario/config
2. Enter your default test and reference urls
3. Create your first test! I recommend keeping it small at first. Note the name of the test will be the name you will use later in the command line. /admin/config/development/backstop/backstop_scenario/add
4. Run "npm install -g backstopjs" 
5. cd <pathtomodule>/backstopjs and run "npm install" 
6. Create a reference using your local install's json: 'node backstop.js reference --uri="http://<yourlocaldrupalsite>"'
7. Now run a test! 'node backstop.js test --uri="http://<yourlocaldrupalsite>"'


