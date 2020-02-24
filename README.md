# Installation

Склонируйте репозиторий:
с помощью https://docs.docker.com/compose/install/ вызовите следующие комманды
```bash
$ docker-compose build
```

```bash
$ docker-compose up -d
```
после запуска всех контейнеров

```bash
$ docker exec -it sn_test_php-fpm php bin/console doctrine:migrations:migrate -n
```
frontend находиться по адресу:
```bash
http://frontend.localhost
```

в базе уже будут находится пользователи с username: 'test', 'user', 'qwerty'


# Additional
что-бы облегчить сборку\развертывание проекта в рамаках т.з. vendor включен в репозиторий, поэтому в вызове комманды при сборке нет необходимости
composer update:
```bash
$ docker exec -it sn_test_php-fpm php composer.phar update
```