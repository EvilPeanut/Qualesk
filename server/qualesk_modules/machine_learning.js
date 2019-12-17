const brain = require('brain.js');

/*var net = new brain.NeuralNetwork();

net.train([{input: { temperature: 16, humidity: 0 }, output: { comfortable: true }},
			{input: { temperature: 24, humidity: 80 }, output: { comfortable: false }},
			{input: { temperature: 21, humidity: 40 }, output: { comfortable: true }},
			{input: { temperature: 25, humidity: 0 }, output: { comfortable: true }},
			{input: { temperature: 14, humidity: 80 }, output: { comfortable: false }},
			{input: { temperature: 42, humidity: 0 }, output: { comfortable: false }},
			{input: { temperature: 37, humidity: 10 }, output: { comfortable: false }}]);

var output = net.run({ temperature: 18, humidity: 0 });

console.log( net.run({ temperature: 18, humidity: 0 }) );
console.log( net.run({ temperature: 25, humidity: 100 }) );
console.log( net.run({ temperature: 30, humidity: 80 }) );*/

//
var learning = {};

learning.onSensorArrayDefinition = function( core, json ) {
	/*var connection = json[1];

	core.mysql_connection.query("SELECT * FROM waterqualitymanagement.`sensor_617e8c3f-eefb-4ef9-b3fa-a608ec883db3` WHERE sensor_uuid='aec6ee61-685a-4414-bcc6-93ab43d60b32' LIMIT 100;", ( err, result ) => {

		var trainingData = [];

		for ( var result_index in result ) {
			var reading = result[ result_index ];

			if ( result_index % 5 == 0 ) {
				trainingData[ parseInt( result_index / 5 ) ] = [];	
			}

			var segment = trainingData[ parseInt( result_index / 5 ) ];
			segment.push( reading.data );
		}

		const net2 = new brain.recurrent.LSTMTimeStep();

		console.log("Training net with", result.length, "data points");
		net2.train(trainingData);
		console.log("Net trained");

		console.log(net2.run([0.5674263224308408, 0.7728951382941169, -0.9799398135254856, 0.2808362496644032]));
	});*/
}
/*const trainingData = [
    [47.4,47.45,47.48,47.56,47.7],
    [47.42,47.47,47.54,47.66,47.72]
];

const net2 = new brain.recurrent.LSTMTimeStep();

net2.train(trainingData);*/

//console.log(net2.run([47.42,47.47,47.52]));

module.exports = learning;