# dropForeign problem

To see the problem of foreign keys not being dropped in SQLite, run:

```bash
composer install
bash testDropForeign.sh
```

The test code may be found in [app/Console/Commands/TestDropForeign.php](app/Console/Commands/TestDropForeign.php), run by [testDropForeign.sh](testDropForeign.sh).


This is a demo for issue [https://github.com/laravel/framework/issues/24041](https://github.com/laravel/framework/issues/24041).