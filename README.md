# Installation

First, clone this repository:

```bash
$ docker-compose build
```

```bash
$ docker-compose up -d
```
after all container starts
```bash
$ docker exec -it sn_test_php-fpm php composer.phar update
```

```bash
$ docker exec -it sn_test_php-fpm php bin/console doctrine:migrations:migrate -n
```
open in browser:
```bash
http://frontend.localhost
```

in database already exist users with username: 'test', 'user', 'qwerty'


