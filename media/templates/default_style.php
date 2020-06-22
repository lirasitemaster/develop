<?php defined('isENGINE') or die; ?>
<style type="text/css">

.<?= $prefix; ?>mainslider,
.<?= $prefix; ?>slider,
.<?= $prefix; ?>gallery {
	position: relative;
	margin-bottom: 20px;
}

.<?= $prefix; ?>mainslider + .<?= $prefix; ?>slider .slick-slide {
	margin: 0 0.25em;
}

.<?= $prefix; ?>mainslider__item,
.<?= $prefix; ?>slider__item,
.<?= $prefix; ?>gallery__item {
	
	position: relative;
	
	<?php if (!empty($sets['special']) && $sets['special'] === 'background') : ?>
	/* for background */
	/* none */
	
	<?php else : ?>
	/* for image */
	height: 500px;
	overflow: hidden;
	
	<?php endif; ?>
}

.<?= $prefix; ?>mainslider + .<?= $prefix; ?>slider .<?= $prefix; ?>slider__item {
	height: calc(200px + 4em);
	/*background: rgb(30,30,30);*/
	background: transparent;
}

/* images */

.<?= $prefix; ?>mainslider__image,
.<?= $prefix; ?>slider__image,
.<?= $prefix; ?>gallery__image {
	
	<?php if (!empty($sets['special']) && $sets['special'] === 'background') : ?>
	/* for background */
	width: 100%;
	height: 500px;
	background-size: cover;
	background-position: 50% 50%;
	background-repeat: no-repeat;
	
	<?php else : ?>
	/* for center image */
	position: relative;
	width: auto;
	height: 100%;
	left: 50%;
	transform: translateX(-50%);
	
	/* for right image */
	/*
	width: auto;
	height: 100%;
	float: right;
	*/
	
	<?php endif; ?>
}

.<?= $prefix; ?>mainslider + .<?= $prefix; ?>slider .<?= $prefix; ?>slider__image {
	height: 200px;
}

.<?= $prefix; ?>mainslider__image + .<?= $prefix; ?>gallery__thumbs,
.<?= $prefix; ?>slider__image + .<?= $prefix; ?>gallery__thumbs,
.<?= $prefix; ?>gallery__image + .<?= $prefix; ?>gallery__thumbs {
	display: none;
}

/* gallery */

.<?= $prefix; ?>gallery {
	line-height: 0;
	font-size: 0;
}

.<?= $prefix; ?>gallery__item {
	width: 30%;
	height: 100px;
	display: inline-block;
	margin-right: 1%;
	margin-bottom: 1%;
}

.<?= $prefix; ?>gallery__image {
	height: 100%;
}

/* mansory */

.<?= $prefix; ?>mansory__container {
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
}

.<?= $prefix; ?>mansory__column {
	display: flex;
	flex-direction: column;
	flex: 1 1 auto;
}

.<?= $prefix; ?>mansory__item {
	width: 100%;
}

/* tiles */

.<?= $prefix; ?>tiles .<?= $prefix; ?>mansory__container {
	flex-direction: column;
	flex-wrap: wrap;
}

.<?= $prefix; ?>tiles .<?= $prefix; ?>mansory__column {
	flex-direction: row;
	flex-wrap: nowrap;
}

.<?= $prefix; ?>tiles .<?= $prefix; ?>mansory__item {
	width: 100%;
}

/* captions */

.<?= $prefix; ?>mainslider__caption,
.<?= $prefix; ?>slider__caption {
	position: absolute;
	z-index: 10;
	top: auto;
	bottom: 48px;
	left: 50%;
	right: auto;
	transform: translateX(-50%);
	padding: 1em;
	display: block;
	background: rgba(0,0,0,0);
	font-size: 1em;
	font-weight: 900;
	text-align: center;
	color: transparent;
	transition: 0.5s all ease 0.5s;
}

.slick-slide.slick-active .<?= $prefix; ?>mainslider__caption,
.slick-slide.slick-active .<?= $prefix; ?>slider__caption {
	background: rgba(0,0,0,0.5);
	color: #fff;
}

.<?= $prefix; ?>mainslider + .<?= $prefix; ?>slider .<?= $prefix; ?>slider__caption {
	top: calc(200px + 0.5em);
	bottom: 0.5em;
	left: 0em;
	right: 0em;
	transform: translateX(0);
	padding: 0.5em;
	background: rgb(30,30,30);
	font-size: 0.75em;
	font-weight: 400;
	transition: none;
}

/* controls */

.<?= $prefix; ?>mainslider__control.slick-arrow,
.<?= $prefix; ?>slider__control.slick-arrow {
	position: absolute;
	top: calc(50% - 22px);
	bottom: auto;
	transform: translateY(-50%);
	display: block;
	z-index: 10;
	width: 50px;
	height: 50%;
	transition: 0.5s all ease;
}

