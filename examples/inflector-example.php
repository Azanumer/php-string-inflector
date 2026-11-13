<?php
/**
 * Demo for php-string-inflector.
 *
 * Run: php examples/inflector-example.php
 */

require __DIR__ . '/../src/Inflector.php';

$words = array('post', 'category', 'quiz', 'person', 'child', 'box', 'church', 'status', 'news', 'series');
foreach ($words as $w) {
    echo $w . ' -> ' . Inflector::pluralize($w) . ' / ' . Inflector::singularize(Inflector::pluralize($w)) . PHP_EOL;
}

echo PHP_EOL;
echo Inflector::pluralizeWithCount(1, 'comment') . PHP_EOL; // 1 comment
echo Inflector::pluralizeWithCount(5, 'comment') . PHP_EOL; // 5 comments

echo PHP_EOL;
echo Inflector::camelCase('blog_post_title') . PHP_EOL;  // blogPostTitle
echo Inflector::pascalCase('blog_post_title') . PHP_EOL; // BlogPostTitle
echo Inflector::snakeCase('BlogPostTitle') . PHP_EOL;    // blog_post_title
echo Inflector::kebabCase('BlogPostTitle') . PHP_EOL;    // blog-post-title
echo Inflector::humanize('blog_post_title') . PHP_EOL;   // Blog post title
echo Inflector::titleize('blog_post_title') . PHP_EOL;   // Blog Post Title

echo PHP_EOL;
foreach (array(1, 2, 3, 4, 11, 12, 13, 21, 22, 23, 101) as $n) {
    echo Inflector::ordinalize($n) . ' ';
}
echo PHP_EOL;
