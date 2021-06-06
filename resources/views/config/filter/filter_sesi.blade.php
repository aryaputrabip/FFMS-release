<div class="input-group">
    <div class="input-group-prepend">
        <select data-column="4" class="form-control form-control-sm" id="tableFilterSesiStatus">
            <option value="" class="font-weight-bold">Status (All)</option>
            <?php
            foreach($sesiStatus as $sesiStatus){?>
            <option value="{{ $sesiStatus->status }}">{{ $sesiStatus->status }}</option><?php
            }?>
        </select>
    </div>
</div>
