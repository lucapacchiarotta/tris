# Apiwhislist

API to manage wishlists. Based on Zend Framework 3.  

Set special permissions:

chmod -R 777 data  
chmod +x bin/cli.php  

## Endpoints

Method: POST  
Encoding: application/json  

  * api/v1/createlist  
	{
		"user": "xx",
		"listname": "xx"
	}
  * api/v1/additemtolist  
  	{
		"listname": "xx",
		"listitem": "xx",
		"user": "xx"
	}
  * api/v1/getlist  
  	{
		"listname": "xx",
		"user": "xx"
	}

## CLI
Inside the Docker container in SSH:  

```bash
$ ./var/www/bin/cli.php export --path /folder
```

OR  

```bash
$ php /var/www/bin/cli.php export --path /folder
```

## Using docker-compose

This skeleton provides a `docker-compose.yml` for use with
[docker-compose](https://docs.docker.com/compose/); it
uses the `Dockerfile` provided as its base. Build and start the image using:

```bash
$ docker-compose up -d --build
```

At this point, you can visit http://localhost:8080 to see the site running.

You can also run composer from the image. The container environment is named
"zf", so you will pass that value to `docker-compose run`:

```bash
$ docker-compose run zf composer install
```



## Running Unit Tests

To run unit tests:

```bash
$ ./vendor/bin/phpunit
```

If you need to make local modifications for the PHPUnit test setup, copy
`phpunit.xml.dist` to `phpunit.xml` and edit the new file; the latter has
precedence over the former when running tests, and is ignored by version
control. (If you want to make the modifications permanent, edit the
`phpunit.xml.dist` file.)

