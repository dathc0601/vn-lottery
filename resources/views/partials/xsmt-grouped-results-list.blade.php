@foreach($groupedResults as $dayGroup)
    <x-result-card-xsmt-grouped :dayGroup="$dayGroup" :region="$region" />
@endforeach
