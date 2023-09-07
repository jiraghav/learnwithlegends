<?php
$page_title = 'Withdrawal Requests';
include 'includes/header.php';

use v2\Models\Wallet\ChartOfAccount;

?>


<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-6  mb-2">
                <?php include 'includes/breadcrumb.php'; ?>

                <h3 class="content-header-title mb-0" style="display:inline;">Withdrawal Requests</h3>
            </div>

            <div class="content-header-right col-6  mb-2">
                <!-- <?= $note; ?> -->
            </div>
        </div>
        <div class="content-body">


            <section class="card">
                <div class="card-header">

                    <?php $this->view('composed/filters/journals', compact('sieve')); ?>
                    <h4 class="card-title" style="display: inline;"></h4>

                </div>
                <div class="card-content">
                    <div class="card-body table-responsive">


                        <table id="charts_of_accounts_table" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>User</th>
                                    <th>Method</th>
                                    <th style="text-align:right;">AMOUNT($)<br>Fee<br>Payable</th>
                                    <th>NOTES</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($journals as $journal) : ?>
                                    <tr>
                                        <td><span class="badge badge-dark"><?= date("M d, Y", strtotime($journal->created_at)); ?></span><br>
                                            <?= $journal->id; ?>#<?= $journal->publishedState; ?>
                                        </td>
                                        <td><?= @$journal->user->DropSelfLink ?? "N/A"; ?></td>
                                        <td><?= @$journal->withdrawalDetails ?? "N/A";  ?></td>
                                        <td style="text-align:right;">
                                            <?= @$journal->payablesDetails['amount'] ?? "Nil"; ?><br>
                                            <?= @$journal->payablesDetails['fee'] ?? ""; ?>
                                            <br><?= @$journal->payablesDetails['payable'] ?? ""; ?>
                                        </td>
                                        <td><?= $journal->notes; ?></td>
                                        <td>

                                            <div class="btn-group btn-group-sm">
                                                <?php if ($journal->is_editable()) : ?>
                                                    <!-- <a class="btn btn-outline-dark" href="<?= domain ?>/admin/edit_journal/<?= $journal->id; ?>">Edit</a> -->
                                                <?php endif; ?>
                                                <!-- <a class="btn btn-outline-dark" href="<?= domain ?>/admin/view_journal/<?= $journal->id; ?>">View</a> -->

                                                <?php if ($journal->is_pending()) : ?>
                                                    <?php
                                                        $peraWalletAddress = $journal->details['withdrawal_method']['details']['perawallet_address'] ?? null;
                                                        if (!empty($peraWalletAddress)) {
                                                    ?>
                                                        <a class="btn btn-outline-dark perawallet-transfer-now"
                                                           data-wallet-address="<?= $peraWalletAddress; ?>"
                                                           data-dollar-amount="<?= $journal->payablesDetails['amount'] ?>"
                                                           data-confirm-transfer-url="<?= domain ?>/admin/complete_journal/<?= $journal->id; ?>"
                                                           onclick="openTransferModal(event)">Transfer now</a>
                                                    <?php
                                                        } else {
                                                    ?>
                                                        <a class="btn btn-outline-dark" onclick="$confirm_dialog = new ConfirmationDialog('<?= domain ?>/admin/complete_journal/<?= $journal->id; ?>', 
                                                        'Complete withdrawal#<?= $journal->id; ?> ?')">Complete</a>
                                                    <?php
                                                      }
                                                    ?>
                                                    <a class="btn btn-outline-dark" onclick="$confirm_dialog = new ConfirmationDialog('<?= domain ?>/admin/decline_journal/<?= $journal->id; ?>', 'Decline journal?')">Decline</a>
                                                <?php endif; ?>

                                                <?php if ($journal->is_reversible()) : ?>
                                                    <a class="btn btn-outline-dark" onclick="$confirm_dialog = new ConfirmationDialog('<?= domain ?>/admin/reverse_journal/<?= $journal->id; ?>',
                                                    'Reverse withdrawal#<?= $journal->id; ?> ?')">Reverse</a>
                                                <?php endif; ?>

                                            </div>



                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>



                    </div>
                </div>
            </section>

            <ul class="pagination">
                <?= $this->pagination_links($data, $per_page); ?>
            </ul>
        </div>
    </div>
</div>
<!-- END: Content-->


<div id="new_category_app"></div>
<style>
  .perawallet-transfer-now {
    width: 100px;
  }
</style>

<script>
function openTransferModal(event) {
		var walletAddress = event.target.getAttribute("data-wallet-address");
		var dollarAmount = event.target.getAttribute("data-dollar-amount");
		var confirmTransferUrl = event.target.getAttribute("data-confirm-transfer-url");

		jQuery('#react-app').remove();
		jQuery(`body`).append(`<div id="react-app" wallet-address="${walletAddress}" dollar-amount="${dollarAmount}" confirm-transfer-url="${confirmTransferUrl}"></div>`);

		setTimeout(function () {
			initReact();

			setTimeout(function () {
				jQuery('#transferTLPModal').modal('show');
			}, 200);
		}, 200);
	}
</script>

<?php
  $debug = false;
  if (!$debug) { // run npm build for production build and move to below path
?>
    <script defer="defer" src="/template/default/app-assets/js/tlp-transfer.min.js"></script>
<?php
  } else { // run npm start for debug
?>
    <script defer="defer" src="http://localhost:3000/static/js/bundle.js"></script>
<?php } ?>

<?php include 'includes/footer.php'; ?>