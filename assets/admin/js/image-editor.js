import React, {Component} from 'react'
import ReactDOM from 'react-dom'
import axios from 'axios'


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

function showEditor() {
    ReactDOM.render(<ImageEditorComponent />, imageEditor);
}

export default function imageEditor() {
    window.showEditor = showEditor;

    const popup = document.querySelector('.popup-container');
    const imageEditor = popup.querySelector('.image-editor');
    const popupClose = popup.querySelector('.popup-content-header .close');
    const imagesToEdit = document.querySelectorAll('.edit-photo');
    for (let i = 0; i < imagesToEdit.length; i++) {
        const image = imagesToEdit[i];
        image.addEventListener('click', async () => {
            popup.classList.remove('hidden');

            const photoJson = image.getAttribute('data-photo');
            const photo = JSON.parse(photoJson);
            const sizes = await axios.get('/dashboard/images/sizes')
                .then(response => response.data);

            ReactDOM.render(<ImageEditorComponent photo={photo.original} files={photo.files} sizes={sizes} />, imageEditor);
        });
    }

    popupClose.addEventListener('click', () => popup.classList.add('hidden'));

}

class InputField extends Component {

    constructor(props) {
        super(props);

        this.state = {
            isInFocus: false,
            prefixWidth: 0
        };

        this.randomNumber = Math.floor(Math.random() * 10000);
    }

    updateFocus(isFocus) {
        return () => {
            this.setState({
                isInFocus: isFocus
            });
        }
    }

    componentDidMount() {
        let prefixWidth = 0;
        this.setState({ prefixWidth });
    }

    render() {
        const {placeholder, label, value, type, onChange, prefix, suffix, disabled, customProps} = this.props;
        const {isInFocus, prefixWidth} = this.state;

        const isPrefixExists = prefix !== null && typeof prefix !== "undefined" && prefix.length > 0;
        const isSuffixExists = suffix !== null && typeof suffix !== "undefined" && suffix.length > 0;

        return <div className={['modern-input-field', (isInFocus || (!isInFocus && value !== null) || (value !== null && value.length > 0)) ? 'active' : null].join(' ')}>
            {isPrefixExists && <span className="modern-input-field-prefix" ref="prefix">{prefix}</span>}
            <label style={{marginLeft: `${typeof this.refs.prefix !== "undefined" ? this.refs.prefix.offsetWidth : 0}px`}} for={`input-${this.randomNumber}`}>{label}</label>
            <input type={type}
                   placeholder={(isInFocus || (!isInFocus && value !== null) || (value !== null && value.length < 1)) ? placeholder : null}
                   value={value || ''}
                   onChange={onChange}
                   className={["modern-input-field-element", isPrefixExists ? 'with-prefix' : null, isSuffixExists ? 'with-suffix' : null].join(' ')}
                   onFocus={this.updateFocus(true)}
                   onBlur={this.updateFocus(false)}
                   disabled={disabled} id={`input-${this.randomNumber}`} {...customProps} />
            {isSuffixExists && <span className="modern-input-field-suffix">{suffix}</span>}
        </div>
    }

}

export class ImageEditorComponent extends Component {

    constructor(props) {
        super(props);

        this.imageWidth = 0;
        this.imageHeight = 0;
        this.scaleWidth = 0;
        this.scaleHeight = 0;

        const {photo, sizes, files} = props;

        const sizesMap = {};

        for (const size of sizes) {
            sizesMap[size.name] = {
                width: size.width,
                height: size.height,
                left: 0,
                top: 0,
                scale: 100,
                _width: 0,
                _height: 0
            };
        }

        console.log(files);

        this.state = {
            sizes, photo, sizesMap, files,
            active: '',
            text: null
        };

        console.log(sizes);
    }

    processPreview() {
        let parent = this.refs.image.parentElement.parentElement;

        this.imageWidth = this.refs.image.offsetWidth;
        this.imageHeight = this.refs.image.offsetHeight;

        if (parent.offsetWidth < this.imageWidth) {
            this.refs.image.style.maxWidth = `${parent.offsetWidth}px`;
        }

        if (parent.offsetHeight < this.imageHeight) {
            this.refs.image.style.maxHeight = `${parent.offsetHeight}px`;
        }

        this.imageWidth = this.refs.image.offsetWidth;
        this.imageHeight = this.refs.image.offsetHeight;

        const originalWidth = this.state.photo.width;
        const originalHeight = this.state.photo.height;

        this.scaleWidth = this.imageWidth / originalWidth;
        this.scaleHeight = this.imageHeight / originalHeight;

        // const {width, height} = calculateOverlaySizes(originalWidth, originalHeight, this.imageWidth, this.imageHeight, 1920, 1080, 'fit');

        // this.refs.overlay.style.width = `${width}px`;
        // this.refs.overlay.style.height = `${height}px`;
    };

