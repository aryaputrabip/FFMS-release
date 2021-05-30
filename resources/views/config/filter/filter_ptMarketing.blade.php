<div class="input-group">
    <div class="input-group-prepend mr-2">
        <select data-column="2" class="form-control form-control-sm" id="tableFilterPTMarketingJK">
            <option value="" class="font-weight-bold">Jenis Kelamin (All)</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>
    </div>
    <div class="input-group-prepend">
        <select data-column="4" class="form-control form-control-sm" id="tableFilterPTMarketingStatus">
            <option value="" class="font-weight-bold">Status (All)</option>
            <?php
            foreach($filterStatus as $fStatus){?>
                <option value="{{ $fStatus->status }}">{{ $fStatus->status }}</option><?php
            }?>
        </select>
    </div>
</div>
