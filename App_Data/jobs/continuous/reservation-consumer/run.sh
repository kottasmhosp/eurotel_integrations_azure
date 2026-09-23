#!/bin/bash

cd /home/site/wwwroot

exec php bin/console consumers:reservationConsumer:start
