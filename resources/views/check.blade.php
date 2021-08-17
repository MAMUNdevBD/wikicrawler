<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    @foreach ($datas->births as $data)
    Full Name: {{ $data->pages[0]->displaytitle }} <br />
    Date of Birth: {{ $date }} {{ $data->year }}<br />
    PageId: {{ $data->pages[0]->pageid }}<br />
    @if (isset($data->pages[0]->description))
    Profession: {{ $data->pages[0]->description }} <br />
    @endif
    @if (isset($data->pages[0]->thumbnail))
    <img src="{{ $data->pages[0]->thumbnail->source }}" alt="">
    @endif
    <hr>
    @endforeach
</body>

</html>
