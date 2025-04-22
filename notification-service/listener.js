const amqp = require('amqplib');
const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 3001 });
console.log('🟢 WebSocket server running on ws://localhost:3001');

let sockets = [];

wss.on('connection', (ws) => {
    sockets.push(ws);
    console.log('✅ New WebSocket client connected');

    ws.on('close', () => {
        sockets = sockets.filter((s) => s !== ws);
        console.log('Client disconnected');
    });
});

(async () => {
    const connection = await amqp.connect('amqp://user:password@localhost:5672');
    const channel = await connection.createChannel();

    const queue = 'notist';
    await channel.assertQueue(queue, { durable: true });

    console.log(`📥 Listening for messages from RabbitMQ queue: ${queue}`);
    channel.consume(queue, (msg) => {
        const content = msg.content.toString();
        console.log('💬 Received from RabbitMQ:', content);

        sockets.forEach(ws => ws.send(content));

        channel.ack(msg);
    });
})();
