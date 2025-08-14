const http = require('http');

const hostname = process.env.SERV_ADDRESS;
const port = process.env.SERV_PORT;

const serv = http.createServer((req, res) => {
    res.statusCode = 200;
    res.setHeader('Content-Type', 'text/plain');
    res.end('Hey there this is a new one\n');
});

serv.listen(port, hostname, () => {
    console.log(
        `server running at ${hostname} on port ${port} from wsl `
    );
});
