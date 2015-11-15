#!/usr/bin/env bash

# testing adding and listing templates

HOST=192.168.59.103
PORT=8001

curl -X PUT \
     -H 'Content-Type: application/vnd.rndr.twig' \
     -d @hello.html \
     http://$HOST:$PORT/greetings/hello \
     -v

curl -X PUT \
     -H 'Content-Type: application/vnd.rndr.twig' \
     -d @complex.twig \
     http://$HOST:$PORT/content/complex \
     -v

curl -X PUT \
     -H 'Content-Type: application/vnd.rndr.twig' \
     -d @difficult.twig \
     http://$HOST:$PORT/content/difficult \
     -v

curl -X PUT \
     -H 'Content-Type: application/vnd.rndr.twig' \
     -d @simple.twig \
     http://$HOST:$PORT/content/simple \
     -v


# list the templates
curl -X GET \
     http://$HOST:$PORT/ \
     -v
