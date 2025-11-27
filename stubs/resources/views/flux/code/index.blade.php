@blaze

@php $highlightLanguages ??= $attributes->pluck('highlight:languages'); @endphp

@props([
    'highlightLanguages' => null,
    'language' => null,
    'copyable' => null,
    'highlight' => null,
])

@php
$classes = Flux::classes()
    ->add('relative group/code')
    ->add('block w-full')
    ->add('rounded-lg border')
    ->add('bg-zinc-900 dark:bg-zinc-950')
    ->add('border-zinc-700 dark:border-zinc-800')
    ->add('overflow-hidden')
    ;

$preClasses = Flux::classes()
    ->add('p-4 overflow-x-auto')
    ->add('text-sm font-mono')
    ->add('text-zinc-100')
    ;

$codeClasses = Flux::classes()
    ->add('block')
    ->add($language ? 'language-' . $language : '')
    ;
@endphp

<div {{ $attributes->class($classes) }} data-flux-code @if($language) data-language="{{ $language }}" @endif @if($highlight) data-highlight @endif>
    <?php if ($highlight): ?>
        <flux:code.highlight :languages="$highlightLanguages ?? ['javascript', 'php', 'html', 'css', 'json', 'bash', 'sql', 'xml', 'yaml', 'markdown']" />
    <?php endif; ?>

    <?php if ($copyable): ?>
        <flux:code.copyable />
    <?php endif; ?>

    <pre class="{{ $preClasses }}"><code class="{{ $codeClasses }}">{{ $slot }}</code></pre>
</div>
