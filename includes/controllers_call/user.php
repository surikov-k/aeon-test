<?php

function controller_user($act, $d) {
  if ($act == 'edit_window') return User::edit_window($d);
  if ($act == 'edit_update') return User::edit_update($d);
  if ($act == 'delete') return User::delete($d);
  return '';
}
