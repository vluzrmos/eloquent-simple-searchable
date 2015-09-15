## Eloquent Simple Searchable

### Installation

`composer require vluzrmos/eloquent-simple-searchable`

### Usage

Put the trait into your model:

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

the attribute $searchable should contain the index with a column or a related column and the value is a type of the search which includes:

`left_like`: The value of the field should match on left side;

`right_like`: The value of the field should match on right side;

`equals`: The value o f the field should match equals to the searched text;

`full_text`: The words in the searched text should be in any position, but in the same order, of the field;

### Real life usage

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

