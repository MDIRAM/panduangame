<script>
    document.querySelectorAll('[data-rich-editor]').forEach((editor) => {
        const surface = editor.querySelector('.rich-editor-surface');
        const input = editor.querySelector('[data-rich-editor-input]');

        if (!surface || !input) {
            return;
        }

        const sync = () => {
            input.value = surface.innerHTML.trim();
        };

        editor.querySelectorAll('[data-command]').forEach((button) => {
            button.addEventListener('click', () => {
                surface.focus();

                if (button.dataset.command === 'createLink') {
                    const url = window.prompt('Masukkan URL link');

                    if (url) {
                        document.execCommand('createLink', false, url);
                    }
                } else if (button.dataset.command === 'formatBlock') {
                    document.execCommand('formatBlock', false, button.dataset.value);
                } else {
                    document.execCommand(button.dataset.command, false, null);
                }

                sync();
            });
        });

        surface.addEventListener('input', sync);
        surface.closest('form')?.addEventListener('submit', sync);
        sync();
    });
</script>
