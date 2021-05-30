<div class="input-group">
    <div class="input-group-prepend mr-2">
        <select data-column="2" class="form-control form-control-sm" id="tableFilterMembershipType">
            <option value="" class="font-weight-bold">Jenis Membership (All)</option>
            <?php
            foreach($membershipType as $mshipType){?>
            <option value="{{ $mshipType->type }}">{{ $mshipType->type }}</option><?php
            }?>
        </select>
    </div>
    <div class="input-group-prepend mr-2">
        <select data-column="3" class="form-control form-control-sm" id="tableFilterMembershipCategory">
            <option value="" class="font-weight-bold">Jenis Membership (All)</option>
            <?php
            foreach($membershipCategory as $mshipCategory){?>
                <option value="{{ $mshipCategory->category }}">{{ $mshipCategory->category }}</option><?php
            }?>
        </select>
    </div>
    <div class="input-group-prepend">
        <select data-column="6" class="form-control form-control-sm" id="tableFilterMembershipStatus">
            <option value="" class="font-weight-bold">Status (All)</option>
            <?php
            foreach($membershipStatus as $mshipStatus){?>
            <option value="{{ $mshipStatus->status }}">{{ $mshipStatus->status }}</option><?php
            }?>
        </select>
    </div>
</div>
