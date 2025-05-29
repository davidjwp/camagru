const http = require('http');
const fs = require('fs');

const hostname = '127.0.0.1';
const port = 8000;

http.createServer((req, res) => {
    fs.readFile('index.html', function (err, data) {
        res.writeHead(200, {'Content-Type': 'text/html'});
        res.write(data);
        return res.end();
    });
}).listen(port, hostname, () => {
    console.log(`server is listening`);
});
