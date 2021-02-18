## Последовательность деплоя
1. Распаковать файл /config/.env.dist
2. В корне проекта запускаем `composer install --ignore-platform-reqs`
3. Переходим в папку /deploy/local и в косоли запускаем команду `docker-compose build`
4. Далее в консоли запускаем команду `sh up.sh`
5. Заходим в fpm контейнер : `docker exec -it fpm.inventory bash` и запускаем
  `php /www/vendor/bin/doctrine-migrations migrations:migrate`
  
  
 Последовательность создания новго сервиса
  1) /config/.env.dist
  2) Заменить все namespace \SkeletonDev\ -> \new_service_name\
  3) Заменить имя сервиса в `composer.json` storytale/skeleton-dev -> storytale/new_service_name. Там же заменить описание
  4) меняем названия сервисов, устанавливаем свободный порт для nginx и postgres в `deploy/local/docker-compose.yml`
  5) Меняем название fpm контейнера `fastcgi_pass    fpm_skeleton-dev:9000;` в `deploy/local/nginx/default.conf`
  6) Меняем название сервиса `/src/PortAdapters/Primary/App/Zend/RestAPI/src/RestAPI/Controller/IndexController.php`
  7) composer install
  8) создать домееные сущности по примеру `src/Domain/PersistModel/Order`. 
  9) создать query классы для сущностей по пример `src/Application/Query/TestOrder`.
  10) создаем реализацию репозиториев и файлы миграций по примеру `/src/PortAdapters/Secondary/Order/Persistence`
  11) Прописавыем конфиг сборки созданных репозиториев в `symfony-ioc.xml` по примеру `storytale.skeletonDev.portAdapters.secondary.order.persistence.doctrine.doctrineOrderRepository`
  12) Удаляем из `symfony-ioc.xml` пример конфига `storytale.skeletonDev.portAdapters.secondary.order.persistence.doctrine.doctrineOrderRepository`
  13) Добавляеем пути к файлам миграции в `symfony-ioc.xml` в блоке `doctrine.metadata.driver`. Там же удаляем путь к миграции order.
  14) Удалить миграции Version20201112142855 и Version20201112143901
  15) Генерим миграции, выполняем их
  16) Делаем реализацию query класса по примеру `src/PortAdapters/Secondary/Order/Querying/`
  17) Прописавыем конфиг сборки созданных query классов в `symfony-ioc.xml` по примеру `storytale.skeletonDev.portAdapters.secondary.order.querying.aura.auraOrderDataProvider`
  18) Удаляем из `symfony-ioc.xml` пример конфига `storytale.skeletonDev.portAdapters.secondary.order.querying.aura.auraOrderDataProvider`
  19) Создаем классы для редактирования новых сущностей по примеру `/src/Application/Command/TestOrder/`
  20) Прописываем конфиг сборки созданных классов в  `symfony-ioc.xml` по примеру `storytale.skeletonDev.application.command.testOrder.testOrderService`
  21) Удаляем из `symfony-ioc.xml` пример конфига `storytale.skeletonDev.application.command.testOrder.testOrderService`
  22) Делаем контроллер по пример `/src/PortAdapters/Primary/App/Zend/RestAPI/src/RestAPI/Controller/TestController.php`
  23) Прописываем конфиг сборки созданных контроллеров в  `symfony-ioc.xml` по примеру `/src/PortAdapters/Primary/App/Zend/RestAPI/src/RestAPI/Controller/TestController.php`
  25) Прописываем конфиги роутинга для новых контроллеров в `/src/PortAdapters/Primary/App/Zend/RestAPI/config/module.config.php` по пример `test`
  26) Удаляем из `/src/PortAdapters/Primary/App/Zend/RestAPI/config/module.config.php` пример конфига `test`
 
 Удалить `src/Domain/PersistModel/Order`
 Удалить `src/Application/Query/TestOrder`
 Удаляем `/src/PortAdapters/Secondary/Order/Persistence`
 Удаляем `src/PortAdapters/Secondary/Order/Querying/`
 