    componentDidMount() {
        // const originalImage = imageEditor.querySelector('.original-image');
        // const imageContainer = originalImage.querySelector('.image-container');
        // const image = imageContainer.querySelector('img');
        // const overlay = imageContainer.querySelector('.overlay');

        let imageWidth = 0;
        let imageHeight = 0;

        let startPosition = { x: 0, y: 0, top: 0, left: 0, _x: 0, _y: 0, started: false };

        const {overlay} = this.refs;

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

            let newX = overlay.style.offsetLeft;
            let newY = overlay.style.offsetTop;

            if (overlay.offsetLeft + overlay.offsetWidth <= this.imageWidth && overlay.offsetLeft >= 0) {
                newX = e.clientX - startPosition._x - startPosition.x + startPosition.left;
            } else if (overlay.offsetLeft < 0) {
                newX = 0;
            } else if (overlay.offsetLeft + overlay.offsetWidth > this.imageWidth) {
                newX = this.imageWidth - overlay.offsetWidth;
            }

            if (overlay.offsetHeight + overlay.offsetTop <= this.imageHeight && overlay.offsetTop >= 0) {
                newY = e.clientY - startPosition._y - startPosition.y + startPosition.top;
            } else if (overlay.offsetTop < 0) {
                newY = 0;
            } else if (overlay.offsetHeight + overlay.offsetTop > this.imageHeight) {
                newY = this.imageHeight - overlay.offsetHeight;
            }

            const {sizesMap, active} = this.state;
            sizesMap[active].top = newY;
            sizesMap[active].left = newX;

            this.setState({ sizesMap });
        });

        overlay.addEventListener('mouseleave', () => {
            startPosition.started = false;
        });

        overlay.addEventListener('mouseover', () => {
            if (startPosition.x !== 0 && startPosition.y !== 0 && startPosition._x !== 0 && startPosition._y !== 0) {
                startPosition.started = true;
            }
        });

        // this.processPreview();
    }

    isActive(name) {
        return this.state.active === name;
    }

    changeCurrentThumbnail(size) {
        return () => {
            const width = Number(size.width) * this.scaleWidth;
            const height = Number(size.height) * this.scaleHeight;

            // this.refs.overlay.style.width = `${width}px`;
            // this.refs.overlay.style.height = `${height}px`;
            this.refs.overlay.style.left = `0px`;
            this.refs.overlay.style.top = `0px`;

            const {sizesMap} = this.state;

            let scale = sizesMap[size.name].scale / 100;

            sizesMap[size.name]._width = width * scale;
            sizesMap[size.name]._height = height * scale;

            this.setState({ active: size.name, sizesMap });
        }
    }

    updateText(e) {
        this.setState({ text: e.target.value });
    }

    getCurrentSize() {
        let current = this.state.sizesMap[this.state.active];
        if (typeof current === "undefined") {
            return {
                left: 0, top: 0, width: 0, height: 0
            };
        }
        return current;
    }

    updateProp(prop, _map, args) {
        return (e) => {
            const valueRaw = e.target.value;
            let value = typeof _map !== "undefined" ? _map.call(null, valueRaw) : valueRaw;

            if (isNaN(Number(value))) {
                value = Math.floor(value);
            }

            const {active, sizesMap} = this.state;
            sizesMap[active][prop] = value;

            if (prop === 'scale') {
                const _scale = sizesMap[active].scale / 100;
                sizesMap[active]._width = sizesMap[active].width * _scale * this.scaleWidth;
                sizesMap[active]._height = sizesMap[active].height * _scale * this.scaleHeight;
            }
            this.setState({ sizesMap });
        }
    }

    dump() {
        const {sizesMap} = this.state;
        const copy = {};

        for (const name in sizesMap) {
            const size = sizesMap[name];

            copy[name] = {
                width: size.width,
                height: size.height,
                top: size.top / this.scaleWidth,
                left: size.left / this.scaleHeight,
                scale: Number(size.scale)
            }
        }

        const formData = new FormData();
        for (const name in copy) {
            for (const key in copy[name]) {
                if (!isNaN(copy[name][key])) {
                    formData.append(`update[${name}][${key}]`, Math.floor(copy[name][key]));
                } else {
                    formData.append(`update[${name}][${key}]`, copy[name][key]);
                }
            }
        }

        formData.append('original[files]', JSON.stringify(this.state.files));
        formData.append('original[filename]', this.state.photo.filename);
        formData.append('original[width]', this.state.photo.width);
        formData.append('original[height]', this.state.photo.height);

        axios.post(`/dashboard/images/generate-thumbnails`, formData)
            .then(response => response.data)
            .then(response => alert('Image successfully updated!'));
    }

    render() {
        const current = this.getCurrentSize();

        return <>
            <div className="original-image">
                <div className="image-container">
                    <img src={`/uploads/originals/${this.state.photo.filename}`} ref="image" onLoad={this.processPreview.bind(this)} />
                    <div className="overlay" ref="overlay" style={{
                        top: current.top,
                        left: current.left,
                        width: current._width,
                        height: current._height
                    }}></div>
                </div>
            </div>
            <div className="image-settings">
                {this.state.sizes.map((size, index) => <div key={index}
                                                            className={['image-settings-item', this.isActive(size.name) ? 'active' : ''].join(' ')}>
                    <div className={['image-settings-item-title', this.isActive(size.name) ? 'active' : ''].join(' ')}
                         onClick={this.changeCurrentThumbnail(size)}>
                        <span>{size.name}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" className="image-settings-item-icon"><path d="M0 0h24v24H0z" fill="none"/><path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6z"/></svg>
                    </div>
                    <div className={['image-settings-inner', this.isActive(size.name) ? null : 'invisible'].join(' ')}>
                        <InputField label={'Width'} type={'text'} value={size.width} disabled={true} suffix={'px'} isVisible={this.isActive(size.name)} />
                        <InputField label={'Height'} type={'text'} value={size.height} disabled={true} suffix={'px'} isVisible={this.isActive(size.name)} />
                        <InputField label={'Scale'}
                                    type={'number'}
                                    value={current.scale}
                                    suffix={'%'}
                                    isVisible={this.isActive(size.name)}
                                    onChange={this.updateProp('scale')}
                                    customProps={{ step: 1, min: 1 }} />
                    </div>
                </div>)}
                <a href='javascript:' onClick={() => this.dump()} className={'button'} style={{marginTop: 16, width: '100%'}}>Update</a>
            </div>
        </>
    }

}

