@blaze

@props([
    'size' => 'sm',
])

@php
$classes = Flux::classes()
    ->add('absolute top-2 end-2')
    ->add('opacity-0 group-hover/code:opacity-100')
    ->add('transition-opacity duration-150')
    ;
@endphp

<div {{ $attributes->class($classes) }}>
    <flux:button
        variant="ghost"
        size="{{ $size }}"
        square
        x-data="{ copied: false }"
        x-on:click="
            if (navigator.clipboard) {
                navigator.clipboard.writeText($el.closest('[data-flux-code]').querySelector('code').textContent)
                    .then(() => { copied = true; setTimeout(() => copied = false, 2000); })
                    .catch(() => { copied = false; });
            }
        "
        x-bind:data-copyable-copied="copied"
        aria-label="{{ __('Copy to clipboard') }}"
        class="text-zinc-400 hover:text-zinc-100 hover:bg-zinc-700/50"
    >
        <flux:icon.clipboard-document-check variant="mini" class="hidden [[data-copyable-copied]>&]:block" />
        <flux:icon.clipboard-document variant="mini" class="block [[data-copyable-copied]>&]:hidden" />
    </flux:button>
</div>
