<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi_model extends MY_Model{

  public function __construct()
  {
    parent::__construct();
  }
  public $per_page = 10;
  public function insert_notifikasi($data)
  {
    $this->db->insert("notifikasi",$data);
    return $this->db->insert_id();
  }

  public function get_userByUsername($username='')
  {
    $this->db->where('username', $username);
    return $this->db->get('user')->result();    
  }

  public function get_all_adminpenerbitan_superadmin()
  {
    $this->db->where('level', "admin_penerbitan");
    $this->db->or_where('level', "superadmin");
    return $this->db->get('user')->result();    
  }

  public function get_draft_author_idByIdDraft($draft_id='')
  {
    $this->db->select('u.*, a.author_id, da.draft_id');
    $this->db->join('author a', 'a.author_id = da.author_id');
    $this->db->join('user u', 'a.user_id = u.user_id');
    $this->db->where('da.draft_id', $draft_id);
    return $this->db->get('draft_author da')->result();    
  }

  public function get_draft_reviewer_idByIdDraft($value='')
  {
    $this->db->select('u.*, r.reviewer_id, dr.draft_id');
    $this->db->join('reviewer r', 'r.reviewer_id = dr.reviewer_id');
    $this->db->join('user u', 'r.user_id = u.user_id');
    $this->db->where('dr.draft_id', $draft_id);
    return $this->db->get('draft_reviewer dr')->result();    
  }

  public function get_userByIdReviewer($reviewer_id='')
  {
    $this->db->where('reviewer_id', $reviewer_id);
    return $this->db->get('reviewer')->result();    
  }

  public function get_authorsByIds($author_ids='')
  {
    $this->db->where_in('author_id', $author_ids);
    return $this->db->get('author')->result();    
  }

  public function get_authorById($author_id='')
  {
    $this->db->where('author_id', $author_id);
    return $this->db->get('author')->result();    
  }

  public function get_userByLevel($level = '')
  {
    $this->db->where('level', $level);
    return $this->db->get('user')->result();    
  }

  public function get_responsibilityBydraft_id($id_draft='')
  {
    $this->db->where('draft_id', $id_draft);
    return $this->db->get('responsibility')->result();    
  }

  public function get_draftById($id)
  {
    $this->db->where('draft_id', $id);
    return $this->db->get('draft')->result();    
  }

  public function filter_notif($filters, $page, $id_user_kpd)
    {
        $notif = $this->select(['id', 'user.username as pengirim', 'draft.draft_title as judul_buku', 'id_user_pembuat', 'id_user_kepada', 'id_draft', 'ket', 'is_read', 'read_at', 'is_starred', 'starred_at', 'creation_date'])
            ->join_piyambak('draft', 'notifikasi',  'draft_id', 'id_draft')
            ->join_piyambak('user', 'notifikasi',  'user_id', 'id_user_pembuat')
            ->when('keyword', $filters['keyword'])
            ->when('id_user_pembuat', $filters['id_user_pembuat'])
            ->when('id_draft', $filters['id_draft'])
            ->when('is_starred', $filters['is_starred'])
            ->when('is_read', $filters['is_read'])
            ->where('id_user_kepada', $id_user_kpd)
            ->order_by('id')
            ->paginate($page)
            ->get_all();
        $total = $this->select('id')
            ->join_piyambak('draft', 'notifikasi',  'draft_id', 'id_draft')
            ->join_piyambak('user', 'notifikasi',  'user_id', 'id_user_pembuat')
            ->when('keyword', $filters['keyword'])
            ->when('id_user_pembuat', $filters['id_user_pembuat'])
            ->when('id_draft', $filters['id_draft'])
            ->when('is_starred', $filters['is_starred'])
            ->when('is_read', $filters['is_read'])
            ->where('id_user_kepada', $id_user_kpd)
            ->group_by('id')
            ->count();

        return [
            'notif' => $notif,
            'total'  => $total,
        ];
    }

    public function when($params, $data)
    {
        // jika data null, maka skip
        if ($data) {
            if ($params == 'id_user_pembuat') {
                $this->where('id_user_pembuat', $data);
            }

            if ($params == 'id_draft') {
                $this->where('id_draft', $data);
            }
            
            if ($params == 'is_starred') {
                if($data == 'y')
                  $this->where('is_starred', 1);
                if($data == 'n')
                  $this->where('is_starred', 0);
            }

            if ($params == 'is_read') {
              if($data == 'y')
                $this->where('is_read', 1);
              if($data == 'n')
                $this->where('is_read', 0);
            }

            if ($params == 'keyword') {
                $this->group_start();
                // $this->like('draft_title', $data);
                $this->or_like('ket', $data);
                // if ($this->session->userdata('level') != 'reviewer') {
                // }
                $this->group_end();
            }
        }
        return $this;
    }
}