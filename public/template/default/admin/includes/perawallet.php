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