.<?= $prefix; ?>mainslider__control.slick-arrow {
	opacity: 0;
}
.<?= $prefix; ?>slider__control.slick-arrow:hover {
	background: rgba(30,30,30,0.5);
}

.<?= $prefix; ?>slider__control.slick-arrow {
	background: transparent;
}
.<?= $prefix; ?>mainslider__control.slick-arrow:hover {
	opacity: 1;	
}

.<?= $prefix; ?>mainslider__control--prev,
.<?= $prefix; ?>slider__control--prev {
	left: 0;
	right: auto;
}
.<?= $prefix; ?>mainslider__control--next,
.<?= $prefix; ?>slider__control--next {
	left: auto;
	right: 0;
}

.<?= $prefix; ?>slider__control--dots {
	position: absolute;
	left: 0;
	right: 0;
	top: auto;
	bottom: 16px;
	display: block;
	height: 16px;
}

.<?= $prefix; ?>mainslider + .<?= $prefix; ?>slider .<?= $prefix; ?>slider__control--dots {
	position: relative;
	left: auto;
	right: auto;
	top: auto;
	bottom: auto;
}

.slick-dots {
	position: absolute;
	bottom: 0;
	display: block;
	width: 100%;
	padding: 0;
	margin: 0;
	list-style: none;
	text-align: center;
}

.slick-dots li {
	position: relative;
	display: inline-block;
}

.slick-dots li button {
	font-size: 0;
	line-height: 0;
	display: block;
	width: 0;
	height: 0;
	padding: 4px;
	margin: 0 5px;
	cursor: pointer;
	color: transparent;
	outline: none;
	border: 2px solid white;
	background: transparent;
	border-radius: 100%;
}

.slick-dots li.slick-active button {
	background: white;
}

/* thumbnails */

.fancybox-thumbs {
	top: auto;
	bottom: 0px;
	left: 0px;
	right: 0;
	width: 100%;
	padding: 20px;
	margin: 0;
	background: rgb(30,30,30);
}

.fancybox-show-thumbs .fancybox-inner {
	right: 0;
}

.fancybox-is-fullscreen .fancybox-thumbs {
	visibility: hidden;
}

.fancybox-thumbs__list {
	margin: auto;
}

.fancybox-thumbs__list a::before {
	border: 4px solid white;
}

.fancybox-caption {
	top: 20px;
	bottom: auto;
	left: 50%;
	right: auto;
	transform: translateX(-50%);
	width: auto;
	padding: 10px 30px;
	margin: 0;
	background: rgb(30,30,30);
}

.fancybox-bg {
	background: rgb(30, 30, 30);
	opacity: 0;
	transition-duration: inherit;
	transition-property: opacity;
	transition-timing-function: cubic-bezier(.47, 0, .74, .71);
}

.fancybox-is-open .fancybox-bg {
	opacity: .9;
	transition-timing-function: cubic-bezier(.22, .61, .36, 1);
}

.fancybox-button span {
	display: block;
	width: 100%;
	height: 100%;
	position: relative;
}

/* icons */

.<?= $prefix; ?>mainslider__control.slick-arrow::before,
.<?= $prefix; ?>slider__control.slick-arrow::before,
.fancybox-button span::before {
	position: absolute;
	top: 50%;
	bottom: auto;
	left: 0;
	right: 0;
	transform: translateY(-50%);
	color: #fff;
	font-size: 20px;
	line-height: 20px;
	text-align: center;
	display: block;
	visibility: visible;
	font-family: 'Font Awesome 5 Free', 'FontAwesome';
	font-weight: 900;
}

.fancybox-button--close span::before {
	content: '\f00d';
}
.fancybox-close-small {
	right: 0;
}
.fancybox-close-small span::before {
	content: '\f00d';
	/*content: '\f057';*/
}
.<?= $prefix; ?>mainslider__control--prev::before,
.<?= $prefix; ?>slider__control--prev::before,
.fancybox-button--arrow_left span::before {
	content: '\f104';
}
.<?= $prefix; ?>mainslider__control--next::before,
.<?= $prefix; ?>slider__control--next::before,
.fancybox-button--arrow_right span::before {
	content: '\f105';
}
.fancybox-button--download span::before {
	content: '\f019';
}
.fancybox-button--zoom span::before {
	content: '\f002';
}

/* play f04b, pause f04c */

/* preload and initialisation */

.<?= $prefix; ?>slider__container.preload,
.<?= $prefix; ?>mainslider__container.preload,
.<?= $prefix; ?>contentslider__container.preload {
	visibility: hidden;
}
.slick-slider.slick-initialized {
	visibility: visible;
}

</style>