#!/bin/bash
nodejs server.js &
echo "WebSocket server started";
sleep 1
nodejs pg_client.js &
echo "PG client started"
