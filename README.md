# Laravel Design Pattern Generator (api generator)
### you can create your restful api easily by using this library
##### and you can filter, sort and include eloquent relations based on a request


![image](https://api.romarkcode.com/storage/images/607403e2823251*cPt2YI-5YxhfL3_Uhw0txA.png)

## Installation:
Require this package with composer using the following command:

```sh
$ composer require g4t/laravel-design-pattern
```

```sh
$ php artisan vendor:publish --provider=g4t\Pattern\PatternServiceProvider 
```

## Usage
##### in folder `config` You will find `jsonapi.php`
##### This is where you will write `relations`, `sortable` columns and `filterable` columns 

## Commands:
##### full command
```sh
$ php artisan make:repo User --c --r --m
```
##### or if you have model 
```sh
$ php artisan make:repo User --c --r --model=User
```
##### and you can use `--force` command


### Available command options:

Command | Description
--------- | -------
`--c` | Create Controller and linked with repository
`--r` | Create apiResource Route in api.php
`--m` | Create Model and linked with Controller Functions
`--force` | override existing Repository


### Parameters to controlling data:

You can modify parameters in `config/jsonapi.php`


##### FILTER A QUERY BASED ON A REQUEST
```sh
/users?filter[name]=John
```

INCLUDING RELATIONS BASED ON A REQUEST
```sh
/users?include=posts
/users?include=posts,comments
```

SORTING A QUERY BASED ON A REQUEST
```sh
/users?sort=id
/users?sort=-id
```


TAKE DATA
```sh
/users?take=10
```


SKIP DATA
```sh
/users?skip=10
```

### License

The Laravel Uploader is free software licensed under the MIT license.
