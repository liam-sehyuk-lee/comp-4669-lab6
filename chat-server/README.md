# Chat Server

Environment variables can be updated in the `.env` file.

### Install Dependencies

```
composer install
```

### Run the application

```
php artisan serve
```

### Run the Centrifugo server

```
centrifugo --config=centrifugo_config.json --admin
```

It is configured to listen for incoming connections on port 8500.
