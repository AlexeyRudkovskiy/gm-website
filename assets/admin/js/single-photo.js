import axios from 'axios'

export default function () {
    const photos = document.querySelectorAll('.single-photo-container');

    for (let i = 0; i < photos.length; i++) {
        const photo = photos[i];
        const img = photo.querySelector('img');
        const deleteUrl = photo.getAttribute('data-delete-url');
        const regenerateUrl = photo.getAttribute('data-regenerate-url');

        photo.querySelector('.delete-image').addEventListener('click', () => {
            axios.delete(deleteUrl)
                .then(response => response.data)
                .then(() => {
                    photo.parentElement.removeChild(photo);
                })
                .then(() => alert('Success'));
        });

        photo.querySelector('.generate-image').addEventListener('click', () => {
            axios.get(regenerateUrl)
                .then(response => response.data)
                .then(response => img.setAttribute('src', response.image['admin-preview']))
                .then(() => alert('Success'));
        });
    }

}
