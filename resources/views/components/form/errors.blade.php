@if($errors->any())
    <div class="border border-red-500 text-red-600 px-4 py-3 mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif