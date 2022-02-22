<table class="table table-bordered">
    <tr>
        <td colspan="2" class="text-center"><img style="margin: auto;" class="thumb-image-md thumbnail"
                src="{{ profile_picture($user->profile_picture) }}">
        </td>
    </tr>
    <tr>
        <td>{{ _lang('Name') }}</td>
        <td>{{ $user->name }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Email') }}</td>
        <td>{{ $user->email }}</td>
    </tr>
    <tr>
        <td>{{ _lang('User Type') }}</td>
        <td>{{ $user->user_type }}</td>
    </tr>
    <tr>
        <td>{{ _lang('User Role') }}</td>
        <td>{{ $user->role->name }}</td>
    </tr>
</table>