/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

import tabs from './tabs'
import gallery from "./gallery";
import singlePhoto from './single-photo'
import simplemde from './simplemde'
import imageEditor from './image-editor'

(() => {
    const functions = [ tabs, gallery, singlePhoto, simplemde, imageEditor ];

    window.addEventListener('load', () => {

        functions.forEach(func => func.call(window));

    });
})();
