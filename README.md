# Jobsity

### Spinning up the application

1. Docker Network
```shell
docker network create jobsity
```

2. Docker Compose
```shell
docker-compose up --build
```
3. Create user test: `CreateTestUser`
````shell
docker run --rm --interactive --tty --network=jobsity --volume $PWD:/var/www/html/ jobsity-apache vendor/bin/phinx seed:run
````

### Application

#### API

- http://localhost:8001/
- [GET] /history
- [GET] /stock
- [POST] /user/auth
- [POST] /user

#### PG Admin (Database UI)
- http://localhost:16543/browser/

#### Swagger API (Play with)
- http://localhost:8001/public/swagger/

#### PHPUnit
Run entire unit tests
````shell
docker container start JOBSITY_PHPUNIT -i
````