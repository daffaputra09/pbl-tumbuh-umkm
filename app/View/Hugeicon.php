<?php

namespace App\View;

use Illuminate\Support\HtmlString;

class Hugeicon
{
    /**
     * Render one icon from the Hugeicons set exported in resources/icons/shell.json.
     */
    public static function render(string $name, int $size = 19, string $class = 'shrink-0', float $strokeWidth = 1.8): HtmlString
    {
        $icons = self::icons();
        $nodes = $icons[$name] ?? [];
        $children = '';

        foreach ($nodes as $node) {
            $tag = $node[0] ?? '';
            $attributes = $node[1] ?? [];

            if (! in_array($tag, ['path', 'circle', 'rect', 'line', 'polyline', 'ellipse'], true)) {
                continue;
            }

            $children .= '<'.$tag.self::attributes($attributes, $strokeWidth).'/>';
        }

        return new HtmlString(
            '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" color="currentColor" stroke="currentColor" stroke-width="'.$strokeWidth.'" class="'.e($class).'" aria-hidden="true">'
            .$children
            .'</svg>'
        );
    }

    /**
     * @return array<string, list<array{0: string, 1: array<string, mixed>}>>
     */
    private static function icons(): array
    {
        static $icons;

        if ($icons === null) {
            $icons = json_decode((string) file_get_contents(resource_path('icons/shell.json')), true) ?? [];
        }

        return $icons;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function attributes(array $attributes, float $strokeWidth): string
    {
        $markup = '';

        foreach ($attributes as $name => $value) {
            if ($name === 'key' || ! is_scalar($value)) {
                continue;
            }

            if ($name === 'strokeWidth') {
                $value = $strokeWidth;
            }

            $markup .= ' '.self::attributeName($name).'="'.e((string) $value).'"';
        }

        return $markup;
    }

    private static function attributeName(string $name): string
    {
        return strtolower((string) preg_replace('/[A-Z]/', '-$0', $name));
    }
}
