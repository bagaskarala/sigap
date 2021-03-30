<?php
$is_add_customer = $this->uri->segment(2) == 'add';
?>

<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url(); ?>"><span class="fa fa-home"></span></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('customer'); ?>">Customer</a>
            </li>
            <li class="breadcrumb-item">
                <a class="text-muted">Form</a>
            </li>
        </ol>
    </nav>
</header>
<div class="page-section">
    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <div class="card-body">
                    <form action="<?= base_url('customer/update_customer/' . $customer->customer_id . ''); ?>" method="POST">
                    <fieldset>
                        <legend>Form Edit Customer</legend>
                        <div class="form-group">
                            <label
                                for="name"
                                class="font-weight-bold"
                            >
                                Nama
                                <abbr title="Required">*</abbr>
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                value="<?= $customer->name ?>"
                            />
                        </div>
                        <div class="form-group">
                            <label
                                for="address"
                                class="font-weight-bold"
                            >Alamat
                                <abbr title="Required">*</abbr>
                            </label>
                            <input
                                type="text"
                                name="address"
                                id="address"
                                class="form-control"
                                value="<?= $customer->address ?>"
                            />
                        </div>
                        <div class="form-group">
                            <label
                                for="phone-number"
                                class="font-weight-bold"
                            >Nomor Telepon
                                <abbr title="Required">*</abbr>
                            </label>
                            <input
                                type="text"
                                name="phone-number"
                                id="phone-number"
                                class="form-control"
                                value="<?= $customer->phone_number ?>"
                            />
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label
                                        for="type"
                                        class="font-weight-bold"
                                    >Jenis Customer<abbr title="Required">*</abbr></label>

                                    <?= form_dropdown('type', $customer_type, $customer->type, 'id="type" class="form-control custom-select d-block"'); ?>
                                </div>
                            </div>
                            <!-- <?php if (!$is_add_user) : ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <?= $this->lang->line('form_user_is_blocked'); ?>
                                            <abbr title="Required">*</abbr>
                                        </label>
                                        <div class="form-group">
                                            <div class="custom-control custom-radio">
                                                <?= form_radio('is_blocked', 'n', isset($input->is_blocked) && ($input->is_blocked == 'n') ? true : false, ' class="custom-control-input" id="category_status1"'); ?>
                                                <label
                                                    class="custom-control-label"
                                                    for="category_status1"
                                                >Aktif</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <?= form_radio('is_blocked', 'y', isset($input->is_blocked) && ($input->is_blocked == 'y') ? true : false, 'class="custom-control-input" id="category_status2"'); ?>
                                                <label
                                                    class="custom-control-label"
                                                    for="category_status2"
                                                >Nonaktif</label>
                                            </div>
                                            <?= form_error('is_blocked'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?> -->
                        </div>
                        <hr>
                    </fieldset>
                    <hr>
                    <div class="form-actions">
                        <button
                            class="btn btn-primary ml-auto"
                            type="submit"
                        >Submit</button>
                    </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
