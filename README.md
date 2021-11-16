## 最初の立ち上げ

$ git clone https://github.com/miyayamaM/rental_app.git
$ cd rental_app
$ docker-compose up -d
$ cp app/.env.example app/.env
$ vi app/.env #DB環境変数修正
$ cp app/.env.example app/.env.testing
$ vi app/.env.testing #テスト用DB環境変数修正
$ docker-compose exec app bash
root@XXXXXXXX:/var/www/app# composer install
root@XXXXXXXX:/var/www/app# php artisan key:generate
root@XXXXXXXX:/var/www/app# php artisan migrate
root@XXXXXXXX:/var/www/app# php artisan key:generate --env=testing
root@XXXXXXXX:/var/www/app# php artisan migrate --env=testing
root@XXXXXXXX:/var/www/app# php artisan test

