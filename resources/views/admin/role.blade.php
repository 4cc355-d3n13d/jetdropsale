<?php
use App\Models\User;
use App\Models\User\UserRole;
?>

<span class="px-2 py-1 text-xs font-semibold rounded mr-2 bg-{{ $title == UserRole::$mapRoles[UserRole::ROLE_ADMIN]['title'] ? "danger" : "success" }} text-white">
    {{ $title }}
</span>
