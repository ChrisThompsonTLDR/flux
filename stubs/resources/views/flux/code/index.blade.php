@blaze

@php $highlightLanguages ??= $attributes->pluck('highlight:languages'); @endphp

@props([
    'highlightLanguages' => null,
    'language' => null,
    'copyable' => null,
    'highlight' => null,
    'editable' => null,
    'wireModel' => null,
    'placeholder' => null,
])

@php
$isEditable = $editable || $wireModel;
$safeLanguage = $language ? preg_replace('/[^a-zA-Z0-9_-]/', '', $language) : null;

if ($isEditable) {
    // Editable code block using TipTap
    $classes = Flux::classes()
        ->add('relative')
        ->add('block w-full')
        ->add('rounded-lg border')
        ->add('bg-zinc-900 dark:bg-zinc-950')
        ->add('border-zinc-700 dark:border-zinc-800')
        ->add('overflow-hidden')
        ->add('focus-within:ring-2 focus-within:ring-accent focus-within:border-accent')
        ;
} else {
    // Display-only code block
    $classes = Flux::classes()
        ->add('relative group/code')
        ->add('block w-full')
        ->add('rounded-lg border')
        ->add('bg-zinc-900 dark:bg-zinc-950')
        ->add('border-zinc-700 dark:border-zinc-800')
        ->add('overflow-hidden')
        ;
}

$preClasses = Flux::classes()
    ->add('p-4 overflow-x-auto')
    ->add('text-sm font-mono')
    ->add('text-zinc-100')
    ;

$codeClasses = Flux::classes()
    ->add('block')
    ->add($safeLanguage ? 'language-' . $safeLanguage : '')
    ;
@endphp

<div {{ $attributes->class($classes) }} 
     data-flux-code 
     @if($safeLanguage) data-language="{{ $safeLanguage }}" @endif 
     @if($highlight) data-highlight @endif
     @if($isEditable) data-editable="true" @endif
     x-data="{
         highlightEnabled: @js($highlight ?? false),
         useTabs: false,
         tabSize: 4,
         copied: false,
         editor: null,
         init() {
             @if($isEditable)
                // Initialize TipTap editor with code-block extension
                if (typeof window.useEditor !== 'undefined') {
                    const { Editor } = window.useEditor();
                    const { CodeBlock } = window.useEditor.extensions || {};
                    
                    // Build extensions array, only including CodeBlock if it exists
                    const extensions = [];
                    if (CodeBlock) {
                        extensions.push(
                            CodeBlock.configure({
                                languageClassPrefix: 'language-',
                                defaultLanguage: @js($safeLanguage ?? 'plaintext'),
                                HTMLAttributes: {
                                    class: 'code-block-editor'
                                }
                            })
                        );
                    }
                    
                    this.editor = new Editor({
                        element: this.$refs.editorElement,
                        extensions: extensions,
                         content: @js($slot->toHtml()),
                         onUpdate: ({ editor }) => {
                             @if($wireModel)
                                 @this.set('{{ $wireModel }}', editor.getHTML());
                             @endif
                         }
                     });
                 }
             @endif
         },
         toggleHighlight() {
             this.highlightEnabled = !this.highlightEnabled;
             const codeEl = this.$el.querySelector('code');
             if (codeEl && this.highlightEnabled) {
                 if (window.hljs) {
                     hljs.highlightElement(codeEl);
                 } else if (window.Prism) {
                     Prism.highlightElement(codeEl);
                 }
             }
         },
         toggleTabsSpaces() {
             this.useTabs = !this.useTabs;
             // Tab/space conversion would be handled by TipTap's code-block extension
             if (this.editor) {
                 // Update editor configuration for tabs/spaces
             }
         },
         copyCode() {
             const codeEl = this.$el.querySelector('code');
             if (codeEl && navigator.clipboard) {
                 navigator.clipboard.writeText(codeEl.textContent)
                     .then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000); })
                     .catch(() => { this.copied = false; });
             }
         }
     }">
    
    @if($isEditable)
        <!-- Controls Bar for Editable Code -->
        <div class="flex items-center justify-between gap-2 px-3 py-2 border-b border-zinc-700 dark:border-zinc-800">
            <div class="flex items-center gap-2">
                <!-- Syntax Highlighting Toggle -->
                <flux:button
                    x-on:click="toggleHighlight()"
                    x-bind:variant="highlightEnabled ? 'primary' : 'ghost'"
                    size="xs"
                    icon="code-bracket"
                >
                    Syntax Highlight
                </flux:button>

                <!-- Spaces vs Tabs Toggle -->
                <flux:button
                    x-on:click="toggleTabsSpaces()"
                    x-bind:variant="useTabs ? 'primary' : 'ghost'"
                    size="xs"
                    icon="arrows-right-left"
                >
                    <span x-text="useTabs ? 'Tabs' : 'Spaces'"></span>
                </flux:button>
            </div>

            <!-- Copy Button -->
            <flux:button
                x-on:click="copyCode()"
                variant="ghost"
                size="xs"
                x-bind:class="copied ? 'text-green-500' : ''"
            >
                <span x-show="!copied" class="flex items-center gap-1">
                    <flux:icon.clipboard-document variant="mini" />
                    Copy
                </span>
                <span x-show="copied" class="flex items-center gap-1">
                    <flux:icon.check variant="mini" />
                    Copied!
                </span>
            </flux:button>
        </div>

        <!-- TipTap Editor Container -->
        <div 
            x-ref="editorElement"
            wire:ignore
            class="prose prose-invert max-w-none p-4 min-h-[200px] focus:outline-none [&_.ProseMirror]:outline-none"
            data-tiptap-editor
        ></div>
    @else
        <!-- Display-only Code Block -->
        <?php if ($highlight): ?>
            <flux:code.highlight :languages="$highlightLanguages ?? ['javascript', 'php', 'html', 'css', 'json', 'bash', 'sql', 'xml', 'yaml', 'markdown']" />
        <?php endif; ?>

        <?php if ($copyable): ?>
            <flux:code.copyable />
        <?php endif; ?>

        <pre class="{{ $preClasses }}"><code class="{{ $codeClasses }}">{{ $slot }}</code></pre>
    @endif
</div>
