<script>
    //SWAL INIT
    const ToastTimer = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
    });

    const ToastDefault = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false
    });

    function messagingErrorCustomTimer(message){
        ToastTimer.fire({
            icon: 'error',html: message
        });
    }

    function messagingInfoCustomTimer(message){
        ToastTimer.fire({
            icon: 'info',
            html: message
        })
    }

    function messagingErrorCustomDefault(message){
        ToastDefault.fire({
            icon: 'error',html: message
        });
    }

    function messagingInfoCustomDefault(message){
        ToastDefault.fire({
            icon: 'info',
            html: message
        })
    }
</script>
