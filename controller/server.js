const http = require('http');
const fs = require('fs');

const hostname = process.env.SERV_ADDRESS;
const port = process.env.SERV_PORT;

const serv = http.createServer((req, res) => {
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
