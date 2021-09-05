<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WIKI GRABBER</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <form action="" method="post" class="flex flex-col mt-40 w-1/2 container mx-auto text-center">
        @csrf
        <div class="grid grid-cols-4 gap-3">
            <div class="col-span-3">
                <label for="">Wiki URL</label>
                <input type="url" name="wiki_url" class="w-full">
            </div>
            <div class="">
                <label for="">Stop at</label>
                <input type="number" name="stop_at" class="w-full">
            </div>
        </div>
        @foreach ($errors->all() as $error)
        <div class="text-red-600">{{ $error }}</div>
        @endforeach
        @if(session('success'))
        <div class="text-green-600">{{ session('success') }}</div>
        @endif
        <button type="submit" class="bg-green-600 mt-10 py-2 text-white">GO</button>
    </form>
</body>

</html>