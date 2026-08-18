// End-to-end WebSocket delivery-latency probe for KnowVerse / Laravel Reverb.
//
// Measures the time from a server-side broadcast to its receipt by a connected
// client, using the Pusher protocol that Reverb implements.
//
// Prerequisites:
//   npm install ws                      # one-time (not bundled)
//   php artisan reverb:start            # Reverb running on :8080
//
// Run (two terminals):
//   1)  VITE_REVERB_APP_KEY=<your key>  node benchmarks/ws-latency.mjs
//   2)  php artisan knowverse:benchmark-ws --count=100 --interval=50
//
// Then Ctrl+C the Node process to print the latency summary.
//
// NOTE: server and client must share a wall clock (run on the same host, which
// local development does) — the latency is (client_now - server_timestamp).

import WebSocket from 'ws';

const KEY = process.env.VITE_REVERB_APP_KEY || process.env.REVERB_APP_KEY;
const HOST = process.env.VITE_REVERB_HOST || process.env.REVERB_HOST || 'localhost';
const PORT = process.env.VITE_REVERB_PORT || process.env.REVERB_PORT || 8080;

if (!KEY) {
  console.error('Set VITE_REVERB_APP_KEY (copy REVERB_APP_KEY from your .env).');
  process.exit(1);
}

const url = `ws://${HOST}:${PORT}/app/${KEY}?protocol=7&client=node&version=1.0`;
const latencies = [];
const ws = new WebSocket(url);

ws.on('open', () => {
  console.log(`Connected to ${url}`);
});

ws.on('message', (raw) => {
  let msg;
  try {
    msg = JSON.parse(raw.toString());
  } catch {
    return;
  }

  if (msg.event === 'pusher:connection_established') {
    ws.send(JSON.stringify({ event: 'pusher:subscribe', data: { channel: 'benchmark' } }));
    console.log("Subscribed to 'benchmark'. Now run:  php artisan knowverse:benchmark-ws");
  } else if (msg.event === 'ping') {
    const data = typeof msg.data === 'string' ? JSON.parse(msg.data) : msg.data;
    const ms = (Date.now() / 1000 - data.t) * 1000;
    latencies.push(ms);
    process.stdout.write(`recv seq=${data.seq}  latency=${ms.toFixed(2)} ms\n`);
  }
});

ws.on('error', (e) => console.error('WS error:', e.message));

process.on('SIGINT', () => {
  summary();
  process.exit(0);
});

function pct(sorted, p) {
  if (!sorted.length) return 0;
  return sorted[Math.min(sorted.length - 1, Math.ceil((p / 100) * sorted.length) - 1)];
}

function summary() {
  if (!latencies.length) {
    console.log('\nNo pings received. Is Reverb running and did you run knowverse:benchmark-ws?');
    return;
  }
  const s = [...latencies].sort((a, b) => a - b);
  const mean = s.reduce((a, b) => a + b, 0) / s.length;
  console.log(
    `\nEnd-to-end delivery latency (n=${s.length}):  ` +
      `mean=${mean.toFixed(2)}  p50=${pct(s, 50).toFixed(2)}  ` +
      `p95=${pct(s, 95).toFixed(2)}  p99=${pct(s, 99).toFixed(2)} ms`
  );
}
