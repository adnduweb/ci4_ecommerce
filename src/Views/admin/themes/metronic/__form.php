<?= $this->extend('/admin/themes/metronic/__layouts/layout_1') ?>
<?= $this->section('main') ?>
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
	<?= form_open_multipart('', ['id' => 'kt_apps_user_add_user_form', 'class' => 'kt-form', 'novalidate' => false]); ?>
	<input type="hidden" name="action" value="<?= $action; ?>" />
	<input type="hidden" name="module" value="<?= base64_encode('Adnduweb\Ci4_ecommerce'); ?>" />
	<input type="hidden" name="controller" value="AdminProductController" />


	<?= $this->include('/admin/themes/metronic/__partials/kt_form_toolbar') ?>

	<!-- begin:: Content -->
	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
		<div class="kt-portlet kt-portlet--tabs">
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-toolbar">
					<ul class="nav nav-tabs nav-tabs-space-xl nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#kt_user_edit_tab_1" role="tab">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24" />
										<path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero" />
										<path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3" />
									</g>
								</svg> <?= lang('Core.tab_general'); ?>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-toggle="tab" href="#kt_user_edit_tab_2" role="tab">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24" />
										<rect opacity="0.3" x="7" y="4" width="10" height="4" />
										<path d="M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z M8,12 C8.55228475,12 9,11.5522847 9,11 C9,10.4477153 8.55228475,10 8,10 C7.44771525,10 7,10.4477153 7,11 C7,11.5522847 7.44771525,12 8,12 Z M8,16 C8.55228475,16 9,15.5522847 9,15 C9,14.4477153 8.55228475,14 8,14 C7.44771525,14 7,14.4477153 7,15 C7,15.5522847 7.44771525,16 8,16 Z M12,12 C12.5522847,12 13,11.5522847 13,11 C13,10.4477153 12.5522847,10 12,10 C11.4477153,10 11,10.4477153 11,11 C11,11.5522847 11.4477153,12 12,12 Z M12,16 C12.5522847,16 13,15.5522847 13,15 C13,14.4477153 12.5522847,14 12,14 C11.4477153,14 11,14.4477153 11,15 C11,15.5522847 11.4477153,16 12,16 Z M16,12 C16.5522847,12 17,11.5522847 17,11 C17,10.4477153 16.5522847,10 16,10 C15.4477153,10 15,10.4477153 15,11 C15,11.5522847 15.4477153,12 16,12 Z M16,16 C16.5522847,16 17,15.5522847 17,15 C17,14.4477153 16.5522847,14 16,14 C15.4477153,14 15,14.4477153 15,15 C15,15.5522847 15.4477153,16 16,16 Z M16,20 C16.5522847,20 17,19.5522847 17,19 C17,18.4477153 16.5522847,18 16,18 C15.4477153,18 15,18.4477153 15,19 C15,19.5522847 15.4477153,20 16,20 Z M8,18 C7.44771525,18 7,18.4477153 7,19 C7,19.5522847 7.44771525,20 8,20 L12,20 C12.5522847,20 13,19.5522847 13,19 C13,18.4477153 12.5522847,18 12,18 L8,18 Z M7,4 L7,8 L17,8 L17,4 L7,4 Z" fill="#000000" />
									</g>
								</svg> <?= lang('Core.tab_information'); ?>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-toggle="tab" href="#kt_user_edit_tab_3" role="tab">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24" />
										<path d="M8,4 C8.55228475,4 9,4.44771525 9,5 L9,17 L18,17 C18.5522847,17 19,17.4477153 19,18 C19,18.5522847 18.5522847,19 18,19 L9,19 C8.44771525,19 8,18.5522847 8,18 C7.44771525,18 7,17.5522847 7,17 L7,6 L5,6 C4.44771525,6 4,5.55228475 4,5 C4,4.44771525 4.44771525,4 5,4 L8,4 Z" fill="#000000" opacity="0.3" />
										<rect fill="#000000" opacity="0.3" x="11" y="7" width="8" height="8" rx="4" />
										<circle fill="#000000" cx="8" cy="18" r="3" />
									</g>
								</svg> <?= lang('Core.tab_carrier'); ?>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " data-toggle="tab" href="#kt_user_edit_tab_4" role="tab">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24" />
										<path d="M8,4 C8.55228475,4 9,4.44771525 9,5 L9,17 L18,17 C18.5522847,17 19,17.4477153 19,18 C19,18.5522847 18.5522847,19 18,19 L9,19 C8.44771525,19 8,18.5522847 8,18 C7.44771525,18 7,17.5522847 7,17 L7,6 L5,6 C4.44771525,6 4,5.55228475 4,5 C4,4.44771525 4.44771525,4 5,4 L8,4 Z" fill="#000000" opacity="0.3" />
										<rect fill="#000000" opacity="0.3" x="11" y="7" width="8" height="8" rx="4" />
										<circle fill="#000000" cx="8" cy="18" r="3" />
									</g>
								</svg> <?= lang('Core.tab_vue'); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="kt-portlet__body">
				<div class="tab-content">
					<div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
						<div class="kt-form kt-form--label-right">
							<div class="kt-form__body">
								<div class="kt-section kt-section--first">
									<div class="kt-section__body">
										<?= $this->include('\Adnduweb\Ci4_ecommerce\Views\admin\themes\metronic\__form_section\general') ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="kt_user_edit_tab_2" role="tabpanel">
						<div class="kt-form kt-form--label-right">
							<div class="kt-form__body">
								<div class="kt-section kt-section--first">
									<div class="kt-section__body">
										<?= $this->include('\Adnduweb\Ci4_ecommerce\Views\admin\themes\metronic\__form_section\information') ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="kt_user_edit_tab_3" role="tabpanel">
						<div class="kt-form kt-form--label-right">
							<div class="kt-form__body">
								<div class="kt-section kt-section--first">
									<div class="kt-section__body">
										<?= $this->include('\Adnduweb\Ci4_ecommerce\Views\admin\themes\metronic\__form_section\carrier') ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="kt_user_edit_tab_4" role="tabpanel">
						<div class="kt-form kt-form--label-right">
							<div class="kt-form__body">
								<div class="kt-section kt-section--first">
									<div class="kt-section__body">
										<?= $this->include('\Adnduweb\Ci4_ecommerce\Views\admin\themes\metronic\__form_section\vue') ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- end:: Content -->
	<?= form_close(); ?>
