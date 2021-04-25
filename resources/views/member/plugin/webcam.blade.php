<!-- Modal -->
<div id="webcamModal" class="modal fade" style="top: 6vh;">
    <div class="modal-dialog" style="width: 430px;">
        <div class="modal-content" style="border-radius: .25rem; border-style: none;">
            <div class="modal-header pt-2 pb-2 pl-3 pr-3" style="padding: 0;" style="border-bottom: 1px solid #dde0e6!important;">
                <h6 class="modal-title" id="exampleModalLabel"><i id="marketingModalIco" class="fas fa-user-plus"></i>Ambil Gambar</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeCam();">
                    <span aria-hidden="true">&times;</span>
                </button>
                <canvas id="canvas" style="display: none;"></canvas>
            </div>

            <div class="modal-body" style="background-color: #ebeef3;">
                <video autoplay="true" id="memberCapture" style="width: 400px; height: 400px; max-resolution:0 auto; background-color: #666; position:relative;"></video>
                <button class="btn btn-primary w-100" onclick="takePicture();">Take Picture</button>
            </div>
        </div>
    </div>
</div>
