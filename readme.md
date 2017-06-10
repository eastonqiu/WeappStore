# 安装
```
composer install
cp .env.example .env # 根据实际环境修改数据库链接等, APP_KEY等
chmod -R 777 bootstrap/cache storage # www-data必须有权限, 不一定设成
php artisan migrate # 迁移数据库表
php artisan db:seed --class=UsersTableSeeder # 填充数据
php artisan migrate:refresh # 刷新数据库结构并执行数据填充
```
