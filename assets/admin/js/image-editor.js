function calculateOverlaySizes(originalWidth, originalHeight, imageWidth, imageHeight, targetWidth, targetHeight, mode) {
    let width = 0;
    let height = 0;

    if (mode === 'fit') {
        width = 100;
        height = 120;
    }

    return {width, height};
}

function findScale(originalWidth, originalHeight, targetWidth, targetHeight, mode) {
    const scaleX = 0;
    const scaleY = 0;

    if (mode === 'fit') {
        const biggestOriginal = originalWidth / originalHeight;
        let biggestTarget = targetWidth / targetHeight;
        const isWidthGreater = biggestOriginal > 1;
        const isTargetWidthGreater = biggestTarget > 1;
    }

    return {scaleX, scaleY};
}

export default function imageEditor() {
    const imageEditor = document.querySelector('.image-editor');

    if (imageEditor !== null) {
        const originalImage = imageEditor.querySelector('.original-image');
        const imageContainer = originalImage.querySelector('.image-container');
        const image = imageContainer.querySelector('img');
        const overlay = imageContainer.querySelector('.overlay');

        let imageWidth = 0;
        let imageHeight = 0;

        let processPreview = () => {
            imageWidth = image.offsetWidth;
            imageHeight = image.offsetHeight;

            const originalWidth = 7360;
            const originalHeight = 4912;

            const scaleWidth = imageWidth / originalWidth;
            const scaleHeight = imageHeight / originalHeight;

            const {width, height} = calculateOverlaySizes(originalWidth, originalHeight, imageWidth, imageHeight, 1920, 1080, 'fit');

            overlay.style.width = `${width}px`;
            overlay.style.height = `${height}px`;
        };

        let startPosition = { x: 0, y: 0, top: 0, left: 0, _x: 0, _y: 0, started: false };

        overlay.addEventListener('mousedown', (e) => {
            const boundingClientRect = overlay.getBoundingClientRect();
            let _x = Math.ceil(boundingClientRect.x);
            let _y = Math.ceil(boundingClientRect.y);

            const x = e.clientX - _x;
            const y = e.clientY - _y;

            startPosition.x = x;
            startPosition.y = y;
            startPosition._x = _x;
            startPosition._y = _y;
            startPosition.left = overlay.offsetLeft;
            startPosition.top = overlay.offsetTop;
            startPosition.started = true;
        });

        overlay.addEventListener('mouseup', () => {
            startPosition.x = 0;
            startPosition.y = 0;
            startPosition._x = 0;
            startPosition._y = 0;
            startPosition.started = false;
        });

        overlay.addEventListener('mousemove', (e) => {
            if (!startPosition.started) {
                return;
            }

            if (overlay.offsetLeft + overlay.offsetWidth >= imageWidth) {
                startPosition.started = false;
                overlay.style.left = `${imageWidth - overlay.offsetWidth - 1}px`;
                return;
            }

            if (overlay.offsetLeft < 0) {
                startPosition.started = false;
                overlay.style.left = `0px`;
                return;
            }

            if (overlay.offsetHeight + overlay.offsetTop >= imageHeight) {
                startPosition.started = false;
                overlay.style.top = `${imageHeight - overlay.offsetHeight - 1}px`;
                return;
            }

            if (overlay.offsetTop < 0) {
                startPosition.started = false;
                overlay.style.top = `0px`;
                return;
            }

            let newX = e.clientX - startPosition._x - startPosition.x + startPosition.left;
            let newY = e.clientY - startPosition._y - startPosition.y + startPosition.top;

            overlay.style.top = `${newY}px`;
            overlay.style.left = `${newX}px`;
        });

        overlay.addEventListener('mouseleave', () => {
            startPosition.started = false;
        });

        overlay.addEventListener('mouseover', () => {
            if (startPosition.x !== 0 && startPosition.y !== 0 && startPosition._x !== 0 && startPosition._y !== 0) {
                startPosition.started = true;
            }
        });

        if (image.complete) {
            processPreview();
        } else {
            image.addEventListener('load', processPreview);
        }

    }
}
