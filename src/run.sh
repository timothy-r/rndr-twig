#!/bin/bash

echo fastcgi_param REDIS_PORT $REDIS_PORT >> /etc/nginx/fastcgi_params

# Start the server
supervisord --nodaemon