<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Performance extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->pages = 'performance';

		// $performance_editor = $this->performance->select(['draft.draft_id','draft_title','username','category_name','edit_start_date','edit_deadline','edit_end_date','is_edit','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'editor')->where('responsibility.user_id !=', null)->orderBy('performance_status')->getAll('draft');



	}

	/* Fungsi untuk menampilkan halaman performa editor */
	public function index()
	{
		$xperformance_editor = $this->performance->join3('draft','responsibility','draft')->join3('user','responsibility','user')->orderBy('responsibility.draft_id')->getAll('responsibility');

		foreach ($xperformance_editor as $key => $row) {
			if(($row->edit_start_date == '0000-00-00 00:00:00' OR $row->edit_start_date == 'NULL') AND ($row->edit_end_date == '0000-00-00 00:00:00' OR $row->edit_end_date == 'NULL')){
				$data	= array('performance_status' => null);
				$this->performance->where('draft_id', $row->draft_id)->where('user_id', $row->user_id)->update($data, 'responsibility');
			}
			elseif ($row->is_edit == 'n' AND ($row->edit_start_date != '0000-00-00 00:00:00' AND $row->edit_start_date != 'NULL') AND ($row->edit_end_date == '0000-00-00 00:00:00' OR $row->edit_end_date == 'NULL')){
				$data	= array('performance_status' => 1);
				$this->performance->where('draft_id', $row->draft_id)->where('user_id', $row->user_id)->update($data, 'responsibility');
			}
			elseif ($row->is_edit == 'n' AND ($row->edit_end_date < $row->edit_deadline) AND $row->edit_start_date != '0000-00-00 00:00:00' AND $row->edit_start_date != 'NULL' AND $row->edit_end_date != '0000-00-00 00:00:00' AND $row->edit_end_date != 'NULL') {
				$data	= array('performance_status' => 2);
				$this->performance->where('draft_id', $row->draft_id)->where('user_id', $row->user_id)->update($data, 'responsibility');
			}
			elseif ($row->is_edit == 'y' AND ($row->edit_end_date < $row->edit_deadline) AND $row->edit_start_date != '0000-00-00 00:00:00' AND $row->edit_start_date != 'NULL' AND $row->edit_end_date != '0000-00-00 00:00:00' AND $row->edit_end_date != 'NULL') {
				$data	= array('performance_status' => 3);
				$this->performance->where('draft_id', $row->draft_id)->where('user_id', $row->user_id)->update($data, 'responsibility');
			}
			elseif ($row->edit_end_date > $row->edit_deadline AND ($row->edit_start_date != '0000-00-00 00:00:00' AND $row->edit_start_date != 'NULL') AND ($row->edit_end_date != '0000-00-00 00:00:00' AND $row->edit_end_date != 'NULL')) {
				$data	= array('performance_status' => 4);
				$this->performance->where('draft_id', $row->draft_id)->where('user_id', $row->user_id)->update($data, 'responsibility');
			}
			else {
				$data	= array('performance_status' => 5);
				$this->performance->where('draft_id', $row->draft_id)->where('user_id', $row->user_id)->update($data, 'responsibility');
			}
		}
		// $this->db->group_by('draft_id');
		$performance_editor = $this->performance->select(['draft.draft_id','draft_title','username','category_name','edit_start_date','edit_deadline','edit_end_date','is_edit','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'editor')->where('performance_status', 1)->where('responsibility.user_id !=', null)->orderBy('username')->getAll('draft');

		$total = count($performance_editor);

		$pages    = $this->pages;
		$main_view = 'performance/performance_editor';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'performance_editor','total'));
	}

	public function index_final()
	{
		// $this->db->group_by('draft_id');
		$performance_editor = $this->performance->select(['draft.draft_id','draft_title','username','category_name','edit_start_date','edit_deadline','edit_end_date','is_edit','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'editor')->where('performance_status', 2)->where('responsibility.user_id !=', null)->orderBy('username')->getAll('draft');

		$total = count($performance_editor);

		$pages    = $this->pages;
		$main_view = 'performance/performance_editor';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'performance_editor','total'));
	}

	public function index_ontime()
	{
		// $this->db->group_by('draft_id');
		$performance_editor = $this->performance->select(['draft.draft_id','draft_title','username','category_name','edit_start_date','edit_deadline','edit_end_date','is_edit','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'editor')->where('performance_status',3)->where('responsibility.user_id !=', null)->orderBy('username')->getAll('draft');

		$total = count($performance_editor);

		$pages    = $this->pages;
		$main_view = 'performance/performance_editor';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'performance_editor','total'));
	}

	public function index_late()
	{
		// $this->db->group_by('draft_id');
		$performance_editor = $this->performance->select(['draft.draft_id','draft_title','username','category_name','edit_start_date','edit_deadline','edit_end_date','is_edit','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'editor')->where('performance_status',4)->where('responsibility.user_id !=', null)->orderBy('username')->getAll('draft');

		$total = count($performance_editor);

		$pages    = $this->pages;
		$main_view = 'performance/performance_editor';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'performance_editor','total'));
	}

	public function index_error()
	{
		$performance_editor = $this->performance->select(['draft.draft_id','draft_title','username','category_name','edit_start_date','edit_deadline','edit_end_date','is_edit','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'editor')->where('performance_status',5)->where('responsibility.user_id !=', null)->orderBy('username')->getAll('draft');

		$total = count($performance_editor);

		$pages    = $this->pages;
		$main_view = 'performance/performance_editor';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'performance_editor','total'));
	}

	public function index_null()
	{
		$performance_editor = $this->performance->select(['draft.draft_id','draft_title','username','category_name','edit_start_date','edit_deadline','edit_end_date','is_edit','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'editor')->where('performance_status',null)->where('responsibility.user_id !=', null)->orderBy('username')->getAll('draft');

		$total = count($performance_editor);

		$pages    = $this->pages;
		$main_view = 'performance/performance_editor';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'performance_editor','total'));
	}

	/*
	*performa layouter
	*/
	public function performa_layouter()
	{
		$xperformance_layouter = $this->performance->join3('draft','responsibility','draft')->join3('user','responsibility','user')->orderBy('responsibility.draft_id')->getAll('responsibility');

		foreach ($xperformance_layouter as $key => $rows) {
			if(($rows->layout_start_date == '0000-00-00 00:00:00' OR $rows->layout_start_date == 'NULL') AND ($rows->layout_end_date == '0000-00-00 00:00:00' OR $rows->layout_end_date == 'NULL')){
				$data	= array('performance_status' => null);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			elseif ($rows->is_layout == 'n' AND ($rows->layout_start_date != '0000-00-00 00:00:00' AND $rows->layout_start_date != 'NULL') AND ($rows->layout_end_date == '0000-00-00 00:00:00' OR $rows->layout_end_date == 'NULL')){
				$data	= array('performance_status' => 1);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			elseif ($rows->is_layout == 'n' AND ($rows->layout_end_date < $rows->layout_deadline) AND $rows->layout_start_date != '0000-00-00 00:00:00' AND $rows->layout_start_date != 'NULL' AND $rows->layout_end_date != '0000-00-00 00:00:00' AND $rows->layout_end_date != 'NULL') {
				$data	= array('performance_status' => 2);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			elseif ($rows->is_layout == 'y' AND ($rows->layout_end_date < $rows->layout_deadline) AND $rows->layout_start_date != '0000-00-00 00:00:00' AND $rows->layout_start_date != 'NULL' AND $rows->layout_end_date != '0000-00-00 00:00:00' AND $rows->layout_end_date != 'NULL') {
				$data	= array('performance_status' => 3);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			elseif ($rows->layout_end_date > $rows->layout_deadline AND ($rows->layout_start_date != '0000-00-00 00:00:00' AND $rows->layout_start_date != 'NULL') AND ($rows->layout_end_date != '0000-00-00 00:00:00' AND $rows->layout_end_date != 'NULL')) {
				$data	= array('performance_status' => 4);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			else {
				$data	= array('performance_status' => 5);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
		}

		$performance_layouter = $this->performance->select(['draft.draft_id','draft_title','username','category_name','layout_start_date','layout_deadline','layout_end_date','is_layout','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 1)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'asc')->orderBy('username')->getAll('draft');


		$total = count($performance_layouter);

		$pages    = $this->pages;
		$main_view = 'performance/performance_layouter';

		$this->load->view('template', compact('main_view', 'pages', 'performance_layouter'));
	}

	public function performa_layouter_final()
	{
		$performance_layouter = $this->performance->select(['draft.draft_id','draft_title','username','category_name','layout_start_date','layout_deadline','layout_end_date','is_layout','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 2)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'asc')->orderBy('username')->getAll('draft');

		$total = count($performance_layouter);

		$pages    = $this->pages;
		$main_view = 'performance/performance_layouter';

		$this->load->view('template', compact('main_view', 'pages', 'performance_layouter'));
	}

	public function performa_layouter_ontime()
	{
		$performance_layouter = $this->performance->select(['draft.draft_id','draft_title','username','category_name','layout_start_date','layout_deadline','layout_end_date','is_layout','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 3)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'asc')->orderBy('username')->getAll('draft');

		$total = count($performance_layouter);

		$pages    = $this->pages;
		$main_view = 'performance/performance_layouter';

		$this->load->view('template', compact('main_view', 'pages', 'performance_layouter'));
	}

	public function performa_layouter_late()
	{
		$performance_layouter = $this->performance->select(['draft.draft_id','draft_title','username','category_name','layout_start_date','layout_deadline','layout_end_date','is_layout','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 4)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'asc')->orderBy('performance_status')->getAll('draft');

		$total = count($performance_layouter);

		$pages    = $this->pages;
		$main_view = 'performance/performance_layouter';

		$this->load->view('template', compact('main_view', 'pages', 'performance_layouter'));
	}

	public function performa_layouter_error()
	{
		$performance_layouter = $this->performance->select(['draft.draft_id','draft_title','username','category_name','layout_start_date','layout_deadline','layout_end_date','is_layout','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 5)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'asc')->orderBy('username')->getAll('draft');

		$total = count($performance_layouter);

		$pages    = $this->pages;
		$main_view = 'performance/performance_layouter';

		$this->load->view('template', compact('main_view', 'pages', 'performance_layouter'));
	}

	public function performa_layouter_null()
	{
		$performance_layouter = $this->performance->select(['draft.draft_id','draft_title','username','category_name','layout_start_date','layout_deadline','layout_end_date','is_layout','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', null)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'asc')->orderBy('username')->getAll('draft');

		$total = count($performance_layouter);

		$pages    = $this->pages;
		$main_view = 'performance/performance_layouter';

		$this->load->view('template', compact('main_view', 'pages', 'performance_layouter'));
	}
	/*
	*performa editor
	*/
	public function index_edit_revise()
	{
		$xrevisi_naskah = $this->performance->join3('draft','revision','draft')->join3('user','revision','user')->orderBy('revision.draft_id')->getAll('revision');

		foreach ($xrevisi_naskah as $key => $rows) {
			if(($rows->revision_start_date == '0000-00-00 00:00:00' OR $rows->revision_start_date == 'NULL') AND ($rows->revision_end_date == '0000-00-00 00:00:00' OR $rows->revision_end_date == 'NULL')){
				$data	= array('revision_status' => null);
				$this->performance->where('revision_id', $rows->revision_id)->where('user_id', $rows->user_id)->update($data, 'revision');
			}
			elseif (($rows->revision_start_date != '0000-00-00 00:00:00' AND $rows->revision_start_date != 'NULL') AND ($rows->revision_end_date == '0000-00-00 00:00:00' OR $rows->revision_end_date == 'NULL')){
				$data	= array('revision_status' => 1);
				$this->performance->where('revision_id', $rows->revision_id)->where('user_id', $rows->user_id)->update($data, 'revision');
			}
			elseif (($rows->revision_end_date < $rows->revision_deadline) AND ($rows->revision_start_date != '0000-00-00 00:00:00' AND $rows->revision_start_date != 'NULL') AND $rows->revision_end_date != '0000-00-00 00:00:00' AND $rows->revision_end_date != 'NULL') {
				$data	= array('revision_status' => 2);
				$this->performance->where('revision_id', $rows->revision_id)->where('user_id', $rows->user_id)->update($data, 'revision');
			}
			elseif ($rows->revision_end_date > $rows->revision_deadline AND ($rows->revision_start_date != '0000-00-00 00:00:00' AND $rows->revision_start_date != 'NULL') AND ($rows->revision_end_date != '0000-00-00 00:00:00' AND $rows->revision_end_date != 'NULL')) {
				$data	= array('revision_status' => 3);
				$this->performance->where('revision_id', $rows->revision_id)->where('user_id', $rows->user_id)->update($data, 'revision');
			}
			else {
				$data	= array('revision_status' => 4);
				$this->performance->where('revision_id', $rows->revision_id)->where('user_id', $rows->user_id)->update($data, 'revision');
			}
		}
		$revisi_naskah = $this->performance->select(['revision_id','draft.draft_id','user.user_id','username','draft_title','revision_role','revision_start_date','revision_deadline','revision_end_date','revision_status'])->join3('draft','revision','draft')->join3('user','revision','user')->where('revision_role', 'editor')->getAll('revision');

		$pages    = $this->pages;
		$main_view = 'performance/naskah_revisi';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'revisi_naskah','total'));
	}

	public function index_layout_revise()
	{
		$revisi_naskah = $this->performance->select(['revision_id','draft.draft_id','user.user_id','username','draft_title','revision_role','revision_start_date','revision_deadline','revision_end_date','revision_status'])->join3('draft','revision','draft')->join3('user','revision','user')->where('revision_role', 'layouter')->getAll('revision');

		$pages    = $this->pages;
		$main_view = 'performance/naskah_revisi';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'revisi_naskah','total'));
	}

	public function index_desk_screening()
	{
		$xdesk_screening = $this->performance->join3('draft','worksheet','draft')->orderBy('worksheet.draft_id')->getAll('worksheet');

		foreach ($xdesk_screening as $key => $rows) {
			if(($rows->worksheet_deadline == '0000-00-00 00:00:00' OR $rows->worksheet_deadline == 'NULL')){
				$data	= array('worksheet_performance' => null);
				$this->performance->where('draft_id', $rows->draft_id)->update($data, 'worksheet');
			}
			elseif (($rows->worksheet_deadline != '0000-00-00 00:00:00' OR $rows->worksheet_deadline != 'NULL') AND ($rows->worksheet_end_date == '0000-00-00 00:00:00' OR $rows->worksheet_end_date == 'NULL')){
				$data	= array('worksheet_performance' => 1);
				$this->performance->where('draft_id', $rows->draft_id)->update($data, 'worksheet');
			}
			elseif ($rows->worksheet_end_date < $rows->worksheet_deadline AND $rows->worksheet_end_date != '0000-00-00 00:00:00' AND $rows->worksheet_end_date != 'NULL') {
				$data	= array('worksheet_performance' => 2);
				$this->performance->where('draft_id', $rows->draft_id)->update($data, 'worksheet');
			}
			elseif ($rows->worksheet_end_date > $rows->worksheet_deadline AND ($rows->worksheet_end_date != '0000-00-00 00:00:00' AND $rows->worksheet_end_date != 'NULL')) {
				$data	= array('worksheet_performance' => 3);
				$this->performance->where('draft_id', $rows->draft_id)->update($data, 'worksheet');
			}
			else {
				$data	= array('worksheet_performance' => 4);
				$this->performance->where('draft_id', $rows->draft_id)->update($data, 'worksheet');
			}
		}
		$desk_screening = $this->performance->select(['draft.draft_id','worksheet_pic','draft_title','worksheet_deadline','worksheet_end_date','worksheet_performance'])->join3('draft','worksheet','draft')->where('worksheet_performance', null)->orWhere('worksheet_performance', 1)->orWhere('worksheet_performance', 2)->orWhere('worksheet_performance', 3)->getAll('worksheet');

		$pages    = $this->pages;
		$main_view = 'performance/desk_screening';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'desk_screening','total'));
	}

	public function index_desk_screening_error()
	{
		$desk_screening = $this->performance->select(['draft.draft_id','worksheet_pic','draft_title','worksheet_deadline','worksheet_end_date','worksheet_performance'])->join3('draft','worksheet','draft')->where('worksheet_performance', 4)->getAll('worksheet');

		$pages    = $this->pages;
		$main_view = 'performance/desk_screening';

		$this->load->view('template', compact('pagination','main_view', 'pages', 'desk_screening','total'));
	}
	/*
	*performa layout cover
	*/
	public function performa_cover()
	{
		$xperformance_cover = $this->performance->join3('draft','responsibility','draft')->join3('user','responsibility','user')->orderBy('responsibility.draft_id')->getAll('responsibility');

		foreach ($xperformance_cover as $key => $rows) {
			if(($rows->cover_start_date == '0000-00-00 00:00:00' OR $rows->cover_start_date == 'NULL') AND ($rows->cover_end_date == '0000-00-00 00:00:00' OR $rows->cover_end_date == 'NULL')){
				$data	= array('performance_status' => null);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			elseif ($rows->is_cover == 'n' AND ($rows->cover_start_date != '0000-00-00 00:00:00' AND $rows->cover_start_date != 'NULL') AND ($rows->cover_end_date == '0000-00-00 00:00:00' OR $rows->cover_end_date == 'NULL')){
				$data	= array('performance_status' => 1);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			elseif ($rows->is_cover == 'n' AND ($rows->cover_end_date < $rows->cover_deadline) AND $rows->cover_start_date != '0000-00-00 00:00:00' AND $rows->cover_start_date != 'NULL' AND $rows->cover_end_date != '0000-00-00 00:00:00' AND $rows->cover_end_date != 'NULL') {
				$data	= array('performance_status' => 2);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			elseif ($rows->is_cover == 'y' AND ($rows->cover_end_date < $rows->cover_deadline) AND $rows->cover_start_date != '0000-00-00 00:00:00' AND $rows->cover_start_date != 'NULL' AND $rows->cover_end_date != '0000-00-00 00:00:00' AND $rows->cover_end_date != 'NULL') {
				$data	= array('performance_status' => 3);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			elseif ($rows->cover_end_date > $rows->cover_deadline AND ($rows->cover_start_date != '0000-00-00 00:00:00' AND $rows->cover_start_date != 'NULL') AND ($rows->cover_end_date != '0000-00-00 00:00:00' AND $rows->cover_end_date != 'NULL')) {
				$data	= array('performance_status' => 4);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
			else {
				$data	= array('performance_status' => 5);
				$this->performance->where('draft_id', $rows->draft_id)->where('user_id', $rows->user_id)->update($data, 'responsibility');
			}
		}

		$performance_cover = $this->performance->select(['draft.draft_id','draft_title','username','category_name','cover_start_date','cover_deadline','cover_end_date','is_cover','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 1)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'desc')->orderBy('username')->getAll('draft');
		

		$total = count($performance_cover);

		$pages    = $this->pages;
		$main_view = 'performance/performance_cover';

		$this->load->view('template', compact('main_view', 'pages', 'performance_cover'));
	}

	public function performa_cover_final()
	{
		$performance_cover = $this->performance->select(['draft.draft_id','draft_title','username','category_name','cover_start_date','cover_deadline','cover_end_date','is_cover','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 2)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'desc')->orderBy('username')->getAll('draft');

		$total = count($performance_cover);

		$pages    = $this->pages;
		$main_view = 'performance/performance_cover';

		$this->load->view('template', compact('main_view', 'pages', 'performance_cover'));
	}

	public function performa_cover_ontime()
	{
		$performance_cover = $this->performance->select(['draft.draft_id','draft_title','username','category_name','cover_start_date','cover_deadline','cover_end_date','is_cover','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 3)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'desc')->orderBy('username')->getAll('draft');

		$total = count($performance_cover);

		$pages    = $this->pages;
		$main_view = 'performance/performance_cover';

		$this->load->view('template', compact('main_view', 'pages', 'performance_cover'));
	}

	public function performa_cover_late()
	{
		$performance_cover = $this->performance->select(['draft.draft_id','draft_title','username','category_name','cover_start_date','cover_deadline','cover_end_date','is_cover','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 4)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'desc')->orderBy('performance_status')->getAll('draft');

		$total = count($performance_cover);

		$pages    = $this->pages;
		$main_view = 'performance/performance_cover';

		$this->load->view('template', compact('main_view', 'pages', 'performance_cover'));
	}

	public function performa_cover_error()
	{
		$performance_cover = $this->performance->select(['draft.draft_id','draft_title','username','category_name','cover_start_date','cover_deadline','cover_end_date','is_cover','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', 5)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'desc')->orderBy('username')->getAll('draft');

		$total = count($performance_cover);

		$pages    = $this->pages;
		$main_view = 'performance/performance_cover';

		$this->load->view('template', compact('main_view', 'pages', 'performance_cover'));
	}

	public function performa_cover_null()
	{
		$performance_cover = $this->performance->select(['draft.draft_id','draft_title','username','category_name','cover_start_date','cover_deadline','cover_end_date','is_cover','performance_status'])->join3('category','draft','category')->join3('responsibility','draft','draft')->join3('user','responsibility','user')->where('level', 'layouter')->where('performance_status', null)->where('responsibility.user_id !=', null)->orderBy('responsibility_id', 'desc')->orderBy('username')->getAll('draft');

		$total = count($performance_cover);

		$pages    = $this->pages;
		$main_view = 'performance/performance_cover';

		$this->load->view('template', compact('main_view', 'pages', 'performance_cover'));
	}
}
