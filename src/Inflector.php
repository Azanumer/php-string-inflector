<?php
/**
 * Inflector — dependency-free English pluralization and case conversion.
 *
 * Usage:
 *   Inflector::pluralize('post');        // "posts"
 *   Inflector::singularize('categories');// "category"
 *   Inflector::camelCase('blog_post');   // "blogPost"
 *   Inflector::snakeCase('BlogPost');    // "blog_post"
 */

final class Inflector
{
    /** @var array<string,string> Regex => replacement, tried in order. */
    private static $pluralRules = array(
        '/(quiz)$/i'                 => '$1zes',
        '/^(ox)$/i'                  => '$1en',
        '/([m|l])ouse$/i'            => '$1ice',
        '/(matr|vert|ind)ix|ex$/i'   => '$1ices',
        '/(x|ch|ss|sh)$/i'           => '$1es',
        '/([^aeiouy]|qu)y$/i'        => '$1ies',
        '/(hive)$/i'                 => '$1s',
        '/(?:([^f])fe|([lr])f)$/i'   => '$1$2ves',
        '/(shea|lea|loa|thie)f$/i'   => '$1ves',
        '/([ti])um$/i'               => '$1a',
        '/(tomat|potat|ech|her|vet)o$/i' => '$1oes',
        '/(bu)s$/i'                  => '$1ses',
        '/(alias|status)$/i'         => '$1es',
        '/(octop)us$/i'              => '$1i',
        '/(ax|test)is$/i'            => '$1es',
        '/(us)$/i'                   => '$1es',
        '/s$/i'                      => 's',
        '/$/'                        => 's',
    );

    /** @var array<string,string> Regex => replacement, tried in order. */
    private static $singularRules = array(
        '/(quiz)zes$/i'              => '$1',
        '/(matr)ices$/i'             => '$1ix',
        '/(vert|ind)ices$/i'         => '$1ex',
        '/^(ox)en$/i'                => '$1',
        '/(alias|status)es$/i'       => '$1',
        '/(octop|vir)i$/i'           => '$1us',
        '/(cris|ax|test)es$/i'       => '$1is',
        '/(shoe)s$/i'                => '$1',
        '/(o)es$/i'                  => '$1',
        '/(bus)es$/i'                => '$1',
        '/([m|l])ice$/i'             => '$1ouse',
        '/(x|ch|ss|sh)es$/i'         => '$1',
        '/(m)ovies$/i'               => '$1ovie',
        '/(s)eries$/i'               => '$1eries',
        '/([^aeiouy]|qu)ies$/i'      => '$1y',
        '/([lr])ves$/i'              => '$1f',
        '/(tive)s$/i'                => '$1',
        '/(hive)s$/i'                => '$1',
        '/(li|wi|kni)ves$/i'         => '$1fe',
        '/(shea|loa|lea|thie)ves$/i' => '$1f',
        '/(^analy)ses$/i'            => '$1sis',
        '/([ti])a$/i'                => '$1um',
        '/(n)ews$/i'                 => '$1ews',
        '/(h|bl)ouses$/i'            => '$1ouse',
        '/(corpse)s$/i'              => '$1',
        '/(us)es$/i'                 => '$1',
        '/s$/i'                      => '',
    );

    /** @var array<string,string> Irregular singular => plural. */
    private static $irregular = array(
        'person' => 'people',
        'man'    => 'men',
        'woman'  => 'women',
        'child'  => 'children',
        'foot'   => 'feet',
        'tooth'  => 'teeth',
        'goose'  => 'geese',
        'mouse'  => 'mice',
        'die'    => 'dice',
        'ox'     => 'oxen',
    );

    /** @var string[] Words that never change. */
    private static $uncountable = array(
        'equipment', 'information', 'rice', 'money', 'species',
        'series', 'fish', 'sheep', 'jeans', 'police', 'news',
    );

    /**
     * Pluralize an English word.
     */
    public static function pluralize($word)
    {
        if ($word === '') {
            return '';
        }
        $lower = strtolower($word);
        if (in_array($lower, self::$uncountable, true)) {
            return $word;
        }
        if (isset(self::$irregular[$lower])) {
            return self::matchCase($word, self::$irregular[$lower]);
        }
        foreach (self::$pluralRules as $pattern => $replacement) {
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $replacement, $word);
            }
        }
        return $word;
    }

    /**
     * Singularize an English word.
     */
    public static function singularize($word)
    {
        if ($word === '') {
            return '';
        }
        $lower = strtolower($word);
        if (in_array($lower, self::$uncountable, true)) {
            return $word;
        }
        $flipped = array_flip(self::$irregular);
        if (isset($flipped[$lower])) {
            return self::matchCase($word, $flipped[$lower]);
        }
        foreach (self::$singularRules as $pattern => $replacement) {
            if (preg_match($pattern, $word)) {
                return preg_replace($pattern, $replacement, $word);
            }
        }
        return $word;
    }

    /**
     * Pluralize based on a count: "1 post", "5 posts".
     */
    public static function pluralizeWithCount($count, $word)
    {
        return (int) $count . ' ' . ((int) $count === 1 ? self::singularize($word) : self::pluralize($word));
    }

    /**
     * Split a string into its component words, whatever the input style.
     *
     * @return string[] Lower-cased words.
     */
    public static function words($string)
    {
        // Split CamelCase boundaries, then split on any non-alphanumeric run.
        $string = (string) preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $string);
        $string = (string) preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1 $2', $string);
        $parts  = preg_split('/[^a-zA-Z0-9]+/', $string, -1, PREG_SPLIT_NO_EMPTY);
        return array_map('strtolower', $parts === false ? array() : $parts);
    }

    /** "blog_post" => "blogPost" */
    public static function camelCase($string)
    {
        $words = self::words($string);
        if (empty($words)) {
            return '';
        }
        $first = array_shift($words);
        return $first . implode('', array_map('ucfirst', $words));
    }

    /** "blog_post" => "BlogPost" */
    public static function pascalCase($string)
    {
        return implode('', array_map('ucfirst', self::words($string)));
    }

    /** "BlogPost" => "blog_post" */
    public static function snakeCase($string)
    {
        return implode('_', self::words($string));
    }

    /** "BlogPost" => "blog-post" */
    public static function kebabCase($string)
    {
        return implode('-', self::words($string));
    }

    /** "blog_post" => "Blog post" */
    public static function humanize($string)
    {
        return ucfirst(implode(' ', self::words($string)));
    }

    /** "blog_post" => "Blog Post" */
    public static function titleize($string)
    {
        return implode(' ', array_map('ucfirst', self::words($string)));
    }

    /**
     * Ordinal suffix: 1 => "1st", 2 => "2nd", 3 => "3rd", 4 => "4th",
     * 11 => "11th", 21 => "21st".
     */
    public static function ordinalize($number)
    {
        $n   = (int) $number;
        $mod = $n % 100;
        if ($mod >= 11 && $mod <= 13) {
            $suffix = 'th';
        } else {
            $suffix = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
            $suffix = $suffix[$n % 10];
        }
        return $n . $suffix;
    }

    /**
     * Apply the capitalization pattern of $source to $target.
     * ("Post" + "categories" => "Categories", "POST" + "category" => "CATEGORY")
     */
    private static function matchCase($source, $target)
    {
        if (ctype_upper($source)) {
            return strtoupper($target);
        }
        if (ctype_upper(substr($source, 0, 1))) {
            return ucfirst($target);
        }
        return $target;
    }
}
