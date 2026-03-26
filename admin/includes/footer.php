    <script>
        /********************************************
         *  MÓDULO: EmojiPicker (popup minimalista)
         ********************************************/
        class EmojiPicker {
            constructor(quill, options) {
                this.quill = quill;

                const toolbar = quill.getModule('toolbar');
                const button = toolbar.container.querySelector('.ql-emoji');

                if (!button) return;

                // Icono del botón
                button.textContent = '😺';

                // Lista de emojis
                this.emojis = [
                    '🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯',
                    '🦁','🐮','🐷','🐸','🐵','🐔','🐧','🐦','🐤','🐣',
                    '🐺','🦝','🦓','🦒','🐴','🦄','🐢','🐍','🐙','🦜'
                ];

                // Crear popup en el BODY (no dentro del botón)
                this.popup = document.createElement('div');
                this.popup.classList.add('emoji-popup');
                document.body.appendChild(this.popup);

                this.emojis.forEach(emoji => {
                    const btn = document.createElement('button');
                    btn.classList.add('emoji-btn');
                    btn.textContent = emoji;
                    btn.onclick = () => this.insertEmoji(emoji);
                    this.popup.appendChild(btn);
                });

                // Abrir/cerrar popup
                button.addEventListener('click', e => {
                    e.preventDefault();
                    e.stopPropagation();

                    const rect = button.getBoundingClientRect();

                    // Posicionar popup justo debajo del botón
                    this.popup.style.position = 'fixed';
                    this.popup.style.top = rect.bottom + 'px';
                    this.popup.style.left = rect.left + 'px';

                    this.popup.classList.toggle('open');
                });

                // Cerrar al hacer clic fuera
                document.addEventListener('click', () => {
                    this.popup.classList.remove('open');
                });

                // Evitar cierre al clicar dentro del popup
                this.popup.addEventListener('click', e => e.stopPropagation());
            }

            insertEmoji(emoji) {
                const range = this.quill.getSelection();
                if (range) {
                    this.quill.insertText(range.index, emoji);
                    this.quill.setSelection(range.index + emoji.length);
                }
                this.popup.classList.remove('open');
            }
        }

        Quill.register('modules/emojiPicker', EmojiPicker);


        /********************************************
         *  INICIALIZACIÓN DE QUILL
         ********************************************/
        var quill = new Quill('#editor-descripcion', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    [{ 'font': [] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['blockquote'],
                    ['link'],
                    ['clean'],
                    ['emoji']   // 👈 Botón del selector de emojis
                ],
                emojiPicker: true
            }
        });


        /********************************************
         *  TOOLTIP TRADUCIDOS
         ********************************************/
        const tooltips = {
            'bold': 'Negrita',
            'italic': 'Cursiva',
            'underline': 'Subrayado',
            'strike': 'Tachado',
            'header': 'Tamaño de letra',
            'font': 'Tipografía',
            'list': 'Lista',
            'align': 'Alinear',
            'color': 'Color de letra',
            'background': 'Color de fondo',
            'blockquote': 'Cita / Bloque de cita',
            'link': 'Enlace',
            'clean': 'Quitar formato',
            'emoji': 'Emojis de animalicos'
        };

        document.querySelectorAll('.ql-toolbar button, .ql-toolbar span').forEach(el => {
            let format = el.className.match(/ql-(\w+)/);
            if (format && tooltips[format[1]]) {
                el.setAttribute('title', tooltips[format[1]]);
                el.setAttribute('aria-label', tooltips[format[1]]);
            }
        });


        /********************************************
         *  SINCRONIZACIÓN CON TEXTAREA
         ********************************************/
        const textarea = document.getElementById('descripcion');

        quill.on('text-change', function() {
            textarea.value = quill.root.innerHTML;
        });

        document.getElementById('btn-guardar').addEventListener('click', function() {
            textarea.value = quill.root.innerHTML;
        });
    </script>

</body>
</html>