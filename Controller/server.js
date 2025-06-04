const http = require('http');
const fs = require('fs');
const getenv = require('getenv');

const hostname = getenv('SERV_ADDRESS');
const port = getenv('SERV_PORT');

const serv = http.createServer((req, res) => {
    let buf;
    fs.readFile('./index.html', function (err, data) {
        if (err) throw "woopsie";
        res.writeHead(200, 'ok read');
        res.write(data);
        res.end();
    });
});

serv.listen(port, hostname, () => {
    console.log(
        `server running at ${hostname} on port ${port} from wsl `
    );
});
