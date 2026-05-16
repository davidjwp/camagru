const http = require('node:http');
// const view = '/view/template.html';
const fs = require('node:fs');
const path = require('node:path');

const hostname = process.env.SERV_ADDRESS;
const port = process.env.SERV_PORT;

const dir = '/view/template.html';
const reactDir = 'view/static/at.js';
let FilRead;
let cType = 'text/html';

const serv = http.createServer((req, res) => {
    if (req.url == reactDir) {
        FilRead = dir;
        cType = 'text/javascript';
    }
    else FilRead = reactDir;

    fs.readFile(FilRead, (err, data)=> {
        if (err) {console.log(err.message)
            res.writeHead(500, {'content-Type': 'text/plain'});
            res.end();
        };
        res.writeHead(200, {'Content-Type': cType});
        // console.log(req.url);
        // console.log(cType);
        // console.log(dir);
        res.end(data);
    });
});
serv.listen(port, hostname, () => {
    console.log(
        `server running at ${hostname} on port ${port} from wsl `
    );
});