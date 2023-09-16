<?php
	$userPerawallet = v2\Models\UserWithdrawalMethod::for($auth->id, 'perawallet');
	$perawalletDetail = @$userPerawallet->DetailsArray;

;?>
<div class="form-group">
  <label>Pera Wallet (TLP) Address</label>
  <input type="" placeholder="Add Pera Wallet (TLP) Address" value="<?=$perawalletDetail['perawallet_address'] ?? '';?>" name="details[perawallet_address]" required="" class="form-control">
</div>
