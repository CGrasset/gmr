# GMR
Giant monkey robot test implementation

## Prerequisites

You will need the following things properly installed on your computer.

* [Docker](https://www.docker.com/)

## What's included?

* Vessel
* Laravel
* PHP 7.3
* MySQL 5.7
* Redis ([latest](https://hub.docker.com/_/redis/))
* NodeJS ([latest](https://hub.docker.com/_/node/)), with Yarn & Gulp

## Installation

* `git clone <repository-url>` this repository
* `cd gmr`
* `bash vessel init`
* `./vessel start`
* `./vessel artisan migrate --seed`

## Start and Stopping

* `./vessel start`
* `./vessel stop`

## Configuration

By default current queue runs in redis and workers are managed by supervisord with a default of 3 workers.
To change the number of workers follow the next steps:
* Run `./vessel stop`
* Change `numprocs=<number-of-workers>` in the file located at `<repository-folder>/docker/app/laravel-worker.conf`
* Run `./vessel build`
* Run `./vessel start` to re-start the container

## Running / Development

* `./vessel start`
* Your app will be running at [http://localhost:80](http://localhost:80).

## API

| HTTP METHOD | URL | Description |
| --- | --- | --- |
| POST | /api/register | Register new user |
| POST | /api/login | Login with user |
| POST | /api/logout | Logout current session |
| GET | /api/job | Get next available job with highest priority |
| GET | /api/job/$id | Get status of job with id = $id |
| POST | /api/job | Submit a new job to the queue |
| DELETE | /api/job/$id | Delete job with id = $id |
| GET | /api/queue | Get queue's status |

## User registration

A admin user is created by default so you can use it if you don't feel like creating new users:
```
email: gmr@test.com
password: gmradmin
```

To create a new user:
```
$ curl -X POST http://localhost:80/api/register \
 -H "Accept: application/json" \
 -H "Content-Type: application/json" \
 -d '{"name": "some-name", "email": "some-email@some-domain.com", "password": "your-pass", "password_confirmation": "your-pass"}'
```

The above request should return a payload as follows:
```
{
    "data": {
        "id": some-id,
        "name": "some-name",
        "email": "some-email@some-domain.com",
        "email_verified_at": null,
        "created_at": "some-timestamp",
        "updated_at": "some-timestamp",
        "api_token": "your-current-api-token"
    }
}
```
This `API_TOKEN` is needed for further requests to the API

## Login

```
$ curl -X POST http://localhost:80/api/login \
 -H "Accept: application/json" \
 -H "Content-Type: application/json" \
 -d '{"email": "some-email@some-domain.com", "password": "your-pass"}'
```
The returned payload should be the same as when you register

## Authenticated requests

Every other request should include the header `Authorization: Bearer <api-token>`.

#### Example:

Submit a new job to the queue

```
$ curl -X POST http://localhost:80/api/job \
 -H "Accept: application/json" \
 -H "Content-Type: application/json" \
 -H "Authorization: Bearer some-api-token"
```

## Useful commands

* `./vessel logs -f` Tail all logs
* `./vessel mysql` Mysql's container cli
* `./vessel exec app bash` App's container cli (bash)

