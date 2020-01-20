<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"
        integrity="sha256-Hgwq1OBpJ276HUP9H3VJkSv9ZCGRGQN+JldPJ8pNcUM=" crossorigin="anonymous"></script>
<script>
    $(function () {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            positionClass: 'toast-top-center',
            timeOut: 4000
        };

        @if(session()->has('info'))
        if (sessionStorage.getItem('sessiontoastr') !== '{{ session('info') }}') {
            sessionStorage.setItem('sessiontoastr', '{{ session('info') }}');
            toastr.info('{{ session('info') }}');
        } else {
            sessionStorage.removeItem('sessiontoastr');
        }
        @endif

            @if(session()->has('success'))
        if (sessionStorage.getItem('sessiontoastr') !== '{{ session('success') }}') {
            sessionStorage.setItem('sessiontoastr', '{{ session('success') }}');
            toastr.success('{{ session('success') }}');
        } else {
            sessionStorage.removeItem('sessiontoastr');
        }
        @endif

            @if(session()->has('warning'))
        if (sessionStorage.getItem('sessiontoastr') !== '{{ session('warning') }}') {
            sessionStorage.setItem('sessiontoastr', '{{ session('warning') }}');
            toastr.warning('{{ session('warning') }}');
        } else {
            sessionStorage.removeItem('sessiontoastr');
        }
        @endif

            @if(session()->has('error'))
        if (sessionStorage.getItem('sessiontoastr') !== '{{ session('error') }}') {
            sessionStorage.setItem('sessiontoastr', '{{ session('error') }}');
            toastr.error('{{ session('error') }}');
        } else {
            sessionStorage.removeItem('sessiontoastr');
        }
        @endif
    });
</script>
