```bash
# コンテナ起動
docker-compose up -d

#  コンテナに入ってartisanコマンド実行
docker-compose exec php /bin/bash
cd src
php artisan
```

- URL `http://127.0.0.1:8000/`
- phpMyAdmin `http://127.0.0.1:8086/`
