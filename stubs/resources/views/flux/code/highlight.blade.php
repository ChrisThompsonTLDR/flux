@blaze

@props([
    'languages' => ['javascript', 'php', 'html', 'css', 'json', 'bash', 'sql', 'xml', 'yaml', 'markdown'],
])

@php
$classes = Flux::classes()
    ->add('absolute top-2 start-2')
    ->add('opacity-0 group-hover/code:opacity-100')
    ->add('transition-opacity duration-150')
    ;
@endphp

<div {{ $attributes->class($classes) }}>
    <flux:dropdown position="bottom start">
        <flux:button
            variant="ghost"
            size="sm"
            icon="code-bracket"
            aria-label="{{ __('Select language for syntax highlighting') }}"
            class="text-zinc-400 hover:text-zinc-100 hover:bg-zinc-700/50"
        />

        <flux:menu>
            @foreach ($languages as $lang)
                <flux:menu.item
                    x-on:click="
                        const codeBlock = $el.closest('[data-flux-code]');
                        const codeEl = codeBlock.querySelector('code');
                        codeEl.className = codeEl.className.replace(/language-\w+/g, '').trim();
                        codeEl.classList.add('language-{{ $lang }}');
                        codeBlock.dataset.language = '{{ $lang }}';
                        if (window.hljs) { hljs.highlightElement(codeEl); }
                        else if (window.Prism) { Prism.highlightElement(codeEl); }
                    "
                >
                    {{ ucfirst($lang) }}
                </flux:menu.item>
            @endforeach
        </flux:menu>
    </flux:dropdown>
</div>
