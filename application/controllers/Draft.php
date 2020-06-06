<?php defined('BASEPATH') or exit('No direct script access allowed');
require('application/libraries/phpspreadsheet/vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Draft extends Operator_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->pages = 'draft';
        \PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(true);
        $this->load->library('jagolibrary');
    }

    public function index($page = null)
    {
        $ceklevel = $this->session->userdata('level');
        $cekusername = $this->session->userdata('username');
        //menampilkan sessuai level user
        if ($ceklevel == 'author') {
            $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('user.username', $cekusername)->paginate($page)->getAll();
            $tot = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('user.username', $cekusername)->getAll();
        } elseif ($ceklevel == 'editor' || $ceklevel == 'layouter') {
            $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('user.username', $cekusername)->paginate($page)->getAll();
            $tot = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('user.username', $cekusername)->getAll();
        } elseif ($ceklevel == 'reviewer') {
            $drafts = $this->draft->join('category')->join('theme')->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('user', 'reviewer', 'user')->where('user.username', $cekusername)->paginate($page)->getAll();
            $tot = $this->draft->join('category')->join('theme')->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('user', 'reviewer', 'user')->where('user.username', $cekusername)->getAll();
        } else {
            $drafts = $this->draft->join('category')->join('theme')->orderBy('draft_status')->orderBy('entry_date', 'desc')->paginate($page)->getAll();
            $tot = $this->draft->join('category')->join('theme')->getAll();
        }
        //tampilkan author dan status draft
        foreach ($drafts as $key => $value) {
            $authors = $this->draft->getIdAndName('author', 'draft_author', $value->draft_id);
            $value->author = $authors;
            $value->stts = $value->draft_status;
            $value->draft_status = $this->checkStatus($value->draft_status);
        }
        //cari tau rev 1 atau rev 2 yg sedang login
        foreach ($drafts as $key => $value) {
            $rev = $this->draft->getIdAndName('reviewer', 'draft_reviewer', $value->draft_id);
            $value->rev = key(array_filter($rev, function ($e) {
                return $e->reviewer_id == $this->session->userdata('role_id');
            }));
            if ($value->rev == 0) {
                $value->review_flag = $value->review1_flag;
                $value->deadline = $value->review1_deadline;
            } elseif ($value->rev == 1) {
                $value->review_flag = $value->review2_flag;
                $value->deadline = $value->review2_deadline;
            } else {
            }
        }
        $total = count($tot);
        $pages = $this->pages;
        $main_view = 'draft/index_draft';
        $pagination = $this->draft->makePagination(site_url('draft'), 2, $total);
        $this->load->view('template', compact('pages', 'main_view', 'drafts', 'pagination', 'total'));
    }

    public function filter($page = null)
    {
        //filter category
        $category = $this->input->get('category', true);
        $kat = $this->checkFilter($category);
        //filter cetak ulang
        $reprint = $this->input->get('reprint', true);
        $cek_reprint = $this->checkReprint($reprint);

        //filter tahapan
        $filter = $this->input->get('filter', true);
        //custom per page
        if ($this->input->get('per_page', true) != null) {
            $this->draft->perPage = $this->input->get('per_page', true);
        }
        $this->db->group_by('draft.draft_id');
        if ($this->level == 'reviewer') {
            if ($filter == 'sudah') {
                $drafts = array();
                $drafts_source = $this->draft->join('category')->join('theme')->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('user', 'reviewer', 'user')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                //cari tau rev 1 atau rev 2 yg sedang login
                foreach ($drafts_source as $key => $value) {
                    $rev = $this->draft->getIdAndName('reviewer', 'draft_reviewer', $value->draft_id);
                    $value->rev = key(array_filter($rev, function ($e) {
                        return $e->reviewer_id == $this->session->userdata('role_id');
                    }));
                    if ($value->rev == 0) {
                        $value->review_flag = $value->review1_flag;
                    } elseif ($value->rev == 1) {
                        $value->review_flag = $value->review2_flag;
                    } else {
                    }
                    if ($value->review_flag != '') {
                        $drafts[] = $value;
                    }
                    $total = count($drafts);
                }
            } elseif ($filter == 'belum') {
                $drafts = array();
                $drafts_source = $this->draft->join('category')->join('theme')->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('user', 'reviewer', 'user')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                //cari tau rev 1 atau rev 2 yg sedang login
                foreach ($drafts_source as $key => $value) {
                    $rev = $this->draft->getIdAndName('reviewer', 'draft_reviewer', $value->draft_id);
                    $value->rev = key(array_filter($rev, function ($e) {
                        return $e->reviewer_id == $this->session->userdata('role_id');
                    }));
                    if ($value->rev == 0) {
                        $value->review_flag = $value->review1_flag;
                    } elseif ($value->rev == 1) {
                        $value->review_flag = $value->review2_flag;
                    } else {
                    }
                    if ($value->review_flag == '') {
                        $drafts[] = $value;
                    }
                    $total = count($drafts);
                }
            }
        } elseif ($this->level == 'editor') {
            if ($filter == 'sudah') {
                $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->whereNot('edit_notes', '')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->whereNot('edit_notes', '')->where('user.username', $this->username)->count();
            } elseif ($filter == 'belum') {
                $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('edit_notes', '')->whereNot('draft_status', 99)->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('edit_notes', '')->whereNot('draft_status', 99)->where('user.username', $this->username)->count();
            } elseif ($filter == 'approve') {
                $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('is_edit', 'y')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('is_edit', 'y')->where('user.username', $this->username)->count();
            } elseif ($filter == 'reject') {
                $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('is_edit', 'n')->where('draft_status', 99)->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $tot = $this->draft->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('is_edit', 'n')->where('draft_status', 99)->where('user.username', $this->username)->getAll();
                $total = count($tot);
            } else {
                redirect(base_url('draft'));
            }
        } elseif ($this->level == 'layouter') {
            if ($filter == 'sudah') {
                $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->whereNot('layout_notes', '')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->whereNot('layout_notes', '')->where('user.username', $this->username)->count();
            } elseif ($filter == 'belum') {
                $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('layout_notes', '')->whereNot('draft_status', 99)->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('layout_notes', '')->whereNot('draft_status', 99)->where('user.username', $this->username)->count();
            } elseif ($filter == 'approve') {
                $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('is_layout', 'y')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('is_layout', 'y')->where('user.username', $this->username)->count();
            } elseif ($filter == 'reject') {
                $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('is_layout', 'n')->where('draft_status', 99)->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $tot = $this->draft->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('is_layout', 'n')->where('draft_status', 99)->where('user.username', $this->username)->getAll();
                $total = count($tot);
            } else {
                redirect(base_url('draft'));
            }
        } elseif ($this->level == 'author') {
            if ($filter == 'desk-screening') {
                $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('draft_status', '0')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('draft_status', '0')->where('user.username', $this->username)->count();
            } elseif ($filter == 'review') {
                $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_review', 'n')->where('draft_status', '4')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_review', 'n')->where('draft_status', '4')->where('user.username', $this->username)->count();
            } elseif ($filter == 'edit') {
                $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_review', 'y')->where('is_edit', 'n')->whereNot('draft_status', '99')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_review', 'y')->where('is_edit', 'n')->whereNot('draft_status', '99')->where('user.username', $this->username)->count();
            } elseif ($filter == 'layout') {
                $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_edit', 'y')->where('is_layout', 'n')->whereNot('draft_status', '99')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_edit', 'y')->where('is_layout', 'n')->whereNot('draft_status', '99')->where('user.username', $this->username)->count();
            } elseif ($filter == 'cover') {
                $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_layout', 'y')->where('is_cover', 'n')->whereNot('draft_status', '99')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_edit', 'y')->where('is_layout', 'n')->whereNot('draft_status', '99')->where('user.username', $this->username)->count();
            } elseif ($filter == 'proofread') {
                $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_proofread', 'n')->where('is_layout', 'y')->whereNot('draft_status', '99')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('is_proofread', 'n')->where('is_layout', 'y')->whereNot('draft_status', '99')->where('user.username', $this->username)->count();
            } elseif ($filter == 'reject') {
                $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('draft_status', '99')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('draft_status', '99')->where('user.username', $this->username)->count();
            } elseif ($filter == 'final') {
                $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('draft_status', '14')->where('user.username', $this->username)->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('draft_status', '14')->where('user.username', $this->username)->count();
            } else {
                redirect(base_url('draft'));
            }
        } else {
            if ($filter == 'desk-screening') {
                $drafts = $this->draft->join('category')->join('theme')->group_start()->where('draft_status', 1)->orWhere('draft_status', 0)->group_end()->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->group_start()->where('draft_status', 1)->orWhere('draft_status', 0)->group_end()->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'review') {
                $drafts = $this->draft->join('category')->join('theme')->where('is_review', 'n')->where('draft_status', '4')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where('is_review', 'n')->where('draft_status', '4')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'edit') {
                $drafts = $this->draft->join('category')->join('theme')->where('is_review', 'y')->where('is_edit', 'n')->whereNot('draft_status', '99')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where('is_review', 'y')->where('is_edit', 'n')->whereNot('draft_status', '99')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'layout') {
                $drafts = $this->draft->join('category')->join('theme')->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'n')->whereNot('draft_status', '99')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'n')->whereNot('draft_status', '99')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            }  elseif ($filter == 'cover') {
                $drafts = $this->draft->join('category')->join('theme')->where('is_review', 'y')->where('is_layout', 'y')->where('is_cover', 'n')->whereNot('draft_status', '99')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'n')->whereNot('draft_status', '99')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'proofread') {
                $drafts = $this->draft->join('category')->join('theme')->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'n')->whereNot('draft_status', '99')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status', 'desc')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'n')->whereNot('draft_status', '99')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'cetak') {
                $drafts = $this->draft->join('category')->join('theme')->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'y')->group_start()->where('is_print', 'n')->orWhere('is_print', 'y')->group_end()->whereNot('draft_status', '99')->whereNot('draft_status', '14')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'y')->group_start()->where('is_print', 'n')->orWhere('is_print', 'y')->group_end()->whereNot('draft_status', '99')->whereNot('draft_status', '14')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'reject') {
                $drafts = $this->draft->join('category')->join('theme')->group_start()->where('draft_status', '99')->orWhere('draft_status', '2')->group_end()->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->group_start()->where('draft_status', '99')->orWhere('draft_status', '2')->group_end()->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'final') {
                $drafts = $this->draft->join('category')->join('theme')->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'y')->where('is_print', 'y')->where('is_reprint', 'n')->where('draft_status', '14')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'y')->where('is_print', 'y')->where('is_reprint', 'n')->where('draft_status', '14')->where($cek_reprint['cond_temp'], $cek_reprint['stts'])->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'cetak-ulang') {
                $drafts = $this->draft->join('category')->join('theme')->where('is_reprint', 'y')->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where('is_reprint', 'y')->where($kat['cond_temp'], $kat['category'])->count();
            } elseif ($filter == 'error') {
                //inisialisasi array penampung kondisi not in
                $desk_screening = [''];
                $review = [''];
                $edit = [''];
                $layout = [''];
                $proofread = [''];
                $final = [''];
                $cetak_ulang = [''];
                $ditolak = [''];

                //menghitung filter lain, untuk mencari draft yang error
                $desk_screenings = $this->draft->select(['draft_id'])->where('draft_status', 1)->orWhere('draft_status', 0)->getAll();
                foreach ($desk_screenings as $value) {
                    $desk_screening[] = $value->draft_id;
                }
                $reviews = $this->draft->select(['draft_id'])->where('is_review', 'n')->where('is_edit', 'n')->where('is_layout', 'n')->where('is_proofread', 'n')->where('is_print', 'n')->where('draft_status', '4')->getAll();
                foreach ($reviews as $value) {
                    $review[] = $value->draft_id;
                }
                $edits = $this->draft->select(['draft_id'])->where('is_review', 'y')->where('is_edit', 'n')->where('is_layout', 'n')->where('is_proofread', 'n')->where('is_print', 'n')->whereNot('draft_status', '99')->getAll();
                foreach ($edits as $value) {
                    $edit[] = $value->draft_id;
                }
                $layouts = $this->draft->select(['draft_id'])->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'n')->where('is_proofread', 'n')->where('is_print', 'n')->whereNot('draft_status', '99')->getAll();
                foreach ($layouts as $value) {
                    $layout[] = $value->draft_id;
                }
                $proofreads = $this->draft->select(['draft_id'])->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'n')->where('is_print', 'n')->whereNot('draft_status', '99')->getAll();
                foreach ($proofreads as $value) {
                    $proofread[] = $value->draft_id;
                }
                $prints = $this->draft->select(['draft_id'])->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'y')->group_start()->where('is_print', 'n')->orWhere('is_print', 'y')->group_end()->whereNot('draft_status', '99')->whereNot('draft_status', '14')->getAll();
                foreach ($prints as $value) {
                    $print[] = $value->draft_id;
                }
                $finals = $this->draft->select(['draft_id'])->where('is_review', 'y')->where('is_edit', 'y')->where('is_layout', 'y')->where('is_proofread', 'y')->where('is_print', 'y')->where('is_reprint', 'n')->where('draft_status', 14)->getAll();
                foreach ($finals as $value) {
                    $final[] = $value->draft_id;
                }
                $cetak_ulangs = $this->draft->select(['draft_id'])->where('is_reprint', 'y')->getAll();
                foreach ($cetak_ulangs as $value) {
                    $cetak_ulang[] = $value->draft_id;
                }
                $rejecteds = $this->draft->select(['draft_id'])->where('draft_status', 2)->orWhere('draft_status', 99)->getAll();
                foreach ($rejecteds as $value) {
                    $rejected[] = $value->draft_id;
                }

                $drafts = $this->draft->join('category')->join('theme')->whereNotIn('draft_id', $desk_screening)->whereNotIn('draft_id', $review)->whereNotIn('draft_id', $edit)->whereNotIn('draft_id', $layout)->whereNotIn('draft_id', $proofread)->whereNotIn('draft_id', $print)->whereNotIn('draft_id', $final)->whereNotIn('draft_id', $cetak_ulang)->whereNotIn('draft_id', $rejected)->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();

                $total = $this->draft->whereNotIn('draft_id', $desk_screening)->whereNotIn('draft_id', $review)->whereNotIn('draft_id', $edit)->whereNotIn('draft_id', $layout)->whereNotIn('draft_id', $proofread)->whereNotIn('draft_id', $print)->whereNotIn('draft_id', $final)->whereNotIn('draft_id', $cetak_ulang)->whereNotIn('draft_id', $rejected)->where($kat['cond_temp'], $kat['category'])->count();
            } else {
                //get semua draft jik filter gagal semua
                $drafts = $this->draft->join('category')->join('theme')->joinRelationMiddle('draft', 'draft_author')->joinRelationDest('author', 'draft_author')->where($kat['cond_temp'], $kat['category'])->orderBy('draft_status')->orderBy('draft_title')->paginate($page)->getAll();
                $total = $this->draft->where($kat['cond_temp'], $kat['category'])->count();
            }
        }
        $pagination = $this->draft->makePagination(site_url('draft/filter/'), 3, $total);
        if (!$drafts) {
            $this->session->set_flashdata('warning', 'Data were not found');
            //redirect($this->pages);

        } else {
            foreach ($drafts as $key => $value) {
                $authors = $this->draft->getIdAndName('author', 'draft_author', $value->draft_id);
                $value->author = $authors;
                $value->stts = $value->draft_status;
                $value->draft_status = $this->checkStatus($value->draft_status);
            }
        }
        $pages = $this->pages;
        $main_view = 'draft/index_draft';
        $this->load->view('template', compact('pages', 'main_view', 'drafts', 'pagination', 'total'));
    }
    public function ajax_reload_author()
    {
        $data = $this->draft->select(['author_id', 'author_name'])->getAll('author');
        if ($data) {
            foreach ($data as $key => $value) {
                $datax[$value->author_id] = $value->author_name;
            }
            echo json_encode($datax);
        }
    }
    public function add($category = '')
    {

        // cek category tersedia dan aktif
        if ($category != '') {
            $data = array('category_id' => $category);
            $cekcategory = $this->draft->getWhere($data, 'category');
            $sisa_waktu_buka = ceil((strtotime($cekcategory->date_open) - strtotime(date('Y-m-d H:i:s'))) / 86400);
            if (!$cekcategory || $cekcategory->category_status == 'n') {
                $this->session->set_flashdata('error', 'Failed, Category not found');
                redirect('home');
            } elseif ($sisa_waktu_buka >= 1) {
                $this->session->set_flashdata('error', 'Failed, Category not yet opened');
                redirect('home');
            }
        }
        //khusus admin dan author
        $ceklevel = $this->session->userdata('level');
        if ($ceklevel != 'author' and $ceklevel != 'admin_penerbitan' and $ceklevel != 'superadmin') {
            redirect('home');
        }
        //jika user belum terdaftar sebagai penulis maka redirect
        $cekrole = $this->session->userdata('role_id');
        if ($ceklevel == 'author') {
            if ($cekrole == 0) {
                $this->session->set_flashdata('error', 'Choose category from dashboard');
                redirect('home');
            }
        }
        if (!$_POST) {
            $input = (object) $this->draft->getDefaultValues();
            $input->category_id = $category;
        } else {
            $input = (object) $this->input->post(null, true);
            //catat orang yang menginput draft
            $input->input_by = $this->session->userdata('username');
        }
        if ($this->draft->validate()) {
            if (!empty($_FILES) && $_FILES['draft_file']['size'] > 0) {
                $getextension = explode(".", $_FILES['draft_file']['name']);
                $draftFileName = str_replace(" ", "_", $input->draft_title . '_' . date('YmdHis') . "." . $getextension[1]); // draft file name
                $upload = $this->draft->uploadDraftfile('draft_file', $draftFileName);
                if ($upload) {
                    $input->draft_file = "$draftFileName"; // Data for column "draft".

                }
            }
        }
        //author tidak bisa coba2 url draft/add
        if ($ceklevel == 'author') {
            if (empty($input->category_id)) {
                $this->session->set_flashdata('error', 'Choose category from dashboard');
                redirect('home');
            }
        }
        if (!$this->draft->validate() || $this->form_validation->error_array()) {
            $pages = $this->pages;
            $main_view = 'draft/form_draft_add';
            $form_action = 'draft/add';
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }
        $draft_id = $this->draft->insert($input);

        $user_id_notif = $this->session->userdata('user_id');
        $judul_notif = 'draft baru';//
        $isi_notif = 'draft baru '.$input->draft_title;
        $data_notif = array('user_id' => $user_id_notif, 
                                'judul' => $judul_notif, 
                                'isi' => $isi_notif,
                                'draft_id' => $draft_id
                            );
        $this->db->insert('notifikasi', $data_notif);
        //firebase
        $topics = "sigap_".$this->level;
        $params_notif = array('draft_id' => $draft_id);
        $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
        $this->jagolibrary->sendNotifTopics($message_notif, $topics, $params_notif);

        $isSuccess = true;
        if ($draft_id > 0) {
            foreach ($input->author_id as $key => $value) {
                $data_author = array('author_id' => $value, 'draft_id' => $draft_id);
                if ($key == 0) {
                    $data_author['draft_author_status'] = 1;
                }
                $draft_author_id = $this->draft->insert($data_author, 'draft_author');
                if ($draft_author_id < 1) {
                    $isSuccess = false;
                    break;
                }
            }
        } else {
            $isSuccess = false;
        }
        if ($isSuccess) {
            $worksheet_num = $this->generateWorksheetNumber();
            $data_worksheet = array('draft_id' => $draft_id, 'worksheet_num' => $worksheet_num, 'worksheet_status' => 0);
            $worksheet_id = $this->draft->insert($data_worksheet, 'worksheet');
            if ($worksheet_id < 1) {
                $isSuccess = false;
            }
        }
        if ($isSuccess) {
            $this->session->set_flashdata('success', 'Data saved');
        } else {
            $this->session->set_flashdata('error', 'Data author failed to save');
        }
        redirect('draft/view/' . $draft_id);
    }
    public function view($id = null)
    {
        $draft = $this->draft->where('draft_id', $id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', 'Draft data were not available');
            redirect('draft');
        }
        //status draft
        $draft->stts = $draft->draft_status;
        $draft->draft_status = $this->checkStatus($draft->draft_status);
        // ambil tabel worksheet
        $ambil_worksheet = ['draft_id' => $id];
        $desk = $this->draft->getWhere($ambil_worksheet, 'worksheet');
        // ambil tabel worksheet
        $ambil_books = ['draft_id' => $id];
        $books = $this->draft->getWhere($ambil_books, 'book');
        //pecah data csv jadi array
        if (!empty($draft->nilai_reviewer1)) {
            $draft->nilai_reviewer1 = explode(",", $draft->nilai_reviewer1);
        }
        if (!empty($draft->nilai_reviewer2)) {
            $draft->nilai_reviewer2 = explode(",", $draft->nilai_reviewer2);
        }
        //hitung bobot nilai
        if (!empty($draft->nilai_reviewer1)) {
            $draft->nilai_total_reviewer1 = 35 * $draft->nilai_reviewer1[0] + 25 * $draft->nilai_reviewer1[1] + 10 * $draft->nilai_reviewer1[2] + 30 * $draft->nilai_reviewer1[3];
        } else {
            $draft->nilai_total_reviewer1 = '';
        }
        if (!empty($draft->nilai_reviewer2)) {
            $draft->nilai_total_reviewer2 = 35 * $draft->nilai_reviewer2[0] + 25 * $draft->nilai_reviewer2[1] + 10 * $draft->nilai_reviewer2[2] + 30 * $draft->nilai_reviewer2[3];
        } else {
            $draft->nilai_total_reviewer2 = '';
        }
        // $arrayapa = array($draft->nilai_reviewer1[0],$draft->nilai_reviewer1[1],$draft->nilai_reviewer1[2]);
        // $draft->apa = implode(",",$arrayapa);
        if (!$_POST) {
            $input = (object) $draft;
        } else {
            $input = (object) $this->input->post(null, true);
            $input->draft_file = $draft->draft_file; // Set draft file for preview.

        }
        // tabel author
        $authors = $this->draft->select(['draft_author.author_id', 'draft_author_id', 'draft_author.draft_author_status', 'author_name', 'author_nip', 'work_unit_name', 'institute_name', 'draft.draft_id'])->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('work_unit', 'author', 'work_unit')->join3('institute', 'author', 'institute')->where('draft_author.draft_id', $id)->getAll();
        //cari author yang pertama atau yang seterusnya
        $author_order = array_filter($authors, function ($e) {
            return $e->author_id == $this->role_id;
        });
        //ambil flag, 1 bisa edit, 0 yg view only
        $author_order = isset(reset($author_order)->draft_author_status) ? reset($author_order)->draft_author_status : 'none';
        // tabel reviewer
        $reviewers = $this->draft->select(['draft_reviewer.reviewer_id', 'draft_reviewer_id', 'reviewer_name', 'reviewer_nip', 'faculty_name', 'username'])->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('faculty', 'reviewer', 'faculty')->join3('user', 'reviewer', 'user')->where('draft_reviewer.draft_id', $id)->getAll();
        //cari reviewer 1 dan 2
        $reviewer_order = key(array_filter($reviewers, function ($e) {
            return $e->username == $this->session->userdata('username');
        }));
        // tampilkan editor
        $editors = $this->draft->select(['username', 'level', 'responsibility_id', 'responsibility.user_id'])->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('responsibility.draft_id', $id)->where('level', 'editor')->getAll();
        // tampilkan layouter
        $layouters = $this->draft->select(['username', 'level', 'responsibility_id', 'responsibility.user_id'])->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('responsibility.draft_id', $id)->where('level', 'layouter')->getAll();

        //hitung jumlah revisi
        $tot_revisi['editor'] = $this->draft->where('revision_role', 'editor')->where('draft_id', $id)->count('revision');
        $tot_revisi['layouter'] = $this->draft->where('revision_role', 'layouter')->where('draft_id', $id)->count('revision');
        $tot_revisi['cover'] = $this->draft->where('revision_role', 'cover')->where('draft_id', $id)->count('revision');

        //prevent ganti link
        if ($this->level == "reviewer") {
            $prevent = count(array_filter($reviewers, function ($e) {
                return $e->reviewer_id == $this->role_id;
            }));
            if ($prevent == 0) {
                $this->session->set_flashdata('warning', 'Anda tidak memiliki akses ke draft ini');
                redirect('draft');
            };
        }
        if ($this->level == "author") {
            $prevent = count(array_filter($authors, function ($e) {
                return $e->author_id == $this->role_id;
            }));
            if ($prevent == 0) {
                $this->session->set_flashdata('warning', 'Anda tidak memiliki akses ke draft ini');
                redirect('draft');
            };
        }
        if ($this->level == "editor") {
            $prevent = count(array_filter($editors, function ($e) {
                return $e->user_id == $this->role_id;
            }));
            if ($prevent == 0) {
                $this->session->set_flashdata('warning', 'Anda tidak memiliki akses ke draft ini');
                redirect('draft');
            };
        }
        if ($this->level == "layouter") {
            $prevent = count(array_filter($layouters, function ($e) {
                return $e->user_id == $this->role_id;
            }));
            if ($prevent == 0) {
                $this->session->set_flashdata('warning', 'Anda tidak memiliki akses ke draft ini');
                redirect('draft');
            };
        }
        // If something wrong
        if (!$this->draft->validate() || $this->form_validation->error_array()) {
            $pages = $this->pages;
            $main_view = 'draft/view/view';
            $form_action = "draft/edit/$id";
            $this->load->view('template', compact('tot_revisi', 'books', 'author_order', 'draft', 'reviewer_order', 'desk', 'pages', 'main_view', 'form_action', 'input', 'authors', 'reviewers', 'editors', 'layouters'));
            return;
        }
        if ($this->draft->where('draft_id', $id)->update($input)) {
            $this->session->set_flashdata('success', 'Data updated');
        } else {
            $this->session->set_flashdata('error', 'Data failed to update');
        }
        redirect('draft');
    }
    public function download($path, $file_name)
    {
        $this->load->helper('download');
        force_download('./' . $path . '/' . $file_name, null);
    }
    public function upload_progress($id, $column)
    {
        $draft = $this->draft->where('draft_id', $id)->get();
        $datatitle = ['draft_id' => $id];
        $title = $this->draft->getWhere($datatitle);
        if (!$draft) {
            $this->session->set_flashdata('warning', 'Draft data were not available');
            redirect('draft');
        }
        $isCanAccess = false;
        if ($this->level == 'author') {
            $draft_author_status = $this->getDraftAuthorStatus($this->role_id, $id);
            if ($draft_author_status < 1) {
                $data['status'] = false;
            } else {
                $isCanAccess = true;
            }
        } else {
            $isCanAccess = true;
        }
        if ($isCanAccess) {
            if (!$_POST) {
                $input = (object) $draft;
            } else {
                $input = (object) $this->input->post(null, true);
                $input->$column = $draft->$column; // Set draft file for preview.

            }
            //tiap upload, update upload date
            $tahap = explode('_', $column);
            $this->draft->editDraftDate($id, $tahap[0] . '_upload_date');
            $last_upload = $tahap[0] . '_last_upload';
            $input->$last_upload = $this->username;
            if (!empty($_FILES) && $_FILES[$column]['size'] > 0) {
                // Upload new draft (if any)
                $getextension = explode(".", $_FILES[$column]['name']);
                $draftFileName = str_replace(" ", "_", $title->draft_title . '_' . $column . '_' . date('YmdHis') . "." . $getextension[1]); // draft file name
                if ($column == 'cover_file') {
                    $upload = $this->draft->uploadProgressCover($column, $draftFileName);
                } else {
                    $upload = $this->draft->uploadProgress($column, $draftFileName);
                }
                if ($upload) {
                    $input->$column = "$draftFileName";
                    // Delete old draft file
                    if ($draft->$column) {
                        if ($column == 'cover_file') {
                            $this->draft->deleteProgressCover($draft->$column);
                        } else {
                            $this->draft->deleteProgress($draft->$column);
                        }
                    }
                }
            }
            //If something wrong
            // if (!$this->draft->validate() || $this->form_validation->error_array()) {
            //     $pages    = $this->pages;
            //     $main_view   = 'draft/view/view';
            //     $form_action = "draft/edit/$id";
            //     $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            //     return;
            // }
            if ($this->draft->where('draft_id', $id)->update($input)) {
                //$this->session->set_flashdata('success', 'Upload Success');
                $data['status'] = true;
            } else {
                //$this->session->set_flashdata('error', 'Upload Failed');
                $data['status'] = false;
            }
        }
        echo json_encode($data);
        //redirect('draft/view/'.$id);

    }
    //hapus progress draft
    public function delete_progress($id, $jenis)
    {
        $draft = $this->draft->where('draft_id', $id)->get();
        if ($jenis == 'edit') {
            if (file_exists("./draftfile/$draft->edit_file")) {
                unlink("./draftfile/$draft->edit_file");
                $flag = true;
            } else {
                $data['status'] = false;
            }
        } elseif ($jenis == 'layout') {
            if (file_exists("./draftfile/$draft->layout_file")) {
                unlink("./draftfile/$draft->layout_file");
                $flag = true;
            } else {
                $data['status'] = false;
            }
        }

        if ($flag) {
            $draft->{$jenis . '_upload_date'} = null;
            $draft->{$jenis . '_last_upload'} = '';
            $draft->{$jenis . '_file'} = '';
            if ($jenis == 'edit') {
                $draft->editor_file_link = '';
            } elseif ($jenis == 'layout') {
                $draft->layouter_file_link = '';
            }

            if ($this->draft->where('draft_id', $id)->update($draft)) {
                $data['status'] = true;
            } else {
                $data['status'] = false;
            }
        }

        echo json_encode($data);
    }
    //ubah notes - buat ubah deadline juga
    public function ubahnotes($id = null, $rev = null)
    {
        $ceklevel = $this->session->userdata('level');
        $data = array();
        $draft = $this->draft->where('draft_id', $id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', 'Draft data were not available');
            redirect('draft');
        }
        $isCanAccess = false;
        if ($this->level == 'author') {
            $draft_author_status = $this->getDraftAuthorStatus($this->role_id, $id);
            if ($draft_author_status < 1) {
                $data['status'] = false;
            } else {
                $isCanAccess = true;
            }
        } else {
            $isCanAccess = true;
        }
        if ($isCanAccess) {
            if (!$_POST) {
                $input = (object) $draft;
            } else {
                $input = (object) $this->input->post(null, false);
            }
            if (empty($input->files)) {
                unset($input->files);
            }
            // If something wrong
            // if (!$this->draft->validate() || $this->form_validation->error_array()) {
            //     return;
            // }
            //gabungkan array menjadi csv
            if ($rev == 1) {
                $input->nilai_reviewer1 = implode(",", $input->nilai_reviewer1);
                //kirim notifikasi untuk super admin, admin_penerbitan, dan author draft
                $q_admin = $this->db->query("select * from user where level='superadmin' or level='admin_penerbitan'");
                foreach($q_admin->result() as $idx => $r_admin){
                    $user_id_notif = $r_admin->user_id;
                    $judul_notif = 'penilaian oleh reviewer 1';//
                    $isi_notif = 'reviewer 1 memberikan penilaian draft '.$draft->draft_title;
                    $data_notif = array('user_id' => $user_id_notif, 
                                            'judul' => $judul_notif, 
                                            'isi' => $isi_notif,
                                            'draft_id' => $id
                                        );
                    $this->db->insert('notifikasi', $data_notif);
                    if($idx == 0){
                        //firebase
                        $topics = "sigap_superadmin";
                        $params_notif = array('draft_id' => $id);
                        $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                        $this->jagolibrary->sendNotifTopics($message_notif, $topics, $params_notif);

                        $topics = "sigap_admin_penerbitan";
                        $this->jagolibrary->sendNotifTopics($message_notif, $topics, $params_notif);
                    }
                }
                
                //kirim notifikasi untuk pemilik draft author
                $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                foreach($q_author->result() as $r_author){
                    $user_id_notif = $r_author->user_id;
                    $judul_notif = 'penilaian oleh reviewer 1';//
                    $isi_notif = 'reviewer 1 memberikan penilaian draft '.$draft->draft_title;
                    $data_notif = array('user_id' => $user_id_notif, 
                                            'judul' => $judul_notif, 
                                            'isi' => $isi_notif,
                                            'draft_id' => $id
                                        );
                    $this->db->insert('notifikasi', $data_notif);
                    //firebase
                    if($r_author->firebase_token != ''){
                        $firebase_token = $r_author->firebase_token;
                        $params_notif = array('draft_id' => $id);
                        $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                        $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                    }
                }


                //backup query : select user_id from reviewer where draft_id='".$id."' and draft_reviewer_id in (select max(draft_reviewer_id) where draft_id='".$id."'
            } elseif ($rev == 2) {
                $input->nilai_reviewer2 = implode(",", $input->nilai_reviewer2);
                //kirim notifikasi untuk super admin, admin_penerbitan, dan author draft
                $q_admin = $this->db->query("select * from user where level='superadmin' or level='admin_penerbitan'");
                foreach($q_admin->result() as $idx => $r_admin){
                    $user_id_notif = $r_admin->user_id;
                    $judul_notif = 'penilaian oleh reviewer 2';//
                    $isi_notif = 'reviewer 2 memberikan penilaian draft '.$draft->draft_title;
                    $data_notif = array('user_id' => $user_id_notif, 
                                            'judul' => $judul_notif, 
                                            'isi' => $isi_notif,
                                            'draft_id' => $id
                                        );
                    $this->db->insert('notifikasi', $data_notif);
                    if($idx == 0){
                        //firebase
                        $topics = "sigap_superadmin";
                        $params_notif = array('draft_id' => $id);
                        $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                        $this->jagolibrary->sendNotifTopics($message_notif, $topics, $params_notif);

                        $topics = "sigap_admin_penerbitan";
                        $this->jagolibrary->sendNotifTopics($message_notif, $topics, $params_notif);
                    }
                }
                //kirim notifikasi untuk pemilik draft author
                $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                foreach($q_author->result() as $r_author){
                    $user_id_notif = $r_author->user_id;
                    $judul_notif = 'penilaian oleh reviewer 2';//
                    $isi_notif = 'reviewer 2 memberikan penilaian draft '.$draft->draft_title;
                    $data_notif = array('user_id' => $user_id_notif, 
                                            'judul' => $judul_notif, 
                                            'isi' => $isi_notif,
                                            'draft_id' => $id
                                        );
                    $this->db->insert('notifikasi', $data_notif);
                    if($r_author->firebase_token != ''){
                        $firebase_token = $r_author->firebase_token;
                        $params_notif = array('draft_id' => $id);
                        $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                        $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                    }
                }
            } else {
                unset($input->nilai_reviewer1);
                unset($input->nilai_reviewer2);
                if($this->level == 'author'){
                    //kirim notifikasi untuk admin dan admin penerbit
                    $q_admin = $this->db->query("select * from user where level='superadmin' or level='admin_penerbitan'");
                    foreach($q_admin->result() as $idx => $r_admin){
                        $user_id_notif = $r_admin->user_id;
                        $judul_notif = 'author memberikan catatan';//
                        $isi_notif = 'author memberikan catatan pada draft '.$draft->draft_title;
                        $data_notif = array('user_id' => $user_id_notif, 
                                                'judul' => $judul_notif, 
                                                'isi' => $isi_notif,
                                                'draft_id' => $id
                                            );
                        $this->db->insert('notifikasi', $data_notif);
                        if($idx == 0){
                            //firebase
                            $topics = "sigap_superadmin";
                            $params_notif = array('draft_id' => $id);
                            $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                            $this->jagolibrary->sendNotifTopics($message_notif, $topics, $params_notif);

                            $topics = "sigap_admin_penerbitan";
                            $this->jagolibrary->sendNotifTopics($message_notif, $topics, $params_notif);
                        }
                    }
                    //kirim notifikasi untuk reviewer terkait
                    if(isset($input->review2_notes_author)){
                        $r_reviewer = $this->db->query("select user_id, firebase_token from reviewer r join draft_reviewer dr on r.reviewer_id=dr.reviewer_id join user u on r.user_id=u.user_id where draft_id='$id' and draft_reviewer_id in (select max(draft_reviewer_id) from draft_reviewer where draft_id='$id')")->row();
                        $user_id_notif = $r_reviewer->user_id;
                        $judul_notif = 'author memberikan catatan';//
                        $isi_notif = 'author memberikan catatan pada draft '.$draft->draft_title;
                        $data_notif = array('user_id' => $user_id_notif, 
                                                'judul' => $judul_notif, 
                                                'isi' => $isi_notif,
                                                'draft_id' => $id
                                            );
                        $this->db->insert('notifikasi', $data_notif);

                        //firebase
                        if($r_reviewer->firebase_token != ''){
                            $firebase_token = $r_reviewer->firebase_token;
                            $params_notif = array('draft_id' => $id);
                            $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                            $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                        }


                    }else if(isset($input->review1_notes_author)){
                        //select user_id from reviewer r join draft_reviewer dr on r.reviewer_id=dr.reviewer_id where draft_id='894' and draft_reviewer_id in (select max(draft_reviewer_id) from draft_reviewer where draft_id='894')
                        $r_reviewer = $this->db->query("select user_id, firebase_token from reviewer r join draft_reviewer dr on r.reviewer_id=dr.reviewer_id join user u on r.user_id=u.user_id where draft_id='$id' and draft_reviewer_id in (select min(draft_reviewer_id) from draft_reviewer where draft_id='$id')")->row();
                        $user_id_notif = $r_reviewer->user_id;
                        $judul_notif = 'author memberikan catatan';//
                        $isi_notif = 'author memberikan catatan pada draft '.$draft->draft_title;
                        $data_notif = array('user_id' => $user_id_notif, 
                                                'judul' => $judul_notif, 
                                                'isi' => $isi_notif,
                                                'draft_id' => $id
                                            );
                        $this->db->insert('notifikasi', $data_notif);
                        //firebase
                        if($r_reviewer->firebase_token != ''){
                            $firebase_token = $r_reviewer->firebase_token;
                            $params_notif = array('draft_id' => $id);
                            $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                            $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                        }

                    }
                }else if($this->level == 'superadmin' || $this->level == 'admin_penerbitan'){
                    if(isset($input->draft_status) && $input->draft_status=='5'){//review disetujuai
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'review telah disetujui';//
                            $isi_notif = 'review draft '.$draft->draft_title.' telah disetujui dan masuk ke tahap antri edit';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);      
                            //firebase
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='99' && $input->is_review=='n'){//review ditolak
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'review telah ditolak';//
                            $isi_notif = 'review draft '.$draft->draft_title.' telah ditolak';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);

                            //firebase
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='7'){//editorial disetujui
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'editorial telah disetujui';//
                            $isi_notif = 'editorial draft '.$draft->draft_title.' telah disetujui';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            //firebase
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='99' && $input->is_edit=='n'){//review ditolak
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'editorial telah ditolak';//
                            $isi_notif = 'editorial draft '.$draft->draft_title.' telah ditolak';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            //firebase
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='9'){//layout disetujui
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'layout telah disetujui';//
                            $isi_notif = 'layout draft '.$draft->draft_title.' telah disetujui';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='99' && $input->is_layout=='n'){//review ditolak
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'layout telah ditolak';//
                            $isi_notif = 'layout draft '.$draft->draft_title.' telah ditolak';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='11'){//cover disetujui
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'cover telah disetujui';//
                            $isi_notif = 'cover draft '.$draft->draft_title.' telah disetujui';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='99' && $input->is_cover=='n'){//review ditolak
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'cover telah ditolak';//
                            $isi_notif = 'cover draft '.$draft->draft_title.' telah ditolak';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='13'){//proofreading disetujui
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'proofreading telah disetujui';//
                            $isi_notif = 'editorial draft '.$draft->draft_title.' telah disetujui';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='99' && $input->is_proofread=='n'){//review ditolak
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'proofreading telah ditolak';//
                            $isi_notif = 'proofreading draft '.$draft->draft_title.' telah ditolak';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='16'){//cetak disetujui
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'cetak telah selesai';//
                            $isi_notif = 'cetak '.$draft->draft_title.' telah selesai';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else if(isset($input->draft_status) && $input->draft_status=='99' && $input->is_print=='n'){//Cetak ditolak
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'cetak telah ditolak';//
                            $isi_notif = 'cetak '.$draft->draft_title.' telah ditolak';
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }else{//note admin
                        //kirim notifikasi untuk pemilik draft author
                        $q_author = $this->db->query("select a.user_id, firebase_token from draft_author da join author a on da.author_id=a.author_id join user u on a.user_id=u.user_id where draft_id='$id'");
                        foreach($q_author->result() as $r_author){
                            $user_id_notif = $r_author->user_id;
                            $judul_notif = 'admin memberikan catatan';//
                            $isi_notif = 'admin memberikan catatan draft '.$draft->draft_title;
                            $data_notif = array('user_id' => $user_id_notif, 
                                                    'judul' => $judul_notif, 
                                                    'isi' => $isi_notif,
                                                    'draft_id' => $id
                                                );
                            $this->db->insert('notifikasi', $data_notif);
                            if($r_author->firebase_token != ''){
                                $firebase_token = $r_author->firebase_token;
                                $params_notif = array('draft_id' => $id);
                                $message_notif = array('title' => $judul_notif, 'body' => $isi_notif, 'sound' => 'default');
                                $this->jagolibrary->sendNotifToId($message_notif, $firebase_token, $params_notif);
                            }
                        }
                    }
                }
            }

            if (!empty($this->input->post('edit_notes_date'))) {
                if ($ceklevel == 'editor') {
                    $input->edit_notes_date = date('Y-m-d H:i:s');
                    $data['edit_notes_date'] = konversiTanggal($input->edit_notes_date);
                    //notif catatan editorial dibuat
                }
            }

            if (!empty($this->input->post('layout_notes_date'))) {
                if ($ceklevel == 'layouter') {
                    $input->layout_notes_date = date('Y-m-d H:i:s');
                    $data['layout_notes_date'] = konversiTanggal($input->layout_notes_date);
                }
            }

            if ($this->draft->where('draft_id', $id)->update($input)) {
                //$this->session->set_flashdata('success', 'Data updated');
                $data['status'] = true;
            } else {
                $data['status'] = false;
                //$this->session->set_flashdata('error', 'Data failed to update');

            }
        }
        echo json_encode($data);
    }
    public function edit($id = null)
    {
        //khusus admin
        $ceklevel = $this->session->userdata('level');
        if ($ceklevel != 'superadmin' and $ceklevel != 'admin_penerbitan') {
            redirect('draft');
        }
        $draft = $this->draft->where('draft_id', $id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', 'Draft data were not available');
            redirect('draft');
        }
        if (!$_POST) {
            $input = (object) $draft;
        } else {
            $input = (object) $this->input->post(null, false);
            //$input->draft_file = $draft->draft_file; // Set draft file for preview.

        }
        if ($this->draft->validate()) {
            if (!empty($_FILES) && $_FILES['draft_file']['size'] > 0) {
                $getextension = explode(".", $_FILES['draft_file']['name']);
                $draftFileName = str_replace(" ", "_", $input->draft_title . '_' . date('YmdHis') . "." . $getextension[1]); // draft file name
                $upload = $this->draft->uploadDraftfile('draft_file', $draftFileName);
                if ($upload) {
                    $input->draft_file = "$draftFileName";
                    // Delete old draft file
                    if ($draft->draft_file) {
                        $this->draft->deleteDraftfile($draft->draft_file);
                    }
                }
            }
        }
        if ($this->draft->validate()) {
            if (!empty($_FILES) && $_FILES['cover_file']['size'] > 0) {
                // Upload new draft (if any)
                $getextension = explode(".", $_FILES['cover_file']['name']);
                $coverFileName = str_replace(" ", "_", $input->draft_title . '_' . date('YmdHis') . "." . $getextension[1]); // cover file name
                $upload = $this->draft->uploadCoverfile('cover_file', $coverFileName);
                if ($upload) {
                    $input->cover_file = "$coverFileName";
                    // Delete old cover file
                    if ($draft->cover_file) {
                        $this->draft->deleteCoverfile($draft->cover_file);
                    }
                }
            }
        }
        // If something wrong
        if (!$this->draft->validate() || $this->form_validation->error_array()) {
            $pages = $this->pages;
            $main_view = 'draft/form_draft_edit';
            $form_action = "draft/edit/$id";
            $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
            return;
        }
        if ($this->draft->where('draft_id', $id)->update($input)) {
            $this->session->set_flashdata('success', 'Data updated');
        } else {
            $this->session->set_flashdata('error', 'Data failed to update');
        }
        redirect('draft');
    }
    public function delete($id = null)
    {
        //khusus admin
        $ceklevel = $this->session->userdata('level');
        if ($ceklevel != 'superadmin' and $ceklevel != 'admin_penerbitan') {
            redirect('draft');
        }
        $draft = $this->draft->where('draft_id', $id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', 'Draft data were not available');
            redirect('draft');
        }
        $isSuccess = true;
        $this->draft->where('draft_id', $id)->delete('draft_author');
        $affected_rows = $this->db->affected_rows();
        if ($affected_rows > 0) {
            if ($this->draft->where('draft_id', $id)->delete()) {
                // Delete cover.
                $this->draft->deleteDraftfile($draft->draft_file);
                $this->draft->deleteDraftfile($draft->review1_file);
                $this->draft->deleteDraftfile($draft->review2_file);
                $this->draft->deleteDraftfile($draft->edit_file);
                $this->draft->deleteDraftfile($draft->layout_file);
                $this->draft->deleteCoverfile($draft->cover_file);
                $this->draft->deleteDraftfile($draft->proofread_file);
            } else {
                $isSuccess = false;
            }
        } else {
            $isSuccess = false;
        }
        if ($isSuccess) {
            $this->session->set_flashdata('success', 'Data deleted');
        } else {
            $this->session->set_flashdata('error', 'Data failed to delete');
        }
        redirect('draft');
    }
    public function copyToBook($draft_id)
    {
        $this->load->model('book_model', 'book', true);
        $book_id = $this->book->getIdDraftFromDraftId($draft_id, 'book');
        $datax = array('draft_id' => $draft_id);
        $draft = $this->draft->getWhere($datax);
        if ($book_id == 0) {
            $data = array('draft_id' => $draft_id, 'book_title' => $draft->draft_title, 'book_file' => $draft->print_file, 'book_file_link' => $draft->print_file_link, 'published_date' => date('Y-m-d H:i:s'));
            if ($this->book->insert($data)) {
                $book_id = $this->db->insert_id();
                if ($book_id != 0) {
                    $this->session->set_flashdata('warning', 'Lengkapi data lalu Submit');
                    redirect('book/edit/' . $book_id);
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Book has been created');
            redirect('book');
        }
    }

    public function cetakUlang($id = '')
    {

        $draft = $this->draft->where('draft_id', $id)->get();
        if (!$draft) {
            $this->session->set_flashdata('warning', 'Draft data were not available');
            redirect('draft');
        }
        $draft->draft_title = $draft->draft_title . " (cetak ulang)";
        // if (!$_POST) {
        // } else {
        //     $input = (object)$this->input->post(null, false);
        // }
        $input = (object) $draft;
        $input->is_reprint = 'y';
        unset($input->draft_id);

        //get array penulis
        $input->authors = $this->draft->select('draft_author.author_id')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('work_unit', 'author', 'work_unit')->join3('institute', 'author', 'institute')->where('draft_author.draft_id', $id)->getAll();
        $input->author_id = array();
        foreach ($input->authors as $au) {
            array_push($input->author_id, $au->author_id);
        }

        // if (!$this->draft->validate() || $this->form_validation->error_array()) {
        //     $pages = $this->pages;
        //     $main_view = 'draft/form_draft_add';
        //     $form_action = 'draft/add';
        //     $this->load->view('template', compact('pages', 'main_view', 'form_action', 'input'));
        //     return;
        // }

        $draft_id = $this->draft->insert($input);
        $isSuccess = true;
        if ($draft_id > 0) {
            foreach ($input->author_id as $key => $value) {
                $data_author = array('author_id' => $value, 'draft_id' => $draft_id);
                if ($key == 0) {
                    $data_author['draft_author_status'] = 1;
                }
                $draft_author_id = $this->draft->insert($data_author, 'draft_author');
                if ($draft_author_id < 1) {
                    $isSuccess = false;
                    break;
                }
            }
        } else {
            $isSuccess = false;
        }
        if ($isSuccess) {
            $worksheet_num = $this->generateWorksheetNumber();
            $data_worksheet = array('draft_id' => $draft_id, 'worksheet_num' => $worksheet_num, 'worksheet_status' => 1, 'is_reprint' => 'y');
            $worksheet_id = $this->draft->insert($data_worksheet, 'worksheet');
            if ($worksheet_id < 1) {
                $isSuccess = false;
            }
        }
        if ($isSuccess) {
            $this->session->set_flashdata('success', 'Data saved');
        } else {
            $this->session->set_flashdata('error', 'Data failed to save');
        }
        redirect('draft/view/' . $draft_id);
    }

    public function getRevision()
    {
        $input = (object) $this->input->post(null);
        $ceklevel = $this->session->userdata('level');
        $revisi = $this->draft->where('revision_role', $input->role)->where('draft_id', $input->draft_id)->getAll('revision');
        $urutan = 1;
        //flag menandai revisi yang belum selesai
        //flag 1 dan lebih artinya ada yg blm selese
        $flag = 0;
        if ($revisi) {
            foreach ($revisi as $value) {
                if ($value->revision_end_date != '0000-00-00 00:00:00' and $value->revision_end_date != null) {
                    $atribut_tombol_selesai = 'disabled';
                    $atribut_tombol_simpan = 'd-none';
                    if (!empty($value->revision_notes)) {
                        $form_revisi = '<div class="font-italic">' . nl2br($value->revision_notes) . '</div>';
                    } else {
                        $form_revisi = '<div class="font-italic mb-3">Tidak ada Catatan</div>';
                    }
                    $badge_revisi = '<span class="badge badge-success">Selesai</span>';
                } else {
                    $flag++;
                    $atribut_tombol_selesai = '';
                    $atribut_tombol_simpan = 'd-inline';
                    if ($ceklevel != 'editor') {
                        if (!empty($value->revision_notes)) {
                            $form_revisi = '<div class="font-italic">' . nl2br($value->revision_notes) . '</div>';
                        } else {
                            $form_revisi = '<div class="font-italic mb-3">Tidak ada Catatan</div>';
                        }
                    } else {
                        $form_revisi = '<textarea rows="6" name="revisi' . $value->revision_id . '" class="form-control summernote-basic" id="revisi' . $value->revision_id . '">' . $value->revision_notes . '</textarea>';
                    }
                    $badge_revisi = '<span class="badge badge-info">Dalam Proses</span>';
                }
                if ($input->role == 'editor') {
                    $tahap = 'edit';
                    $role = 'editor';
                }else if ($input->role == 'cover') {
                    $tahap = 'cover';
                    $role = 'cover';
                } else {
                    $tahap = 'layout';
                    $role = 'layouter';
                }

                if ($ceklevel == 'superadmin' or $ceklevel == 'admin_penerbitan') {
                    $tombol_hapus = '<button title="Hapus revisi" type="button" class="d-inline btn btn-danger hapus-revisi" data="' . $value->revision_id . '"><i class="fa fa-trash"></i><span class="d-none d-lg-inline"> Hapus</span></button>';
                    $tombol_edit = '<button type="button" class="d-inline btn btn-secondary btn-xs trigger-' . $tahap . '-revisi-deadline" data-toggle="modal" data-target="#' . $tahap . '-revisi-deadline" title="' . $tahap . ' Deadline" data="' . $value->revision_id . '">Edit</button>';
                } else {
                    $tombol_hapus = '';
                    $tombol_edit = '';
                }

                $data['revisi'][] = '<section class="card card-expansion-item">
                    <header class="card-header border-0" id="heading' . $value->revision_id . '">
                      <button class="btn btn-reset collapsed" data-toggle="collapse" data-target="#collapse' . $value->revision_id . '" aria-expanded="false" aria-controls="collapse' . $value->revision_id . '">
                        <span class="collapse-indicator mr-2">
                          <i class="fa fa-fw fa-caret-right"></i>
                        </span>
                        <span>Revisi #' . $urutan . '</span>
                        ' . $badge_revisi . '
                      </button>
                    </header>
                    <div id="collapse' . $value->revision_id . '" class="collapse" aria-labelledby="heading' . $value->revision_id . '" data-parent="#accordion-' . $role . '">
                    <div class="list-group list-group-flush list-group-bordered">
                        <div class="list-group-item justify-content-between">
                          <span class="text-muted">Tanggal mulai</span>
                          <strong>' . konversiTanggal($value->revision_start_date) . '</strong>
                        </div>
                        <div class="list-group-item justify-content-between">
                          <span class="text-muted">Tanggal selesai</span>
                          <strong>' . konversiTanggal($value->revision_end_date) . '</strong>
                        </div>
                        <div class="list-group-item justify-content-between">
                          <span class="text-muted">Deadline ' . $tombol_edit . '</span>
                          <strong>' . konversiTanggal($value->revision_deadline) . '</strong>
                        </div>
                        <div class="list-group-item mb-0 pb-0">
                          <span class="text-muted">Catatan ' . $role . '</span>
                        </div>
                    </div>
                    <div class="card-body">
                    <form>
                    ' . $form_revisi . '
                    </form>
                        <div class="el-example">
                            <button title="Submit catatan" type="button" class="' . $atribut_tombol_simpan . ' btn btn-primary submit-revisi" data="' . $value->revision_id . '"><i class="fas fa-save"></i><span class="d-none d-lg-inline"> Simpan</span></button>
                            <button title="Selesai revisi" type="button" class="d-inline btn btn-secondary selesai-revisi" ' . $atribut_tombol_selesai . ' data="' . $value->revision_id . '"><i class="fas fa-stop"></i><span class="d-none d-lg-inline"> Selesai</span></button>
                            ' . $tombol_hapus . '
                        </div>
                    </div>
                    </div>
                  </section>';
                $urutan++;
            }
        } else {
            $data['revisi'] = 'Tidak ada revisi';
        }

        if ($flag > 0) {
            $data['flag'] = true;
        } else {
            $data['flag'] = false;
        }
        echo json_encode($data);
    }

    public function insertRevision()
    {
        $input = (object) $this->input->post(null);
        if ($input->role == 'editor') {
            $status = array('draft_status' => 17);
        } elseif ($input->role == 'layouter') {
            $status = array('draft_status' => 18);
        } elseif ($input->role == 'cover') {
            $status = array('draft_status' => 20);
        }
        $this->draft->updateDraftStatus($input->draft_id, $status);
        $datenow = date('Y-m-d H:i:s');
        $deadline = date('Y-m-d H:i:s', (strtotime($datenow) + (7 * 24 * 60 * 60)));
        $data = array('draft_id' => $input->draft_id, 'revision_start_date' => $datenow, 'revision_role' => $input->role, 'revision_deadline' => $deadline, 'user_id' => $this->session->userdata('user_id'));
        $insert = $this->draft->insert($data, 'revision');
        if ($insert) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        echo json_encode($data);
    }

    public function endRevision()
    {
        $input = (object) $this->input->post(null);
        $status = array('draft_status' => 19);
        $this->draft->updateDraftStatus($input->draft_id, $status);
        $datenow = date('Y-m-d H:i:s');
        $data = array('revision_end_date' => $datenow);
        $insert = $this->draft->where('revision_id', $input->revision_id)->update($data, 'revision');
        if ($insert) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        echo json_encode($data);
    }

    public function submitRevision()
    {
        $input = (object) $this->input->post(null);
        $revision_notes = $this->input->post('revision_notes');
        $data = array('revision_notes' => $revision_notes);
        $insert = $this->draft->where('revision_id', $input->revision_id)->update($data, 'revision');
        if ($insert) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        echo json_encode($data);
    }

    public function deleteRevision($revision_id = '')
    {
        $delete = $this->draft->where('revision_id', $revision_id)->delete('revision');
        if ($delete) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        echo json_encode($data);
    }

    public function deadlineRevision()
    {
        $input = (object) $this->input->post(null);
        $data = ['revision_deadline' => $input->revision_deadline, 'revision_start_date' => $input->revision_start_date, 'revision_end_date' => $input->revision_end_date];
        $insert = $this->draft->where('revision_id', $input->revision_id)->update($data, 'revision');
        if ($insert) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        echo json_encode($data);
    }

    public function search($page = null)
    {
        $cekusername = $this->session->userdata('username');
        $keywords = $this->input->get('keywords', true);
        $this->db->group_by('draft.draft_id');
        if ($this->level == 'superadmin' || $this->level == 'admin_penerbitan') {
            $drafts = $this->draft->like('category_name', $keywords)->orLike('draft_title', $keywords)->orLike('author_name', $keywords)->join('category')->join('theme')->joinRelationMiddle('draft', 'draft_author')->joinRelationDest('author', 'draft_author')->orderBy('category.category_id')->orderBy('theme.theme_id')->orderBy('draft_title')->paginate($page)->getAll();
            $tot = $this->draft->like('category_name', $keywords)->orLike('draft_title', $keywords)->orLike('author_name', $keywords)->join('category')->join('theme')->joinRelationMiddle('draft', 'draft_author')->joinRelationDest('author', 'draft_author')->orderBy('category.category_id')->orderBy('theme.theme_id')->orderBy('draft_title')->getAll();
            $total = count($tot);
        } elseif ($this->level == 'author') {
            $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('user.username', $cekusername)->like('draft_title', $keywords)->orderBy('author.author_name')->orderBy('draft_title')->orderBy('category.category_id')->paginate($page)->getAll();
            $tot = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('user.username', $cekusername)->like('draft_title', $keywords)->orderBy('author.author_name')->orderBy('draft_title')->orderBy('category.category_id')->getAll();
            $total = count($tot);
        } elseif ($this->level == 'reviewer') {
            $drafts = $this->draft->join('category')->join('theme')->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('user', 'reviewer', 'user')->where('user.username', $cekusername)->like('draft_title', $keywords)->orderBy('draft_title')->orderBy('category.category_id')->paginate($page)->getAll();
            $tot = $this->draft->join('category')->join('theme')->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('user', 'reviewer', 'user')->where('user.username', $cekusername)->like('draft_title', $keywords)->orderBy('draft_title')->orderBy('category.category_id')->getAll();
            $total = count($tot);
            //cari tau rev 1 atau rev 2 yg sedang login
            foreach ($drafts as $key => $value) {
                $rev = $this->draft->getIdAndName('reviewer', 'draft_reviewer', $value->draft_id);
                $value->rev = key(array_filter($rev, function ($e) {
                    return $e->reviewer_id == $this->session->userdata('role_id');
                }));
            }
        } else {
            $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->where('user.username', $cekusername)->like('draft_title', $keywords)->orderBy('author.author_name')->orderBy('draft_title')->orderBy('category.category_id')->paginate($page)->getAll();
            $tot = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->where('user.username', $cekusername)->like('draft_title', $keywords)->orderBy('author.author_name')->orderBy('draft_title')->orderBy('category.category_id')->getAll();
            $total = count($tot);
        }
        $pagination = $this->draft->makePagination(site_url('draft/search/'), 3, $total);
        if (!$drafts) {
            $this->session->set_flashdata('warning', 'Data were not found');
        } else {
            foreach ($drafts as $key => $value) {
                $authors = $this->draft->getIdAndName('author', 'draft_author', $value->draft_id);
                $value->author = $authors;
                $value->draft_status = $this->checkStatus($value->draft_status);
            }
        }
        //cari tau rev 1 atau rev 2 yg sedang login
        foreach ($drafts as $key => $value) {
            $rev = $this->draft->getIdAndName('reviewer', 'draft_reviewer', $value->draft_id);
            $value->rev = key(array_filter($rev, function ($e) {
                return $e->reviewer_id == $this->session->userdata('role_id');
            }));
            if ($value->rev == 0) {
                $value->review_flag = $value->review1_flag;
                $value->deadline = $value->review1_deadline;
            } elseif ($value->rev == 1) {
                $value->review_flag = $value->review2_flag;
                $value->deadline = $value->review2_deadline;
            } else {
            }
        }
        $pages = $this->pages;
        $main_view = 'draft/index_draft';
        $this->load->view('template', compact('pages', 'main_view', 'drafts', 'pagination', 'total'));
    }
    public function endProgress($id, $status)
    {
        $this->draft->updateDraftStatus($id, array('draft_status' => $status + 1));
        switch ($status) {
            case '4':
                $column = 'review_end_date';
                break;
            default:
                # code...

                break;
        }
        $this->draft->editDraftDate($id, $column);
        $this->detail($id);
    }
    public function generateWorksheetNumber()
    {
        $date = date('Y-m');
        $this->db->limit(1);
        $query = $this->draft->like('worksheet_num', $date, 'after')->orderBy('draft_id', 'desc')->get('worksheet');
        if ($query) {
            $worksheet_num = $query->worksheet_num;
            $worksheet_num = explode("-", $worksheet_num);
            $num = (int) $worksheet_num[2];
            $num++;
            $num = str_pad($num, 2, '0', STR_PAD_LEFT);
        } else {
            $num = '01';
        }
        return $date . '-' . $num;
    }
    private function getDraftAuthorStatus($author_id, $draft_id)
    {
        $data = array('author_id' => $author_id, 'draft_id' => $draft_id);
        $result = $this->draft->getWhere($data, 'draft_author');
        if ($result) {
            $draft_author_status = $result->draft_author_status;
        } else {
            $draft_author_status = -1;
        }
        return $draft_author_status;
    }
    public function checkStatus($code)
    {
        $status = "";
        switch ($code) {
            case 0:
                $status = 'Desk Screening';
                break;
            case 1:
                $status = 'Lolos Desk Screening';
                break;
            case 2:
                $status = 'Tidak Lolos Desk Screening';
                break;
            case 3:
                $status = 'Review Ditolak';
                break;
            case 4:
                $status = 'Reviewing';
                break;
            case 5:
                $status = 'Antri Edit';
                break;
            case 6:
                $status = 'Editing';
                break;
            case 7:
                $status = 'Editorial Selesai';
                break;
            case 8:
                $status = 'Layouting';
                break;
            case 9:
                $status = 'Layout selesai';
                break;
            case 10:
                $status = 'Desain Cover';
                break;
            case 11:
                $status = 'Cover Selesai';
                break;
            case 12:
                $status = 'Proofreading';
                break;
            case 13:
                $status = 'Proofread Selesai';
                break;
            case 14:
                $status = 'Final';
                break;
            case 15:
                $status = 'Cetak';
                break;
            case 16:
                $status = 'Cetak Selesai';
                break;
            case 17:
                $status = 'Revisi Edit';
                break;
            case 18:
                $status = 'Revisi Layout';
                break;
            case 19:
                $status = 'Selesai Revisi';
                break;
            case 20:
                $status = 'Revisi Cover';
                break;
            case 99:
                $status = 'Draft Ditolak';
                break;
            default:
                # code...

                break;
        }
        return $status;
    }
    public function checkFilter($category = '')
    {
        if (empty($category)) {
            $data['category'] = null;
            $data['cond_temp'] = 'draft.category_id !=';
        } else {
            $data['category'] = $category;
            $data['cond_temp'] = 'draft.category_id';
        }
        return $data;
    }
    public function checkReprint($reprint = '')
    {
        if ($reprint == 'n') {
            $data['stts'] = 'n';
            $data['cond_temp'] = 'is_reprint';
        } elseif (($reprint == 'y')) {
            $data['stts'] = 'y';
            $data['cond_temp'] = 'is_reprint';
        } else {
            $data['stts'] = 'null';
            $data['cond_temp'] = 'is_reprint !=';
        }
        return $data;
    }
    /*
    |-----------------------------------------------------------------
    | Callback
    |-----------------------------------------------------------------
     */
    //    public function alpha_coma_dash_dot_space($str)
    //    {
    //        if ( !preg_match('/^[a-zA-Z .,\-]+$/i',$str) )
    //        {
    //            $this->form_validation->set_message('alpha_coma_dash_dot_space', 'Can only be filled with letters, numbers, dash(-), dot(.), and comma(,).');
    //            return false;
    //        }
    //    }
    //
    public function unique_draft_title()
    {
        $draft_title = $this->input->post('draft_title');
        $draft_id = $this->input->post('draft_id');
        $this->draft->where('draft_title', $draft_title);
        !$draft_id || $this->draft->where('draft_id !=', $draft_id);
        $draft = $this->draft->get();
        if(is_array($draft)){
            if (count($draft)) {
                $this->form_validation->set_message('unique_draft_title', '%s has been used');
                return false;
            }
        }
        return true;
    }

    function download_draft_report(){
        $ceklevel = $this->session->userdata('level');
        $cekusername = $this->session->userdata('username');
        //menampilkan sessuai level user
        if ($ceklevel == 'author') {
            $drafts = $this->draft->join('category')->join('theme')->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('user.username', $cekusername)->getAll();
            $tot = $this->draft->join3('draft_author', 'draft', 'draft')->join3('author', 'draft_author', 'author')->join3('user', 'author', 'user')->where('user.username', $cekusername)->getAll();
        } elseif ($ceklevel == 'editor' || $ceklevel == 'layouter') {
            $drafts = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('user.username', $cekusername)->getAll();
            $tot = $this->draft->join('category')->join('theme')->join3('responsibility', 'draft', 'draft')->join3('user', 'responsibility', 'user')->where('user.username', $cekusername)->getAll();
        } elseif ($ceklevel == 'reviewer') {
            $drafts = $this->draft->join('category')->join('theme')->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('user', 'reviewer', 'user')->where('user.username', $cekusername)->getAll();
            $tot = $this->draft->join('category')->join('theme')->join3('draft_reviewer', 'draft', 'draft')->join3('reviewer', 'draft_reviewer', 'reviewer')->join3('user', 'reviewer', 'user')->where('user.username', $cekusername)->getAll();
        } else {
            $drafts = $this->draft->join('category')->join('theme')->orderBy('draft_status')->orderBy('entry_date', 'desc')->getAll();
            $tot = $this->draft->join('category')->join('theme')->getAll();
        }
        //echo $this->db->last_query();
        //tampilkan author dan status draft
        foreach ($drafts as $key => $value) {
            $authors = $this->draft->getIdAndName('author', 'draft_author', $value->draft_id);
            $value->author = $authors;
            $value->stts = $value->draft_status;
            $value->draft_status = $this->checkStatus($value->draft_status);
        }
        //cari tau rev 1 atau rev 2 yg sedang login
        foreach ($drafts as $key => $value) {
            $rev = $this->draft->getIdAndName('reviewer', 'draft_reviewer', $value->draft_id);
            $value->rev = key(array_filter($rev, function ($e) {
                return $e->reviewer_id == $this->session->userdata('role_id');
            }));
            if ($value->rev == 0) {
                $value->review_flag = $value->review1_flag;
                $value->deadline = $value->review1_deadline;
            } elseif ($value->rev == 1) {
                $value->review_flag = $value->review2_flag;
                $value->deadline = $value->review2_deadline;
            } else {
            }
        }
        try{
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();

            // Set document properties
            $spreadsheet->getProperties()->setCreator('SIGAP')
            ->setLastModifiedBy('SIGAP')
            ->setTitle('Draft')
            ->setSubject('Draft')
            ->setDescription('Draft Report')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Report generated');

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'DRAFT REPORT');
            if ($ceklevel == 'reviewer' or $ceklevel == 'editor' or $ceklevel == 'layouter'){
                $spreadsheet->getActiveSheet()->mergeCells('A1:H1');
                $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
                $spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
            }else{
                $spreadsheet->getActiveSheet()->mergeCells('A1:G1');
                $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
                $spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
            }


            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A3', 'NO')
            ->setCellValue('B3', 'JUDUL')
            ->setCellValue('C3', 'KATEGORI')
            ->setCellValue('D3', 'TAHUN')
            ->setCellValue('E3', 'PENULIS')
            ->setCellValue('F3', 'TANGGAL MASUK')
            ->setCellValue('G3', 'STATUS');
            if ($ceklevel == 'reviewer' or $ceklevel == 'editor' or $ceklevel == 'layouter'){
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('H3', 'SISA WAKTU');
                
                $spreadsheet->getActiveSheet()->getStyle('A3:H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('B3B3B3');
                $spreadsheet->getActiveSheet()->getStyle('A3:H3')->getAlignment()->setHorizontal('center');
            }else{
                $spreadsheet->getActiveSheet()->getStyle('A3:G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('B3B3B3');
                $spreadsheet->getActiveSheet()->getStyle('A3:G3')->getAlignment()->setHorizontal('center');
            }
            
            //->setCellValue('L1', 'OBAT LAMA');
            // Miscellaneous glyphs, UTF-8
            $i=4; 
            $no = 1;
            foreach($drafts as $draft) {     
                $authors = '';           
                foreach ($draft->author as $key => $value) {
                  $authors .= $value->author_name;
                  $authors .= ', ';
                }
                $authors = substr($authors, 0, -2);
                $author_name = '';
                if ($ceklevel != 'reviewer'){
                    $author_name = isset($draft->author[0]->author_name) ? $draft->author[0]->author_name : '-';
                }
                $status_draft = '';
                if ($ceklevel == 'reviewer') {
                    if ($draft->review_flag != '') {
                        $status_draft = 'Sudah direview';
                    } else {
                        $status_draft = 'Belum direview';
                    }
                }else{
                    $status_draft = $draft->draft_status;
                }
                $sisa_waktu_draft = '';
                if ($ceklevel == 'reviewer'){
                    $sisa_waktu = ceil((strtotime($draft->deadline) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                    if ($sisa_waktu <= 0 and $draft->review_flag == '') {
                        $sisa_waktu_draft = 'Melebihi Deadline!';
                    } elseif ($sisa_waktu <= 0 and $draft->review_flag != '') {
                        $sisa_waktu_draft = '-';
                    } else {
                        $sisa_waktu_draft = $sisa_waktu . ' hari';
                    }
                }else if ($ceklevel == 'editor'){
                    if(konversiTanggal($draft->edit_start_date) == '-'){
                      $sisa_waktu_draft = 'Belum Mulai';
                    }elseif(konversiTanggal($draft->edit_end_date) != '-'){
                      $sisa_waktu_draft = 'Selesai';
                    }else{
                      $sisa_waktu = ceil((strtotime($draft->edit_deadline) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                      if ($sisa_waktu <= 0 and $draft->edit_notes == '') {
                        $sisa_waktu_draft = 'Melebihi Deadline!';
                      } elseif ($sisa_waktu <= 0 and $draft->edit_notes != '') {
                        $sisa_waktu_draft = '-';
                      } else {
                        $sisa_waktu_draft = $sisa_waktu . ' hari';
                      }
                    }
                }else if ($ceklevel == 'layouter'){
                    if(konversiTanggal($draft->layout_start_date) == '-'){
                      $sisa_waktu_draft = 'Belum Mulai';
                    }elseif(konversiTanggal($draft->layout_end_date) != '-'){
                      $sisa_waktu_draft = 'Selesai';
                    }else{
                      $sisa_waktu = ceil((strtotime($draft->layout_deadline) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                      if ($sisa_waktu <= 0 and $draft->layout_notes == '') {
                        $sisa_waktu_draft = 'Melebihi Deadline!';
                      } elseif ($sisa_waktu <= 0 and $draft->layout_notes != '') {
                        $sisa_waktu_draft = '-';
                      } else {
                        $sisa_waktu_draft = $sisa_waktu . ' hari';
                      }
                    }

                }else{
                    $sisa_waktu = 1;
                    $draft->review_flag = true;
                }
                        
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $no)
                ->setCellValue('B'.$i, $draft->draft_title)
                ->setCellValue('C'.$i, $draft->category_name)
                ->setCellValue('D'.$i, $draft->category_year)
                ->setCellValue('E'.$i, $author_name)
                ->setCellValue('F'.$i, konversiTanggal($draft->entry_date))
                ->setCellValue('G'.$i, $status_draft);
                if ($ceklevel == 'reviewer' or $ceklevel == 'editor' or $ceklevel == 'layouter'){
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('H'.$i, $sisa_waktu_draft);
                }

                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
                $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
                $i++;
                $no++;
            }
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ];
            if ($ceklevel == 'reviewer' or $ceklevel == 'editor' or $ceklevel == 'layouter'){
                $spreadsheet->getActiveSheet()->getStyle('A3:H'.($i-1))->applyFromArray($styleArray);
            }else{
                $spreadsheet->getActiveSheet()->getStyle('A3:G'.($i-1))->applyFromArray($styleArray);
            }

            // Rename worksheet
            $spreadsheet->getActiveSheet()->setTitle('summary');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="draft_report.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            ob_end_clean();
            $writer->save('php://output');
            
            exit;
        }catch(\Exception $e) {
            print_r($e);
        }
    }
}
