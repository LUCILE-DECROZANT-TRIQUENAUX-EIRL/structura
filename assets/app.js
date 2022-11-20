/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// -- require jQuery and make it globally accessible --- //
import $ from 'jquery';
global.$ = global.jQuery = window.jQuery = $;

// -- require Bootstrap -- //
require('bootstrap');

// -- require select2 -- //
require('select2');

// -- require datatables dependencies -- //
// jszip
require('jszip');
// pdfmake
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;

// -- require datatables -- //
require('datatables.net-bs4');
require('datatables.net-buttons-bs4');
require('datatables.net-buttons/js/buttons.html5.js');
require('datatables.net-colreorder-bs4');
require('datatables.net-fixedcolumns-bs4');
require('datatables.net-fixedheader-bs4');
require('datatables.net-responsive-bs4');
require('datatables.net-rowgroup-bs4');
require('datatables.net-select-bs4');

// start the Stimulus application
import './bootstrap';
