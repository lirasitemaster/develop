<?php defined('isCMS') or die; ?>

<style>
table th {
	background: #eee;
}
table th,
table td {
	border: 1px solid #ccc;
	padding: 0.25em;
	vertical-align: top;
}
td {
	min-width: 0;
}
td[data-json="1"] {
	min-width: 50vw;
}
th {
	vertical-align: top!important;
}
th:before,
th:after {
	bottom: auto!important;
	top: 0.75em!important;
}
button.dt-button.buttons-enable {
	background: #ff0000!important;
}
.toggle-container {
	list-style: none;
	margin: 20px 0;
	padding: 0;
	clear: both;
}
.toggle-vis {
	display: inline-block;
	padding: 5px 10px;
	border: 1px solid;
	cursor: pointer;
	margin-bottom: 4px;
}
.toggle-inactive {
	color: #ccc;
	border-color: #ccc;
}
</style>
