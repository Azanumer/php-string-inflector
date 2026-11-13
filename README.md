# PHP String Inflector

Dependency-free English pluralization and case conversion for PHP 7.4+.

## Features

- **Pluralize / singularize** — rule-based with irregulars (`person → people`, `child → children`, `quiz → quizzes`) and uncountables (`news`, `series`, `sheep` stay unchanged).
- **Count-aware** — `pluralizeWithCount(5, 'comment')` → `"5 comments"`, `pluralizeWithCount(1, 'comment')` → `"1 comment"`.
- **Case conversion** — `camelCase`, `pascalCase`, `snakeCase`, `kebabCase`, `humanize`, `titleize`, all accepting any input style (`BlogPost`, `blog_post`, `blog-post`, `blog post`).
- **Ordinals** — `ordinalize(21)` → `"21st"`, `ordinalize(11)` → `"11th"` (11/12/13 handled correctly).

## Usage

```php
require 'src/Inflector.php';

Inflector::pluralize('category');   // "categories"
Inflector::singularize('boxes');    // "box"
Inflector::camelCase('blog_post');   // "blogPost"
Inflector::snakeCase('BlogPost');    // "blog_post"
Inflector::kebabCase('BlogPost');    // "blog-post"
Inflector::ordinalize(3);           // "3rd"
```

Run the demo:

```bash
php examples/inflector-example.php
```

## Notes

- Pluralization is English-only and rule-based; exotic words may need a custom override — extend the `$pluralRules` / `$irregular` tables in `src/Inflector.php`.
- Case is preserved where it matters: `pluralize('Post')` → `"Posts"`, `pluralize('POST')` → `"POSTS"`.

## License

MIT — see [LICENSE](LICENSE).
