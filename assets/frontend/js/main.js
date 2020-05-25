(() => {
    let map;

    window.initMap = () => {
        map = new google.maps.Map(document.querySelector('#map'), {
            center: {lat: -34.397, lng: 150.644},
            zoom: 8
        });
    };

    window.addEventListener('load', () => {
        const projectsAndProducts = document.querySelectorAll('.grid-item');
        const contentMargin = 20;
        const popup = document.querySelector('.popup-container');
        const popupContent = popup.querySelector('.popup-content .content');
        const popupClose = popup.querySelector('.popup-header .close-button');
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
                    const image = content.querySelector('img[popup-header-image]');

                    const newImage = image.cloneNode();

                    document.body.classList.add('no-scroll');

                    popupContent.innerHTML = contentBody.innerHTML;
                    popupImage.parentElement.replaceChild(newImage, popupImage);
                    popupImage = newImage;

                    popup.classList.remove('hidden');
                });
            }
        }
    });

})();
