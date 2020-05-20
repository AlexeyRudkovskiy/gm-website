import SimpleMDE from 'simplemde'

export default function () {
    const editors = document.querySelectorAll('.form-group textarea.form-control');
    for (let i = 0; i < editors.length; i++) {
        const editor = editors[i];
        const simpleMdeEditor = new SimpleMDE({
            element: editor,
            forceSync: true
        });
        simpleMdeEditor.value(editor.innerText);
    }
}
