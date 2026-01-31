@props(['item', 'index' => null])

@if(in_array($item->type, ['xsmb_days', 'xsmt_days', 'xsmn_days']))
    <x-navigation.days-dropdown :item="$item" :index="$index" />
@endif
