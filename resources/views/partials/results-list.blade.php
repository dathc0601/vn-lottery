@foreach($results as $result)
    <x-result-card-xskt :result="$result" :region="$region" />
@endforeach
