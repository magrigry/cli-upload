# CLI Upload

This project is a minimalistic application designed to facilitate file uploads from one server and file downloads from another. 
It provides a basic interface and API for transferring files between servers, focusing on simplicity and ease of use.

# Local development environnement 

- Clone this project
- Run `php artisan serve` to start a local server environnement 


# Production environnement with Docker 

- Clone this project 

## Building 
```shell 
docker build -t cliupload .
```

## Starting the container 

```shell
docker run -p 8080:8080 -e APP_KEY=yourappkeysecret -e XX=YY cliupload
```

Use the `-e` flag to pass environnement variables.

### APP_KEY

The APP_KEY must be 256 bits randomly generated secret string that will be used by the application. 

If you change this application's encryption key, all authenticated user sessions will and some files might not be recoverable.

### Mounting 

You can mount some volumes for persisting some data. 

E.g. if you want to use an SQLite Database you might run this command

```shell
docker run -v ./database/database.sqlite:/var/www/html/database/database.sqlite \
          -e APP_KEY=<your secret> \
          -p 8080:8080 cliupload
```

## Environnement variable

See the `.env.example` file for a list of usefull environnement variable.

See also `./config/upload.php` for a list of environnement variables that might be use for rate limiting and setting some capacity limits.

## Acknowledgments

This project was inspired and made possible by several open-source projects and resources.
Below are some of the key tools and libraries that helped in its development:
-  [bashupload.com](https://bashupload.com) PHP based files uploader for CLI, servers, desktops and mobiles


