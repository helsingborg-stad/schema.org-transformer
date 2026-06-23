#!/bin/sh

TAG=$1

docker build -f .docker/Dockerfile -t schema-transformer:$TAG .