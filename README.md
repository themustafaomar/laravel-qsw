## Introduction

Cleanly apply filters on a query builder depending on query string parameters.

Sometimes you may want to apply additional logic on the query builder eg: loading some relationships so you can return that relations with the response depending on the query string parameters, or you may want to apply complex filters eg: `status=active&with=comments,replies&from=2000-01-10&to=2022-05-25&sort=newest` etc..

This package makes it easy to accomplish this with a great manner.

The main goal of using this package is the separation of concerns, we don't want tons of lines in our controllers.

## Installation

You can install it via [composer](https://getcomposer.org/)

```bash
$ composer require mustafaomar/laravel-qsw
```

## Usage

There are serveral ways to start using `laravel-qsw`

### Scopable trait

You can use `Scopable` in your model and we're done, you now have access to the `watch` scope, example.

Article.php

```php
use QueryWatcher\Traits\Scopable;

class Article extends Model
{
    use HasFactory, Scopable;
}
```

The last step we need is to create a scope, you can do so by using the following command.

For convention please use the model name followed by the query parameter you want to watch for, followed by Scope, for example `?status=success` will be `ArticleStatusScope`.

```bash
php artisan make:scope ArticleStatusScope
```

This command will create a scope class similar to this:

```php
namespace App\Scopes;
 
use QueryWatcher\Contracts\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
 
class ArticleStatusScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        //
    }
}
```

## Real-life use cases

Now let's create an example describes how to filter data with real-life example.

ArticleController.php

```php
public function index(Request $request)
{
    $article = Article::watch($this->queryWatchers())->first();

    return response()->json($article);
}

protected function queryWatchers()
{
    return [
        // 'comments' => ArticleCommentScope::class,
        // Notice: sometimes you may want to apply the scope
        // when two query parameters are presented, you can do this with:
        'when:from,to' => ArticleRangeScope::class,
        'sort' => ArticleSortScope::class
    ];
}
```

In this scope, we're gonna filter records which are between a date range.

```php
class ArticleRangeScope implements Scope
{
    public $from;

    public $to;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string|number $value
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereBetween('created_at', [$this->from, $this->to]);
    }
}

// Sort scope

class ArticleSortScope implements Scope
{
    public $sort;

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string|number $value
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if ($this->sort === 'newest') {
            $builder->latest();
        } else if ($this->sort === 'oldest') {
            $builder->oldest();
        }

        // ...
    }
}
```

## Advanced

If for some reasons you don't want to use the `watch` keyword as a scope, you can create your own local `scope`.

in the following example we'll create a scope called `scopes`.

Article.php

```php
use QueryWatcher\Facades\QueryWatcher;
use Illuminate\Database\Eloquent\Builder;

class Article extends Model
{
    use HasFactory;

    /**
     * Register the query string params to watch
     * 
     * @param \Illuminate\Database\Eloquent\Builder  $builder
     * @param array  $scopes
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScopes(Builder $builder, $scopes)
    {
        $instance = QueryWatcher::getInstance();

        // Or by resolving query watcher from the container

        $instance = app('laravel.qsw');

        $instance->watch($builder, $scopes);

        return $builder;
    }
}
```

Please don't get confused with scope and Scopes, `scope` is a word that tells Laravel we want to use the suffixed word (`Scopes`) to call when building a query with the query builder.

```php
$article = Article::scopes([])->first();
```
