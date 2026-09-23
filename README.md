Eurotel Integration microservice (Symfony 4.2)
=======================================

## Description

Eurotel Integration (a.k.a "eurotel") is a Symfony microservice - deployed on [google cloud](https://console.cloud.google.com/appengine/services?project=mhosp-integration). It is used to pull data from Eurotel PMS and send them to the General Integration Service (a.k.a. "protel" service on google cloud).

In "eurotel" service there are the following two scheduled jobs:
1. *Get Inhouse Guests* - a cron job that runs every 10 mins
2. *Get Transactions* - a cron job that runs every night at 00:01 GMT

### Get Inhouse Guests

Get inhouse guests is a cron job that runs every 10 minutes and goes through the following steps:
1. Fetch all hotelgroups from the database
2. Checks if there is an ongoing integration process for each hotelgroup and if not, then publishes the corresponding hotelgroup to the "inhouse_hotel_queue" 
3. For each hotelgroup in the "inhouse_hotel_queue" make a call to the Eurotel server and gets all the in-house guests
4. For each in-house guest checks if there is already an existing reservation. If there is an existing reservation then checks if this reservation has been updated. 
5. Finally, persists all the **new** or **updated** reservations in the "eurotel" database and publish them to the "reservation_queue"
6. All the messages from the "reservation_queue" are being sent to the GIS (/api/eurotel)

### Get Transactions

Get Transactions is a cron job that runs every night at 00:01 GMT and goes through the following steps:
1. Fetch all hotelgroups from the database
2. Publish each one of the hotelgroups to the "checkout_hotel_queue" with a relevant time interval (by default one day - the previous day)
3. For each hotelgroup in the "checkout_hotel_queue" make a call to the Eurotel server for the specified time interval and gets all the transactions for that time interval
4. For each one of the transactions find its trx code from the database and set the correct transaction category to it.
5. Finally,  persist the transactions to the database and publishes them to "transaction_queue"
6. From there, all transactions being sent to the GIS (/api/eurotel/transactions)