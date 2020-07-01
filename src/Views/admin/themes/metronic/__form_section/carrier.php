<div class="row">
    <label class="col-xl-3"></label>
    <div class="col-lg-9 col-xl-6">
        <h3 class="kt-section__title kt-section__title-sm"><?= lang('Core.carrier'); ?>:</h3>
    </div>
</div>

<div class="form-group row">
    <label for="width" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.width')); ?> : </label>
    <div class="col-lg-9 col-xl-6">
        <div class="input-group">
            <input class="form-control kt_carrier bootstrap-touchspin-vertical-btn" type="text" value="<?= old('width') ? old('width') : $form->width; ?>" name="width" id="width">
            <div class="input-group-append">
                <span class="input-group-text">cm</span>
            </div>
        </div>
        <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
    </div>
</div>

<div class="form-group row">
    <label for="height" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.height')); ?> : </label>
    <div class="col-lg-9 col-xl-6">
        <div class="input-group">
            <input class="form-control kt_carrier bootstrap-touchspin-vertical-btn" type="text" value="<?= old('height') ? old('height') : $form->height; ?>" name="height" id="height">
            <div class="input-group-append">
                <span class="input-group-text">cm</span>
            </div>
        </div>
        <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
    </div>
</div>


<div class="form-group row">
    <label for="depth" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.depth')); ?> : </label>
    <div class="col-lg-9 col-xl-6">
        <div class="input-group">
            <input class="form-control kt_carrier bootstrap-touchspin-vertical-btn" type="text" value="<?= old('depth') ? old('depth') : $form->depth; ?>" name="depth" id="depth">
            <div class="input-group-append">
                <span class="input-group-text">grs</span>
            </div>
        </div>
        <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
    </div>
</div>


<div class="form-group row">
    <label for="weight" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.weight')); ?> : </label>
    <div class="col-lg-9 col-xl-6">

        <div class="input-group">
            <input class="form-control kt_carrier bootstrap-touchspin-vertical-btn" type="text" value="<?= old('weight') ? old('weight') : $form->weight; ?>" name="weight" id="weight">
            <div class="input-group-append">
                <span class="input-group-text">grs</span>
            </div>
        </div>
        <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
    </div>
</div>