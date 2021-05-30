<div class="input-group">
    <div class="input-group-prepend mr-2">
        <select data-column="4" class="form-control form-control-sm" id="tableFilterMembershipName">
            <option value="" class="font-weight-bold">Membership (All)</option>
            <?php
            foreach($membership as $mship){?>
            <option value="{{ $mship->name }}">{{ $mship->name }}</option><?php
            }?>
        </select>
    </div>
    <div class="input-group-prepend mr-2">
        <select data-column="4" class="form-control form-control-sm" id="tableFilterMembershipType">
            <option value="" class="font-weight-bold">Jenis Membership (All)</option>
            <?php
            foreach($membershipType as $mshipType){?>
            <option value="{{ $mshipType->type }}">{{ $mshipType->type }}</option><?php
            }?>
        </select>
    </div>
    <div class="input-group-prepend">
        <select data-column="3" class="form-control form-control-sm" id="tableFilterStatus">
            <option value="" class="font-weight-bold">Status (All)</option>
            <?php
            foreach($memberStatus as $mStatus){?>
            <option value="{{ $mStatus->status }}">{{ $mStatus->status }}</option><?php
            }?>
        </select>
    </div>
</div>
