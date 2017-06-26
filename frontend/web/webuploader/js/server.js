const http = require('http');

const hostname = '127.0.0.1';
//const port = 8080;

const server = http.createServer((req, res) => {
  res.statusCode = 200;
  res.setHeader('Content-Type', 'text/plain');
  res.end('Hello,NodeJs\n');
});

server.listen(port, hostname, () => {
  console.log(`Server running at http://${hostname}:${port}/`);
  //console.log(`Server running at http://${hostname}/`);
});

/*var http = require('http');

var server = http.createServer(function(req, res){
	res.writeHead(200, {'Content-Type': 'text/plain'})
	res.end('hello world')
})
server.listen(1337, '127.0.0.1')

console.log(`Server running at http://127.0.0.1:1337/`)*/