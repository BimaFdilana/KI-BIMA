@if (session('success'))
    <div class="animate__animated animate__slideInRight fixed right-5 top-20 z-40 shadow" role="alert" id="alert">
        <div class="flex">
            <div class="w-16 rounded-l bg-green-500 bg-opacity-85 p-1 text-center">
                <div class="flex h-full items-center justify-center">
                    <i class="fa-solid fa-check text-white"></i>
                </div>
            </div>
            <div class="w-full rounded-r border-r border-green-400 bg-white bg-opacity-85 p-2">
                <div>
                    <p class="font-bold text-green-600">Success!</p>
                    <ul class="list-inside list-disc text-sm text-gray-700">
                        <p>{{ session('success') }}</p>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="animate__animated animate__slideInRight fixed right-5 top-5 z-40 shadow" role="alert" id="alert">
        <div class="flex">
            <div class="w-16 rounded-l bg-red-500 bg-opacity-85 p-1 text-center">
                <div class="flex h-full items-center justify-center">
                    <i class="fas fa-exclamation text-white"></i>
                </div>
            </div>
            <div class="w-full min-w-40 rounded-r border-r border-red-400 bg-white bg-opacity-85 p-2">
                <div>
                    <p class="font-bold text-red-600">Error!</p>
                    <ul class="list-inside list-disc text-sm text-red-500">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('notif'))
    @php
        $notif = session('notif');
    @endphp
    <div class="animate__animated animate__slideInRight fixed bottom-5 right-5 z-40 flex min-h-screen items-end" id="alert">
        <div class="pointer-events-auto w-80 max-w-full rounded border bg-clip-padding text-sm shadow-sm">
            <div class="flex items-center rounded-t border-b-2 bg-gray-100 bg-clip-padding px-3 py-2 text-gray-500">
                <svg class="mr-2 select-none rounded text-lg" width="20" height="20" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 54 33">
                    <path fill="#06B6D4" fill-rule="evenodd" d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.513 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.513-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z" clip-rule="evenodd" />
                </svg>
                <strong class="mr-auto">{{ $notif['type'] }}</strong>
                <small>{{ $notif['time'] }}</small>
                <button type="button" class="alert-del -mr-1 ml-3 box-content rounded p-1 text-black opacity-50 hover:opacity-100">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="192" height="192" fill="#000000" viewBox="0 0 256 256">
                        <rect width="256" height="256" fill="none"></rect>
                        <line x1="200" y1="56" x2="56" y2="200" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="24" fill="none"></line>
                        <line x1="200" y1="200" x2="56" y2="56" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="24" fill="none"></line>
                    </svg>
                </button>
            </div>
            <div class="bg-white p-3">{{ $notif['message'] }}</div>
        </div>
    </div>
@endif

<script>
    const preload = document.getElementById('preloader');

    // Memeriksa apakah elemen preloader ada dan tidak memiliki kelas 'hidden'
    if (preload && !preload.classList.contains('hidden')) {
        setTimeout(function() {
            var alert = document.getElementById('alert');
            alert.classList.remove('right-5');
            alert.classList.add('right-0');
            alert.classList.remove('animate__slideInRight');
            alert.classList.add('animate__slideOutRight');
            setTimeout(function() {
                alert.classList.add('hidden');
            }, 1000);
        }, 5000);
    }

    // setTimeout(function() {
    //     var hilang = document.getElementById('hilang');
    //     setTimeout(function() {
    //         hilang.classList.add('hidden');
    //     }, 1000);
    // }, 5000);

    var alert_del = document.querySelectorAll('.alert-del');
    alert_del.forEach((x) =>
        x.addEventListener('click', function() {
            var notificationElement = x.parentElement.parentElement.parentElement;
            notificationElement.classList.remove('right-5');
            notificationElement.classList.add('right-0');
            notificationElement.classList.remove('animate__slideInRight');
            notificationElement.classList.add('animate__slideOutRight');

            // Tunggu animasi selesai sebelum menghilangkan elemen
            notificationElement.addEventListener('animationend', function() {
                notificationElement.classList.add('hidden');
            });
        })
    );
</script>
