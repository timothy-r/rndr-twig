#!/bin/bash

# inject the REDIS_PORT env var into redis config

echo "fastcgi_param REDIS_PORT $REDIS_PORT;" >> /etc/nginx/fastcgi_params

# Start the server
supervisord --nodaemon