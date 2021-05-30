<div class="input-group">
    <div class="input-group-prepend mr-2">
            <select data-column="3" class="form-control form-control-sm" id="tableFilterLogMemberStatus">
            <option value="" class="font-weight-bold">Status (All)</option>
            <option value="Lunas">Lunas</option>
            <option value="Dalam Cicilan">Dalam Cicilan</option>
        </select>
    </div>
    <div class="input-group-prepend">
        <select data-column="2" class="form-control form-control-sm" id="tableFilterLogMemberKategori">
            <option value="" class="font-weight-bold">Kategori (All)</option>
            <?php
                foreach($logCategory as $logCat){?>
                <option value="{{ $logCat->category }}">{{ $logCat->category }}</option><?php
            }?>
        </select>
    </div>
</div>
