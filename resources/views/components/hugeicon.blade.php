@props(['name', 'size' => 19, 'class' => 'shrink-0'])

{!! \App\View\Hugeicon::render($name, (int) $size, $class) !!}
