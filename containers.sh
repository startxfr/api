#!/bin/bash
export APP=api
docker build -t ${APP}-data .
docker run -d --name=${APP}_data  ${APP}-data
docker run -d --name=${APP}_db sv-mariadb
#docker run -d --name=${APP}_db startx/sv-mariadb
docker run -d --name=${APP}_ndb startx/sv-mongo
docker run -d -p 81:80 -p 444:443 --volumes-from ${APP}_data --link ${APP}_db:db  --link ${APP}_ndb:ndb --name=${APP} startx/sv-php
