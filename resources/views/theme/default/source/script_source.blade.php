<!-- jQuery -->
<script src="{{ asset('/lib/jquery/jquery.min.js') }}"></script>
<!-- pooper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<!-- Bootstrap -->
<script src="{{ asset('/lib/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- AdminLTE -->
<script src="{{ asset('/lib/adminlte/js/adminlte.js') }}"></script>
<!-- Overlay Scrollbar -->
<script src="{{ asset('/lib/overlayScrollbars/js/OverlayScrollbars.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('/lib/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- MomentJS -->
<script src="{{ asset('/lib/moment.js/moment.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('/lib/chart.js/Chart.min.js') }}"></script>
<!-- ChartJS Datalabels -->
<script src="{{ asset('/lib/chartjs-datalabels/chartjs-plugin-datalabels.min.js') }}"></script>
<!-- Datatables Filter -->
<script src="{{ asset('/lib/datatables-filter/filterDropDown.js') }}"></script>

<!-- RESOURCES -->
<script src="{{ asset('/js/custom-script.js') }}"></script>


<script>
    $(function () {
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('member.dataChecking') }}"
        });
    });
</script>
