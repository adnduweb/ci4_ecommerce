<div class="row">
    <label class="col-xl-3"></label>
    <div class="col-lg-9 col-xl-6">
        <h3 class="kt-section__title kt-section__title-sm"><?= lang('Core.detail'); ?>:</h3>
    </div>
</div>

<div class="form-group form-group-sm row">
    <label class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.on_sale')); ?></label>
    <div class="col-lg-9 col-xl-6">
        <span class="kt-switch kt-switch--icon">
            <label>
                <input type="checkbox" <?= ($form->on_sale == true) ? 'checked="checked"' : ''; ?> name="on_sale" value="1">
                <span></span>
            </label>
        </span>
    </div>
</div>

<div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>
<h3 class="text-dark font-weight-bold mb-10"><?= ucfirst(lang('Core.états et références')); ?> </h3>
<div class="form-group row">
    <div class="col-xl-4 mx-auto">
        <div class="form-group row">
            <label for="isbn" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.isbn')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <input class="form-control" type="text" value="<?= old('isbn') ? old('isbn') : $form->isbn; ?>" name="isbn" id="isbn">
                <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mx-auto">
        <div class="form-group row">
            <label for="ean13" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.ean13')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <input class="form-control" type="text" value="<?= old('ean13') ? old('ean13') : $form->ean13; ?>" name="ean13" id="ean13">
                <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mx-auto">
        <div class="form-group row">
            <label for="upc" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.upc')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <input class="form-control" type="text" value="<?= old('upc') ? old('upc') : $form->upc; ?>" name="upc" id="upc">
                <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
            </div>
        </div>
    </div>
</div>
<div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>
<h3 class="text-dark font-weight-bold mb-10"><?= ucfirst(lang('Core.quantités')); ?></h3>
<div class="form-group row">
    <div class="col-xl-4 mx-auto">
        <div class="form-group row">
            <label for="quantity" class="col-xl-3 col-lg-3 col-form-label"><?= ucfirst(lang('Core.quantity')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <input class="form-control kt_qty bootstrap-touchspin-vertical-btn" type="text" value="<?= old('quantity') ? old('quantity') : $form->quantity; ?>" name="quantity" id="quantity">
                <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mx-auto">

        <div class="form-group row">
            <label for="price" class="col-xl-4 col-lg-4 col-form-label"><?= ucfirst(lang('Core.quantity_minimal')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <input class="form-control kt_qty bootstrap-touchspin-vertical-btn" type="text" value="<?= old('quantity_minimal') ? old('quantity_minimal') : $form->quantity_minimal; ?>" name="quantity_minimal" id="quantity_minimal">
                <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>
<h3 class="text-dark font-weight-bold mb-10"><?= ucfirst(lang('Core.prix')); ?></h3>
<div class="form-group row">
    <div class="col-xl-4 mx-auto">
        <div class="form-group row">
            <label for="wholesale_price" class="col-xl-4 col-lg-4 col-form-label"><?= ucfirst(lang('Core.wholesale_price')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <div class="input-group">
                    <input class="form-control kt_price bootstrap-touchspin-vertical-btn" type="text" value="<?= old('wholesale_price') ? old('wholesale_price') : $form->wholesale_price; ?>" name="wholesale_price" id="wholesale_price">
                    <div class="input-group-append">
                        <span class="input-group-text"><?= $currencyDefault->symbol; ?></span>
                    </div>
                </div>
                <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mx-auto">
        <div class="form-group row">
            <label for="price" class="col-xl-4 col-lg-4 col-form-label"><?= ucfirst(lang('Core.price')); ?> HT : </label>
            <div class="col-lg-9 col-xl-6">
                <div class="input-group">
                    <input class="form-control kt_price bootstrap-touchspin-vertical-btn" type="text" value="<?= old('price') ? old('price') : $form->price; ?>" name="price" id="price">
                    <div class="input-group-append">
                        <span class="input-group-text"><?= $currencyDefault->symbol; ?></span>
                    </div>
                </div>
                <div class="invalid-feedback"><?= lang('Core.this_field_is_requis'); ?> </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 mx-auto">
        <div class="form-group row">
            <label for="taxe_rules_group_id" class="col-xl-4 col-lg-4 col-form-label"><?= ucfirst(lang('Core.devise')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <select name="taxe_rules_group_id" class="form-control kt-selectpicker" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="taxe_rules_group_id">
                    <option data-rates="0" <?= ($form->taxe_rules_group_id == '0') ? 'selected' : ''; ?> value="0"><?= ucfirst(lang('Core.aucune')); ?></option>
                    <?php foreach ($taxes as $taxe) { ?>
                        <option data-rates="<?= $taxe->rate; ?>" <?= ($form->id_taxe == $taxe->id) ? 'selected' : ''; ?> value="<?= $taxe->id; ?>"><?= ucfirst($taxe->getBName()); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
</div>





<div class="kt-separator kt-separator--border-dashed kt-separator--portlet-fit kt-separator--space-lg"></div>
<h3 class="text-dark font-weight-bold mb-10"><?= ucfirst(lang('Core.marques et fournisseurs ')); ?></h3>
<div class="form-group row">
    <div class="col-xl-6 mx-auto">
        <div class="form-group row">
            <label for="brand_id" class="col-xl-4 col-lg-4 col-form-label"><?= ucfirst(lang('Core.brands')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <select name="brand_id" class="form-control kt-selectpicker" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="brand_id">
                    <option <?= ($form->brand_id == '0') ? 'selected' : ''; ?> value="0"><?= ucfirst(lang('Core.aucune')); ?></option>
                    <?php foreach ($brands as $brand) { ?>
                        <option <?= ($form->brand_id == $brand->id) ? 'selected' : ''; ?> value="<?= $brand->id; ?>"><?= ucfirst($brand->getBName()); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="col-xl-6 mx-auto">
        <div class="form-group row">
            <label for="supplier_id" class="col-xl-4 col-lg-4 col-form-label"><?= ucfirst(lang('Core.brands')); ?> : </label>
            <div class="col-lg-9 col-xl-6">
                <select name="supplier_id" class="form-control kt-selectpicker" title="<?= ucfirst(lang('Core.choose_one_of_the_following')); ?>" id="supplier_id">
                    <option <?= ($form->supplier_id == '0') ? 'selected' : ''; ?> value="0"><?= ucfirst(lang('Core.aucune')); ?></option>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option <?= ($form->supplier_id == $supplier->id) ? 'selected' : ''; ?> value="<?= $supplier->id; ?>"><?= ucfirst($supplier->getBName()); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
</div>