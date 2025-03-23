import Pusher from 'pusher-js';

const pusher = new Pusher('c59e8a8c8980e404fb73', {
    cluster: 'ap1',
    encrypted: true,
});

export default pusher;