</div>
<!-- <div id=app>
	<template>
		<form @submit.prevent="submit" class="vld-parent" ref="formContainer">
			 your form inputs goes here-->
<!-- <label><input type="checkbox" v-model="fullPage">Full page?</label>
			<button type="submit">Login</button>
		</form>

		<div class="container">
			<example-modal />
			<div>
			<Logo />
			<h1 class="title">
				nuxt
			</h1>
			<div class="links">
				<button class="button--green" @click="click">Show modal</button>
			</div>
			</div>
		</div>

	</template>
</div> -->

<div id="app">
	<template>
		<form @submit.prevent="submit" class="vld-parent" ref="formContainer">
			<!-- your form inputs goes here-->
			<label><input type="checkbox" v-model="fullPage">Full page?</label>
			<button type="submit">Login</button>
		</form>
	</template>
</div>


<!-- <form id="app3" @submit="checkForm" action="https://vuejs.org/" method="post">

	<p v-if="errors.length">
		<b>Please correct the following error(s):</b>
		<ul>
			<li v-for="error in errors">{{ error }}</li>
		</ul>
	</p>

	<p>
		<label for="name">Name</label>
		<input id="name" v-model="name" type="text" name="name">
	</p>

	<p>
		<label for="age">Age</label>
		<input id="age" v-model="age" type="number" name="age" min="0">
	</p>

	<p>
		<label for="movie">Favorite Movie</label>
		<select id="movie" v-model="movie" name="movie">
			<option>Star Wars</option>
			<option>Vanilla Sky</option>
			<option>Atomic Blonde</option>
		</select>
	</p>

	<p>
		<input type="submit" value="Submit">
	</p>

</form> -->

<?= $this->endSection() ?>

<?= $this->section('extra-js') ?>
<script type="text/javascript">
	"use strict";
	var KTCkeditor = {
		init: function() {
			<?php
			$i = 1;
			foreach ($supportedLocales as $k => $v) { ?>
				ClassicEditor.create(document.querySelector("#description_<?= $k; ?>"), {
					ui: '<?= $k; ?>',
					language: '<?= $k; ?>'
				}).then(e => {
					console.log(e)
				}).
				catch(e => {
					console.error(e)
				}) <?php if ($i != count($supportedLocales)) { ?>, <?php } ?>

			<?php $i++;
			} ?>

		}
	};
	jQuery(document).ready(function() {
		KTCkeditor.init()
	});
</script>

<?= $this->endSection() ?>