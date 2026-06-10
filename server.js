const http = require('node:http');
// const view = '/view/template.html';
const fs = require('node:fs');
const path = require('node:path');

const hostname = process.env.SERV_ADDRESS;
const port = process.env.SERV_PORT;

const reactDir = '/view/static/at.js';

const serv = http.createServer((req, res) => {
    console.log(`req.url = ${req.url}`);
    
    let conType = 'text/html';
    let dir = '/view/template.html';
    
    if (req.url == reactDir) {
        conType = 'text/javascript';
        dir = reactDir;
    }
    
    fs.readFile(dir, (err, data)=> {
        if (err) {console.log(err.message)
            res.writeHead(500, {'content-Type': 'text/plain'});
            return res.end();
        };
        res.writeHead(200, {'Content-Type': conType});
        res.end(data);
    });
});
serv.listen(port, hostname, () => {
    console.log(
        `server running at ${hostname} on port ${port} from wsl `
    );
});