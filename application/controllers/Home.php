<?php defined('BASEPATH') or exit('No direct script access allowed');

class Home extends Operator_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pages = 'home';
    }

    public function index($page = null)
    {
        $tulisan_dashboard = $this->home->get('setting');

        // menampilkan kategori yang open saja
        $current_date   = strtotime(date('Y-m-d'));
        $all_categories = $this->home->order_by('category_name')->get_all_where("category_status = 'y'", 'category');
        foreach ($all_categories as $key) {
            $close_date = $key->date_close;
            $close_date = strtotime($close_date);

            if ($current_date >= $close_date) {
                $data = array('category_status' => 'n');
                $this->home->where('category_id', $key->category_id)->update($data, 'category');
            }
        }

        $cekusername   = $this->session->userdata('username');
        $ceklevel      = $this->session->userdata('level');
        $drafts        = array();
        $count         = array();
        $categories    = '';
        $drafts_newest = '';

        //menampilkan info sesuai level
        if ($ceklevel == 'superadmin' or $ceklevel == 'admin_penerbitan') {
            $count['tot_category'] = $this->home->count('category');
            $count['tot_draft']    = $this->home->count('draft');
            $count['tot_book']     = $this->home->count('book');
            $count['tot_author']   = $this->home->count('author');
            $count['tot_reviewer'] = $this->home->count('reviewer');
            //sedang desk screening dan lolos desk screening
            $count['draft_desk'] = $this->home->where('draft_status', 1)->or_where('draft_status', 0)->count('draft');
            //sedang review
            $count['draft_review'] = $this->home->where('is_review', 'n')->where('is_edit', 'n')->where('is_layout', 'n')->where('is_proofread', 'n')->where('is_print', 'n')->where('draft_status', '4')->count('draft');
            //lolos review
            $count['draft_review_lolos'] = $this->home->where('is_review', 'y')->where('draft_status', '5')->count('draft');
            //sedang edit
            $count['draft_edit'] = $this->home->where('is_review', 'y')->where('is_edit', 'n')->where('is_layout', 'n')->where('is_proofread', 'n')->where('is_print', 'n')->where_not('draft_status', '99')->count('draft');
            //sedang layout
            $count['draft_layout'] = $this->home->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'n')->where('is_proofread', 'n')->where('is_print', 'n')->where_not('draft_status', '99')->count('draft');
            //sedang proofread
            $count['draft_proofread'] = $this->home->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'n')->where('is_print', 'n')->where_not('draft_status', '99')->count('draft');
            //sedang cetak
            $count['draft_cetak'] = $this->home->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'y')->group_start()->where('is_print', 'n')->or_where('is_print', 'y')->group_end()->where_not('draft_status', '99')->where_not('draft_status', '14')->count('draft');
            //final
            $count['draft_final'] = $this->home->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'y')->where('is_print', 'y')->where('is_reprint', 'n')->where('draft_status', '14')->count('draft');
            //cetak ulang
            $count['draft_cetak_ulang'] = $this->home->where('is_reprint', 'y')->count('draft');

            //$count['draft_approved'] = $count['draft_desk_lolos']+$count['draft_review_lolos'];
            $count['draft_in_progress']    = $count['draft_edit'] + $count['draft_layout'] + $count['draft_proofread'] + $count['draft_cetak'];
            $count['draft_rejected_total'] = $this->home->where('draft_status', '2')->or_where('draft_status', '99')->count('draft');
        } elseif ($ceklevel == 'reviewer') {
            $drafts        = $this->home->join_table('draft_reviewer', 'draft', 'draft')->join_table('reviewer', 'draft_reviewer', 'reviewer')->join_table('user', 'reviewer', 'user')->where('user.username', $cekusername)->get_all('draft');
            $drafts_newest = $this->home->join_table('draft_reviewer', 'draft', 'draft')->join_table('reviewer', 'draft_reviewer', 'reviewer')->join_table('user', 'reviewer', 'user')->where('user.username', $cekusername)->limit(5)->order_by('entry_date', 'desc')->get_all('draft');

            foreach ($drafts_newest as $key => $value) {
                $rev        = $this->home->get_id_and_name('reviewer', 'draft_reviewer', $value->draft_id, 'draft');
                $value->rev = key(array_filter(
                    $rev,
                    function ($e) {
                        return $e->reviewer_id == $this->session->userdata('role_id');
                    }
                ));

                if ($value->rev == 0) {
                    if (!empty($value->review1_deadline)) {
                        $value->deadline = $value->review1_deadline;
                        $value->flag     = $value->review1_flag;
                    }
                } elseif ($value->rev == 1) {
                    if (!empty($value->review2_deadline)) {
                        $value->deadline = $value->review2_deadline;
                        $value->flag     = $value->review2_flag;
                    }
                }
            }

            $count_sudah  = 0;
            $count_belum  = 0;
            $count_sedang = 0;
            $count_total  = count($drafts);

            foreach ($drafts as $key => $value) {
                $rev        = $this->home->get_id_and_name('reviewer', 'draft_reviewer', $value->draft_id, 'draft');
                $value->rev = key(array_filter(
                    $rev,
                    function ($e) {
                        return $e->reviewer_id == $this->session->userdata('role_id');
                    }
                ));

                if ($value->rev == 0) {
                    if ($value->review1_flag != '') {
                        $count_sudah++;
                    } elseif ($value->review1_notes == '') {
                        $count_belum++;
                    } else {
                        $count_sedang++;
                    }
                }
                if ($value->rev == 1) {
                    if ($value->review2_flag != '') {
                        $count_sudah++;
                    } elseif ($value->review2_notes == '') {
                        $count_belum++;
                    } else {
                        $count_sedang++;
                    }
                }
            }

            $count['count_sudah']  = $count_sudah;
            $count['count_sedang'] = $count_sedang;
            $count['count_belum']  = $count_belum;
            $count['count_total']  = $count_total;
        } elseif ($ceklevel == 'author') {
            $categories               = $this->home->order_by('category_name')->get_all_where("category_status = 'y'", 'category');
            $count['draft_total']     = $this->home->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where('user.username', $cekusername)->count('draft');
            $count['draft_desk']      = $this->home->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where('user.username', $cekusername)->where('draft_status', '0')->count('draft');
            $count['draft_review']    = $this->home->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where('user.username', $cekusername)->where('draft_status', '4')->where('is_review', 'n')->count('draft');
            $count['draft_edit']      = $this->home->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where('user.username', $cekusername)->where('is_review', 'y')->where('is_edit', 'n')->where_not('draft_status', '99')->count('draft');
            $count['draft_layout']    = $this->home->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where('user.username', $cekusername)->where('is_edit', 'y')->where('is_layout', 'n')->where_not('draft_status', '99')->count('draft');
            $count['draft_proofread'] = $this->home->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where('user.username', $cekusername)->where('is_layout', 'y')->where('is_proofread', 'n')->where_not('draft_status', '99')->count('draft');
            $count['draft_approved']  = $this->home->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where_not('draft_status', '99')->where_not('draft_status', '2')->where('user.username', $cekusername)->count('draft');
            $count['draft_rejected']  = $this->home->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where('draft_status', '99')->where('user.username', $cekusername)->count('draft');
            $count['draft_book']      = $this->home->join_table('draft', 'book', 'draft')->join_table('draft_author', 'draft', 'draft')->join_table('author', 'draft_author', 'author')->join_table('user', 'author', 'user')->where('user.username', $cekusername)->count('book');
        } elseif ($ceklevel == 'editor') {
            $count['draft_total']    = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where('user.username', $cekusername)->count('draft');
            $count['draft_desk']     = $this->home->where('draft_status', 0)->count('draft');
            $count['draft_sudah']    = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where_not('edit_notes', '')->where('user.username', $cekusername)->count('draft');
            $count['draft_belum']    = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where('edit_notes', '')->where_not('draft_status', 99)->where('user.username', $cekusername)->count('draft');
            $count['draft_approved'] = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where('is_edit', 'y')->where('user.username', $cekusername)->count('draft');
            $count['draft_rejected'] = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where('is_edit', 'n')->where('draft_status', 99)->where('user.username', $cekusername)->count('draft');
        } elseif ($ceklevel == 'layouter') {
            $count['draft_total']    = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where('user.username', $cekusername)->count('draft');
            $count['draft_desk']     = $this->home->where('draft_status', 0)->count('draft');
            $count['draft_sudah']    = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where_not('layout_notes', '')->where('user.username', $cekusername)->count('draft');
            $count['draft_belum']    = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where('layout_notes', '')->where_not('draft_status', 99)->where('user.username', $cekusername)->count('draft');
            $count['draft_approved'] = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where('is_layout', 'y')->where('user.username', $cekusername)->count('draft');
            $count['draft_rejected'] = $this->home->join_table('responsibility', 'draft', 'draft')->join_table('user', 'responsibility', 'user')->where('is_layout', 'n')->where('draft_status', 99)->where('user.username', $cekusername)->count('draft');
        }

        $pages     = $this->pages;
        $main_view = 'home/index';
        $this->load->view('template', compact('tulisan_dashboard', 'categories', 'count', 'drafts_newest', 'drafts', 'pages', 'main_view'));
    }
}