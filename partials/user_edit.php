<div class="modal_head">
  <i class="icon_close" onclick="common.modal_hide()"></i>
</div>
<div class="modal_body">
  <div class="input_group_modal">
    <div>First name</div>
    <input type="text" id="number" value="{$user.first_name}">
  </div>
  <div class="input_group_modal">
    <div>Last name</div>
    <input type="text" id="size" value="{$user.last_name}">
  </div>
  <div class="input_group_modal">
    <div>Phone</div>
    <input type="tel" id="price" value="{$user.phone}">
  </div>
  <div class="input_group_modal">
    <div>Email</div>
    <input type="email" id="price" value="{$user.email}">
  </div>
  <div class="input_group_modal">
    <div>Plot</div>
    <input type="text" id="price" value="{$user.plot}">
  </div>
  <div class="modal_controls">
    <div>
      <div class="btn_modal" onclick="common.plot_edit_update({$plot.id});">Save</div>
    </div>
    <div>
      <div class="btn_modal light" onclick="common.modal_hide();">Cancel</div>
    </div>
  </div>
</div>
