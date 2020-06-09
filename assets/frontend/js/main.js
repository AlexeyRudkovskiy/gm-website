import axios from 'axios'

(() => {
    let map;

    window.initMap = () => {
        let location = { lat: 52.5050293, lng: 13.3402111 };
        map = new google.maps.Map(document.querySelector('#map'), {
            center: location,
            zoom: 17
        });

        const marker = new google.maps.Marker({
            position: location,
            map: map
        });
    };

    window.addEventListener('load', () => {
        const projectsAndProducts = document.querySelectorAll('.grid-item');
        const contentMargin = 20;
        const popup = document.querySelector('.popup-container');
        const popupContent = popup.querySelector('.popup-content .content');
        const popupClose = popup.querySelector('.popup-header .close-button');
        const contactForm = document.querySelector('#contact-form');
        const slider = document.querySelector('.header-image-wrapper');
        const sliderImage = slider.querySelector('img');
        const slidesJson = slider.getAttribute('data-images');
        const slides = JSON.parse(slidesJson);
        const totalSlides = slides.length;
        let currentSlide = 0;
        let popupImage = popup.querySelector('.popup-header img');

        popupClose.addEventListener('click', () => {
            popupContent.innerHTML = '';
            popup.classList.add('hidden');
            document.body.classList.remove('no-scroll');
        });

        for (let i = 0; i < projectsAndProducts.length; i++) {
            const item = projectsAndProducts[i];
            const content = item.querySelector('.content');
            const header = item.querySelector('h2');
            const headerHeight = header.offsetHeight;

            let defaultMargin = headerHeight + contentMargin;
            content.style.marginTop = `-${defaultMargin}px`;

            item.addEventListener('mouseover', () => {
                content.style.marginTop = `-${content.offsetHeight}px`;
            });

            item.addEventListener('mouseleave', () => {
                content.style.marginTop = `-${defaultMargin}px`;
            });

            if (item.hasAttribute('data-open-popup')) {
                item.addEventListener('click', () => {
                    const content = item.querySelector('[data-popup-content]');
                    const title = item.querySelector('.content h2').innerText;
                    const contentBody = content.querySelector('.body');
                    const image = content.querySelector('[popup-header-image]');

                    const newImage = image.cloneNode(true);

                    document.body.classList.add('no-scroll');

                    popupContent.innerHTML = contentBody.innerHTML;
                    popupImage.parentElement.replaceChild(newImage, popupImage);
                    popupImage = newImage;

                    popup.classList.remove('hidden');
                });
            }
        }

        window.setInterval(() => {
            currentSlide++;
            if (currentSlide >= totalSlides) {
                currentSlide = 0;
            }

            const defaultImage = 'slider-large';
            const data = slides[currentSlide];

            // sliderImage.src = `/uploads/photos/${data[defaultImage]}`;
        }, 1000);

        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            axios.post(`/api/contact`, formData)
                .then(response => response.data)
                .then(response => alert(response.message))
                .then(() => e.target.reset())
                .catch(() => alert('Something went wrong'));
        });
    });

})();
