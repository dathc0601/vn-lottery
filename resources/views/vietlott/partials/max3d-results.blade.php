@foreach($results as $result)
    @if($gameType === 'max3dpro')
        <x-vietlott.max3dpro-result-card :result="$result" />
    @else
        <x-vietlott.max3d-result-card :result="$result" />
    @endif
@endforeach
