# Qualesk
A real-time sensor array and hardware management and visualization system with a web-based front-end

## Copyright and Usage
All code and resources, excluding Node.js modules, are copyright Reece Aaron Lecrivain 2019.
You are free to use and modify the project without warranty in both commercial and non-commercial environments as long as credit is given.

## Fundamental Capabilities
* Real-time visualisation of remote sensor data
* Mass storage of remote sensor data
* Write once, use anywhere design
* Modular front-end and back-end
* Logging system
* Conditional events system
* Mapping system
* Users and permissions system
* Installation wizard

## Network Topology
A Qualesk network consists of arrays of sensors, a data and processing server, a database server, a web server and connected clients as shown below
![Qualesk Network Diagram](https://user-images.githubusercontent.com/820781/57197622-7cee1780-6f61-11e9-8723-078724867554.png)

## Concepts
### Systems, Sensor Arrays and Sensors
A Qualesk network is broken down in to three seperate components:
* Systems
* Sensor Arrays
* Sensors

A system can contain many sensor arrays and each sensor array can contain many sensors as shown below
![Venn Diagram](https://user-images.githubusercontent.com/820781/57198049-61394000-6f66-11e9-8028-37c5d8c3ca56.png)

A system is a system of related sensor arrays. An example of a system is a river; it may have multiple sensor arrays deployed at different points along it.

A sensor array is an array of sensors. An example of a sensor array, relating to our previous example, is a sensor array halfway down the river system.

A sensor is a bit of hardware capable of taking readings from the outside environment. An example of a sensor is a temperature sensor.

## Prerequisites
### Sensor Arrays
It is possible to run any operating system and self-made software capable of sending the JSON data format to a remote WebSocket on the sensor arrays. In this case, a Raspberry Pi running the Raspian operating system was used. The Qualesk software built to run on the sensor arrays uses the Node.js run-time environment and makes use of NPM packages.

### Data and Processing Server
The data and processing server code uses the Node.js run-time environment and makes use of NPM packages.

### Database Server
MySQL Community Server is used as the database server software.

### Web Server
Apache HTTP Server 2 with PHP 7 is used as the web server. Please note that the routing and PHP module of Apache Server must be enabled. The MySQLi module of PHP must also be enabled for Qualesk to run.

### Clients
Any client with modern web capabilities, such as WebSockets, should be compatible with the Qualesk system.

## Data Format
Data is sent in JSON format from:
* Sensor arrays to data and processing server
* Data and processing server to clients

## Sensor Array Data Transfer
Two different types of packets are sent from the sensor arrays to the data and processing server:
* An initial packet which tells the data and processing server the parameters of the sensor array
* A packet which contains sensor readings

A sensor array sends data on connection in the JSON format, to the data and processing server, which defines what the sensor is and what data the server should expect from it. The data and processing server is the responsible for ensuring the correct database tables and data exists relating to the sensor; non-existant tables and definitions are added automatically if they don't currently exist.

Each time a sensor reading is conducted it is sent in the JSON format, to the data and processing server, which contains a timestamp and the reading data. The data and processing server then does any required processing on the data and adds the data to the database.
