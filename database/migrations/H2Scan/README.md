# INFO

This is basically the same tables (like the tables outside this folder) but with a few added tables for H2Scan

if you're having problems migrating with this set of tables:
```bash
php artisan db:wipe

php artisan migrate --path=/database/migrations/H2Scan/
php artisan migrate:refresh --path=/database/migrations/H2Scan/
php artisan migrate:refresh --path=/database/migrations/H2Scan/ --seeder=H2ScanSeeder
```
if you want to migrate while still keeping your data inside the tables, I'm sorry but you're out of luck (idk how hehe)