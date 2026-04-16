
const http = require('http');

const server = http.createServer((req, res) => {
    res.writeHead(200, {'Content-Type': 'text/plain; charset=utf-8'});
    res.end('Привіт! Це мій перший сервер на Node.js');
});

server.listen(3000, () => {
    console.log('Сервер запущено на http://localhost:3000');
});