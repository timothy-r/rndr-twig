#!/usr/bin/env bash

# send a test template

HOST=192.168.59.103
PORT=8001

curl -X PUT \
     -H 'Content-Type: application/vnd.rndr.twig' \
     -d @hello.html \
     http://$HOST:$PORT/test/hello \
     -v

curl -X GET \
     http://$HOST:$PORT/test/hello \
     -v

curl -X POST \
     http://$HOST:$PORT/test/hello?name=Malcolm \
     -v

curl -X DELETE \
     http://$HOST:$PORT/test/hello \
     -v

curl -X GET \
     http://$HOST:$PORT/test/hello \
     -v
