#!/bin/bash

cd /home/site/wwwroot

exec php bin/console consumers:futureReservationConsumer:start
