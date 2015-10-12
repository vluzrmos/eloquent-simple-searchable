## Eloquent Simple Searchable

### Installation

`composer require vluzrmos/eloquent-simple-searchable`

### Usage

Put that trait into your model:

```php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Vluzrmos\SimpleSearchable\Eloquent\SimpleSearchableTrait;

class User extends Model
{ 
	use SimpleSearchableTrait;

	protected $searchable = [
		'field' => 'type'
	];
}
```

The attribute $searchable should contain the index with a column or a related column and the value is a type of the search which includes:

 * `left_text`: Match the left side of the column value
 * `right_text`:  Match the right side of the column value
 * `equals`: The searchd text should be equals to the column value
 * `full_text`: The searched text should be in any position of the searchd column.

### Real Life Usage

```php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Vluzrmos\SimpleSearchable\Eloquent\SimpleSearchableTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
	use Authenticatable, CanResetPassword, SimpleSearchableTrait;

	protected $searchable = [
		'name' => 'full_text',
		'posts.title' => 'full_text',
		// query deeply into your relations, that relations should exists on the respective models.
		'posts.comments.owner.name' => 'full_text'
	];

	public function posts()
	{
		return $this->hasMany(Post::class);
	}
}
```

And in your controller or anywhere:

```php
$users = User::search('Jonh')->get();

// or replace the default searchable fields:

$users = User::search('Jonh', ['name' => 'full_text'])->get();
```

> Note: The `search` method is a scope, so you need to use query builder methods like `get` or `all` to perform the search and get a collection of the results.
