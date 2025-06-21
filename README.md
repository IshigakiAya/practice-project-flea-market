# coachtechフリマ

## 環境構築

**Dockerビルド**
1. git clone [git@github.com:IshigakiAya/practice-project-flea-market.git](git@github.com:IshigakiAya/practice-project-flea-market.git)
2. cd practice-project-flea-market
3. DockerDesktopアプリを立ち上げる
4. docker-compose up -d --build

> *Apple Silicon (arm64) 環境では、そのまま実行するとエラーになることがあります。エラーが発生する場合は、docker-compose.ymlファイルの「mysql」「phpmyadmin」セクションに以下のように platform を明示してください。
``` bash
mysql:
    image: mysql:8.0.26
    platform: linux/x86_64　# ← 追加
    environment:

・・・・・・

phpmyadmin:
    image: phpmyadmin/phpmyadmin
    platform: linux/amd64  # ← 追加
    environment:
```


**Laravel 環境構築**
1. docker-compose exec php bash
2. composer install
3. 「.env.example」ファイルから「.env」ファイルを作成し、以下の環境変数を変更
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
4. php artisan key:generate
5. php artisan migrate
6. php artisan db:seed
7. php artisan storage:link


## 開発環境
* 商品一覧画面（トップ画面）：http://localhost/
* 会員登録画面：http://localhost/register
* phpMyAdmin：http://localhost:8080/

## 使用技術（実行環境）
* php 8.3.0
* Laravel 8.83.29
* MySQL 8.0.26
* nginx 1.21.1

## ER図
![ER図](er.png)