<?php
class Warehouse_sales_controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->is_login) {
            redirect('auth');
        }

        if (!is_admin() && $_SESSION['level'] == "admin_gudang" && $_SESSION['level']=='admin_pemasaran') {
            $this->session->set_flashdata('error', $this->lang->line('toast_error_not_authorized'));
            redirect();
        }
    }
}
