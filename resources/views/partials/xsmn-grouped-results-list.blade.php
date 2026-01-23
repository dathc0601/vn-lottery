@foreach($groupedResults as $dayGroup)
    <x-result-card-xsmn-grouped :dayGroup="$dayGroup" :region="$region" />
@endforeach
