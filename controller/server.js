const http = require('node:http');
const view = '/view/template.html';
const fs = require('node:fs');
const path = require('node:path');

const hostname = process.env.SERV_ADDRESS;
const port = process.env.SERV_PORT;

const response = await fetch('/view/template.html')

const dir = path.dirname('');

const serv = http.createServer((req, res) => {
    fs.readFile()
    res.statusCode = 200;
    res.setHeader('Content-Type', 'text/plain');
    res.end(response);
});
serv.listen(port, hostname, () => {
    console.log(
        `server running at ${hostname} on port ${port} from wsl `
    );
});