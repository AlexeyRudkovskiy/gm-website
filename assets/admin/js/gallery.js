import axios from 'axios'
import Sortable from 'sortablejs';

export default function () {

    const galleries = document.querySelectorAll('.gallery');
    console.log(galleries);

    for (let i = 0; i < galleries.length; i++) {
        const gallery = galleries[i];
        const updateUrl = gallery.getAttribute('data-update-url');
        const deleteUrl = gallery.getAttribute('data-delete-url');
        const regenerateUrl = gallery.getAttribute('data-regenerate-url');

        const sortable = Sortable.create(gallery, {
            dataIdAttr: 'data-id',
            filter: '.updating-overlay',
            onUpdate: (e) => {
                const updateData = {};
                const childs = gallery.querySelectorAll('.single-photo');
                const formData = new FormData();

                for (let index = 0; index < childs.length; index++) {
                    const child = childs[index];
                    const currentIndex = child.getAttribute('data-index');
                    child.setAttribute('data-index', index);
                    child.getElementsByClassName('delete-image')[0].setAttribute('data-index', index);
                    child.getElementsByClassName('generate-image')[0].setAttribute('data-index', index);
                    formData.append(`orders[${currentIndex}]`, index);
                }

                gallery.classList.add('show-updating-overlay');

                axios.post(updateUrl, formData)
                    .then(response => response.data)
                    .then(() => gallery.classList.remove('show-updating-overlay'));
            }
        });

        const deletePhotoLinks = gallery.querySelectorAll('.delete-image');
        const generatePhotoLinks = gallery.querySelectorAll('.generate-image');
        for (let j = 0; j < deletePhotoLinks.length; j++) {
            const deletePhoto = deletePhotoLinks[j];
            deletePhoto.addEventListener('click', () => {
                const photoIndex = deletePhoto.getAttribute('data-index');
                const formData = new FormData();

                gallery.classList.add('show-updating-overlay');
                axios.delete(`${deleteUrl}?index=${photoIndex}`)
                    .then(response => response.data)
                    .then(response => {
                        const childToRemove = gallery.querySelector(`.single-photo[data-index="${photoIndex}"]`);
                        gallery.removeChild(childToRemove);
                        gallery.classList.remove('show-updating-overlay');

                        const childs = gallery.querySelectorAll('.single-photo');
                        for (let index = 0; index < childs.length; index++) {
                            const child = childs[index];
                            child.setAttribute('data-index', index);
                            child.getElementsByClassName('delete-image')[0].setAttribute('data-index', index);
                            child.getElementsByClassName('generate-image')[0].setAttribute('data-index', index);
                        }
                    });
            });
        }

        for (let j = 0; j < generatePhotoLinks.length; j++) {
            const generatePhotoLink = generatePhotoLinks[j];
            generatePhotoLink.addEventListener('click', () => {
                const photoIndex = generatePhotoLink.getAttribute('data-index');

                gallery.classList.add('show-updating-overlay');
                axios.get(`${regenerateUrl}?index=${photoIndex}`)
                    .then(response => response.data)
                    .then(response => {
                        gallery.classList.remove('show-updating-overlay');
                    });
            });
        }
    }

}
