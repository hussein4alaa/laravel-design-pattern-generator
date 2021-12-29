# Laravel Design Pattern Generator (api generator)
### you can create your restful api with validation easily by using this package
##### and you can filter, sort and include eloquent relations based on a request

<img src="https://github.com/hussein4alaa/laravel-design-pattern-generator/blob/1.0.1/logo.png" style="width:400px;"/>


![me](https://github.com/hussein4alaa/laravel-design-pattern-generator/blob/1.0.1/image.gif)

## Installation:
Require this package with composer using the following command:

```sh
composer require g4t/laravel-design-pattern
```

```sh
php artisan vendor:publish --provider=g4t\Pattern\PatternServiceProvider 
```

## Usage
##### in folder `config` You will find `jsonapi.json`
##### This is where you will write `relations`, `sortable` columns and `filterable` columns 

## Commands:
##### full command 
###### create (Model, Controller, Route And Repostitory)
```sh
php artisan make:repo User
```
##### or if you have model 
```sh
php artisan make:repo User --model=User
```
##### and you can use `--force` command

##### you can create validation from database table using this command 
```sh
php artisan repo:validation ModelName
```


### Available command options:

Command | Description
--------- | -------
`--m` | Create Migration
`--model={ModelName}` | Insert model in controller if you have model
`--force` | override existing Repository




### Parameters to controlling data:

You can modify parameters in `config/jsonapi.json`


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

Laravel Design Pattern Generator is free software licensed under the MIT license.
