{{--
    Status filter pills — the Task screen's status row, as a component.

    Clicking one writes its value into the form's hidden #status field and
    submits, so the pills are just another filter and travel with everything
    else in the query string.

    Expects:
      $pills   [value => label]  in the order they should read
      $counts  [value => int]    how many records sit behind each pill
      $active  string            the currently selected value ('' = all)
--}}
<div>
    @foreach($pills as $value => $label)
        <span class="btn btn-sm nav-item waves-effect waves-light" style="padding-left:1px; padding-right:1px;">
            <a class="btn btn-sm nav-link {{ (string) $active === (string) $value ? 'active' : 'inactive' }}"
               onclick="filter_data('{{ $value }}')">
                <span>{{ $label }}{{ ($counts[$value] ?? 0) > 0 ? ' ( ' . $counts[$value] . ' )' : '' }}</span>
            </a>
        </span>
    @endforeach
</div>
