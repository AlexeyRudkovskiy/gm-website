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
        }
    });

})();
