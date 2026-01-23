@foreach($results as $result)
    @if($gameType === 'power655')
        <x-vietlott.power-result-card :result="$result" />
    @else
        <x-vietlott.mega-result-card :result="$result" />
    @endif
@endforeach
