<span class="log-icon log-icon-{{ $history->type }}"></span>
<span>
    @if ($history->user->id)
        <a href="{{ route('users.profile.view', $history->user->id) }}">{{ $history->user->getFullName() }}</a>
    @else
        <span>{{ trans('plugins/audit-log::history.system') }}</span>
    @endif
    @if (Lang::has('plugins/audit-log::history.' . $history->action)) {{ trans('plugins/audit-log::history.' . $history->action) }} @else {{ $history->action }} @endif
    @if ($history->module)
        @if (Lang::has('plugins/audit-log::history.' . $history->module)) {{ trans('plugins/audit-log::history.' . $history->module) }} @else {{ $history->module }} @endif
    @endif
    @if ($history->reference_name)
        @if (empty($history->user) || $history->user->getFullName() != $history->reference_name)
            "{{ Str::limit($history->reference_name, 40) }}"
        @endif
    @endif
    .
</span>
<span class="small italic">{{ $history->created_at->diffForHumans() }} </span>
<span>(<a href="https://whatismyipaddress.com/ip/{{ $history->ip_address }}" target="_blank" title="{{ $history->ip_address }}">{{ $history->ip_address }}</a>)</span>
