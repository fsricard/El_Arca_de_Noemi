            <!-- Estilos y script de Quill -->
            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
            <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

            <script>
                /********************************************
                 *  LIMPIADOR UNIVERSAL DE CONTENIDO QUILL
                 ********************************************/
                function limpiarContenidoQuill(html) {
                    if (!html) return '';

                    html = html.replace(/ class="ql-[^"]*"/g, '');
                    html = html.replace(/ data-[^=]*="[^"]*"/g, '');
                    html = html.replace(/<span[^>]*><\/span>/g, '');
                    html = html.replace(/<p><br><\/p>/g, '');
                    html = html.trim();
                    html = html.replace(/^<p><\/p>/, '');
                    html = html.replace(/<p><\/p>$/, '');
                    html = html.replace(/<ol[^>]*>/g, '<ol>');
                    html = html.replace(/<ul[^>]*>/g, '<ul>');
                    html = html.replace(/<li[^>]*>/g, '<li>');

                    return html;
                }

                /********************************************
                 *  MÓDULO: EmojiPicker
                 ********************************************/
                class EmojiPicker {
                    constructor(quill, options) {
                        this.quill = quill;

                        const toolbar = quill.getModule('toolbar');
                        const button = toolbar.container.querySelector('.ql-emoji');
                        if (!button) return;

                        button.textContent = '😺';

                        this.emojis = [
                            '🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯',
                            '🦁','🐮','🐷','🐸','🐵','🐔','🐧','🐦','🐤','🐣',
                            '🐺','🦝','🦓','🦒','🐴','🦄','🐢','🐍','🐙','🦜'
                        ];

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

                        button.addEventListener('click', e => {
                            e.preventDefault();
                            e.stopPropagation();
                            const rect = button.getBoundingClientRect();
                            this.popup.style.position = 'fixed';
                            this.popup.style.top = rect.bottom + 'px';
                            this.popup.style.left = rect.left + 'px';
                            this.popup.classList.toggle('open');
                        });

                        document.addEventListener('click', () => this.popup.classList.remove('open'));
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
                 *  INICIALIZADOR UNIVERSAL DE QUILL
                 ********************************************/
                const editors = document.querySelectorAll('.quill-editor');
                const quillInstances = {};

                editors.forEach(editor => {

                    const textareaId = editor.dataset.target;
                    const textarea = document.getElementById(textareaId);

                    var quill = new Quill(editor, {
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
                                ['emoji']
                            ],
                            emojiPicker: true
                        }
                    });

                    quillInstances[textareaId] = quill;

                    // Cargar contenido inicial
                    let contenidoLimpio = limpiarContenidoQuill(textarea.value);
                    quill.root.innerHTML = contenidoLimpio;

                    // Sincronizar cambios
                    quill.on('text-change', function() {
                        textarea.value = quill.root.innerHTML;
                    });
                });

                /********************************************
                 *  GUARDAR AL PULSAR BOTÓN
                 ********************************************/
                document.getElementById('btn-guardar').addEventListener('click', function() {
                    Object.keys(quillInstances).forEach(id => {
                        const quill = quillInstances[id];
                        const textarea = document.getElementById(id);
                        textarea.value = quill.root.innerHTML;
                    });
                });
            </script>

    </body>

</html>