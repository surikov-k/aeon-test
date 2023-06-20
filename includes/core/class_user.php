<?php

const DEFAULT_VILLAGE_ID = 1;
const DEFAULT_PHONE_CODE = 1111;
const DEFAULT_ACCESS_CODE = 1;

class User
{

  // GENERAL

  public static function user_status($d)
  {
    // vars
    $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
    $phone = isset($d['phone']) ? preg_replace('~\D+~', '', $d['phone']) : 0;
    // where
    if ($user_id) $where = "user_id='" . $user_id . "'";
    else if ($phone) $where = "phone='" . $phone . "'";
    else return [];
    // info
    $q = DB::query("SELECT user_id, phone, access FROM users WHERE " . $where . " LIMIT 1;") or die (DB::error());
    if ($row = DB::fetch_row($q)) {
      return [
        'id' => (int)$row['user_id'],
        'access' => (int)$row['access']
      ];
    } else {
      return [
        'id' => 0,
        'access' => 0
      ];
    }
  }

  public static function user_info($user_id)
  {
    $q = DB::query("SELECT user_id, plot_id, first_name, last_name, email, phone 
            FROM users WHERE user_id='" . $user_id . "' LIMIT 1;") or die (DB::error());
    if ($row = DB::fetch_row($q)) {
      return [
        'id' => (int)$row['user_id'],
        'plot_id' => $row['plot_id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'phone' => $row['phone'],
        'email' => $row['email'],
      ];
    } else {
      return [
        'id' => 0,
        'plot_id' => '',
        'first_name' => '',
        'last_name' => '',
        'phone' => '',
        'email' => '',
      ];
    }
  }


  public static function users_list_plots($number)
  {
    // vars
    $items = [];
    // info
    $q = DB::query("SELECT user_id, plot_id, first_name, email, phone
            FROM users WHERE plot_id LIKE '%" . $number . "%' ORDER BY user_id;") or die (DB::error());
    while ($row = DB::fetch_row($q)) {
      $plot_ids = explode(',', $row['plot_id']);
      $val = false;
      foreach ($plot_ids as $plot_id) if ($plot_id == $number) $val = true;
      if ($val) $items[] = [
        'id' => (int)$row['user_id'],
        'first_name' => $row['first_name'],
        'email' => $row['email'],
        'phone_str' => phone_formatting($row['phone'])
      ];
    }
    // output
    return $items;
  }

  public static function users_list($d = [])
  {
    // vars
    $search = isset($d['search']) && trim($d['search']) ? $d['search'] : '';
    $offset = isset($d['offset']) && is_numeric($d['offset']) ? $d['offset'] : 0;
    $limit = 20;
    $items = [];
    // where
    $where = [];
    if ($search) {
      $where[] = "first_name LIKE '%" . $search . "%'";
      $where[] = "phone LIKE '%" . $search . "%'";
      $where[] = "email LIKE '%" . $search . "%'";
    }
    $where = $where ? "WHERE " . implode(" OR ", $where) : "";
    // info
    $q = DB::query("SELECT user_id, plot_id, first_name, last_name, phone, email, last_login
            FROM users " . $where . " LIMIT " . $offset . ", " . $limit . ";") or die (DB::error());

    while ($row = DB::fetch_row($q)) {
      $items[] = [
        'id' => (int)$row['user_id'],
        'plot_id' => $row['plot_id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'phone' => $row['phone'],
        'email' => $row['email'],
        'last_login' => date('Y/m/d', $row['last_login'])
      ];
    }
    // paginator
    $q = DB::query("SELECT count(*) FROM users " . $where . ";");
    $count = ($row = DB::fetch_row($q)) ? $row['count(*)'] : 0;
    $url = 'users';
    if ($search) $url .= '?search=' . $search . '&';
    paginator($count, $offset, $limit, $url, $paginator);
    // output
    return ['items' => $items, 'paginator' => $paginator];
  }

  public static function users_fetch($d = [])
  {
    $info = User::users_list($d);
    HTML::assign('users', $info['items']);
    return ['html' => HTML::fetch('./partials/users_table.html'), 'paginator' => $info['paginator']];
  }

  // ACTIONS

  public static function edit_window($d = [])
  {
    $user_id = isset($d['id']) && is_numeric($d['id']) ? $d['id'] : 0;
    HTML::assign('user', User::user_info($user_id));
    return ['html' => HTML::fetch('./partials/user_edit.html')];
  }

  public static function edit_update($d = [])
  {
    // vars
    $user_id = isset($d['id']) && is_numeric($d['id']) ? $d['id'] : 0;
    $plot_id = isset($d['plot_id']) ? implode(',', array_filter(explode(',', $d['plot_id']), 'is_numeric')): '';
    $first_name = $d['first_name'] ?? '';
    $last_name = $d['last_name'] ?? '';
    $phone = isset($d['phone']) ? preg_replace('~\D+~', '', $d['phone']) : '';
    $email = isset($d['email']) ? strtolower($d['email']) : '';
    $offset = isset($d['offset']) ? preg_replace('~\D+~', '', $d['offset']) : 0;

    // validate
    if (!$first_name) return error_response(1003, 'First name was missing or was passed in the wrong format.', ['first_name' => 'empty field']);

    if (!$last_name) return error_response(1003, 'Last name was missing or was passed in the wrong format.', ['last_name' => 'empty field']);

    if (!$phone) return error_response(1003, 'Phone was missing or was passed in the wrong format.', ['phone' => 'empty field']);

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) return error_response(1003, 'Email  was missing or was passed in the wrong format.', ['email' => 'empty field']);

    // update
    if ($user_id) {
      $set = [];
      $set[] = "first_name='" . $first_name . "'";
      $set[] = "last_name='" . $last_name . "'";
      $set[] = "phone='" . $phone . "'";
      $set[] = "email='" . $email . "'";
      $set[] = "plot_id='" . $plot_id . "'";
      $set[] = "updated='" . Session::$ts . "'";
      $set = implode(", ", $set);
      DB::query("UPDATE users SET " . $set . " WHERE user_id='" . $user_id . "' LIMIT 1;") or die (DB::error());
    } else {
      DB::query("INSERT INTO users (
                village_id,
                first_name,
                last_name,
                phone,
                email,
                plot_id,
                updated,
                phone_code,
                access
            ) VALUES (
                '" . DEFAULT_VILLAGE_ID . "',
                '" . $first_name . "',
                '" . $last_name . "',
                '" . $phone . "',
                '" . $email . "',
                '" . $plot_id . "',
                '" . Session::$ts . "',
                '" . DEFAULT_PHONE_CODE . "',
                '" . DEFAULT_ACCESS_CODE . "'
            
            );") or die (DB::error());
    }
    // output
    return User::users_fetch(['offset' => $offset]);
  }


}
