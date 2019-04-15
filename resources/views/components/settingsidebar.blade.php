<div class="list-group">
    <a href="/setting/info" class="list-group-item list-group-item-action text-center{{ $item == 'info' ? ' active' : '' }}">
        <i class="fa fa-user-o" aria-hidden="true"></i> 个人信息
    </a>
    <a href="/setting/avatar" class="list-group-item list-group-item-action text-center{{ $item == 'avatar' ? ' active' : '' }}">
        <i class="fa fa-picture-o" aria-hidden="true"></i> 修改头像
    </a>
    <a href="/setting/email" class="list-group-item list-group-item-action text-center{{ $item == 'email' ? ' active' : '' }}">
        <i class="fa fa-envelope-o" aria-hidden="true"></i> 修改邮箱
    </a>
    <a href="/setting/password" class="list-group-item list-group-item-action text-center{{ $item == 'password' ? ' active' : '' }}">
        <i class="fa fa-lock" aria-hidden="true"></i> 修改密码
    </a>
</div>