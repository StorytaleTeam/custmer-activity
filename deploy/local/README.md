## Последовательность деплоя
1. Распаковать файл /config/.env.dist
2. В корне проекта запускаем `composer install --ignore-platform-reqs`
3. Переходим в папку /deploy/local и в косоли запускаем команду `docker-compose build`
4. Далее в консоли запускаем команду `sh up.sh`
5. Заходим в fpm контейнер : `docker exec -it fpm.inventory bash` и запускаем
  `php /www/vendor/bin/doctrine-migrations migrations:migrate`