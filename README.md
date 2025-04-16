# coachtech 勤怠管理
## 環境構築
#### Dockerビルド
1. ```php
    git clone git@github.com:ume32/attendance.git
   ```
2.docker-compose up -d --build
#### ※ MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。
#### Laravel環境構築
1.docker-compose exec php bash

2.composer install

3.env.exampleファイルから.envを作成し、環境変数を変更

4.php artisan key:generate

5.php artisan migrate

6.php artisan db:seed

## ログイン方法

###  一般ユーザーとしてログイン
- ログインURL：`http://localhost/login`
- 登録後にログイン可能です（新規登録フォームあり）

---

###  管理者としてログイン
- 管理者ログインURL：`http://localhost/admin/login`
- 以下の初期管理者アカウントでログインしてください  
（※ シーディングで自動作成されます）

####  管理者アカウント
| メールアドレス         | パスワード     |
|------------------------|----------------|
| `admin@example.com`    | `password123`  |

> 必要に応じて  `AdminSeeder.php` で変更できます。

## 使用技術

* PHP 7.4.9
* Laravel 9.0
* MySQL 8.0
* Nginx1.21
* phpMyAdmin

##
