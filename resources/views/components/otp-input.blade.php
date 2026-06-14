<div class="flex items-center justify-center gap-2">
    @for ($i = 0; $i < $length; $i++)
        <input type="text" maxlength="1" wire:model="{{ $name }}.{{ $i }}" class="h-14 w-12 rounded-md border text-center text-xl font-semibold focus:border-indigo-500 focus:ring-indigo-500" x-data="{}" x-on:keydown.backspace="$event.target.value = ''; if ($event.target.previousElementSibling && !$event.target.value) { $event.target.previousElementSibling.focus(); }" x-on:keyup="if ($event.target.value) { $event.target.nextElementSibling ? $event.target.nextElementSibling.focus() : $event.target.blur() }" x-on:focus="$event.target.select()" x-on:paste="
                $event.preventDefault();
                const text = $event.clipboardData.getData('text');
                const inputs = Array.from($event.target.parentElement.children);
                const startPos = inputs.indexOf($event.target);
                
                for (let i = 0; i < Math.min(text.length, inputs.length - startPos); i++) {
                    if (/^[0-9]$/.test(text[i])) {
                        inputs[startPos + i].value = text[i];
                        $wire.set('{{ $name }}.' + (startPos + i), text[i]);
                    }
                }
                
                if (inputs[startPos + Math.min(text.length, inputs.length - startPos) - 1]) {
                    inputs[startPos + Math.min(text.length, inputs.length - startPos) - 1].focus();
                }
            " x-listen:focus-next.window="if ($event.detail.position == {{ $i }}) { $el.focus(); }">
    @endfor
</div>
