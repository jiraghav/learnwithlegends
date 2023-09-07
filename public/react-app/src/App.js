import {PeraWalletConnect} from "@perawallet/connect";
import React, { useState, useEffect } from 'react';
import algosdk from "algosdk";

const peraWallet = new PeraWalletConnect({
    shouldShowSignTxnToast: false
});

const isDebuging = 0; // use same account to send amount

var baseUrl = 'https://mainnet-algorand.api.purestake.io/ps2';
const purestakeApiKey = "6iq400ijoVaRYvbGTUw6fRN3lFFbKMb1lYZMG1oj"; // Created From https://developer.purestake.io

const peraWalletApiKey = 'pera-web-Dr98Vnmu-0yFejf-G-A1M7-7cZS6P0d-'; // Copied From https://web.perawallet.app

const algodClient = new algosdk.Algodv2({
    'X-API-key': purestakeApiKey,
}, baseUrl, '');
function App(props) {
  const [accountAddress, setAccountAddress] = useState(null);
  const [chargeAmount, setChargeAmount] = useState('');
  const [toAccountAddress] = useState(props.toAccount);
  const [dollarAmount] = useState(props.dollarAmount);
  const [assetId] = useState(987374809);
  const [assetFraction] = useState(100);
  const [disabledAddBtn, setDisabledAddBtn] = useState(false);
  const [addBtnText, setAddBtnText] = useState('Transfer');
  const [calculateUsdValue, setCalculateUsdValue] = useState(false);

  useEffect(() => {
    peraWallet.reconnectSession().then((accounts) => {
      peraWallet.connector?.on("disconnect", handleDisconnectWalletClick);

      if (accounts.length) {
        setAccountAddress(accounts[0]);
      }
    });

    getUnitAssetValue();
  }, []);

  function getUnitAssetValue() {
    setCalculateUsdValue(true);
    getAssetPriceUsd().then((assetUsdValue) => {
      setChargeAmount((1/assetUsdValue) * dollarAmount);
      setCalculateUsdValue(false);
    }).catch((error) => {
        showFailedToast("TLP price fetching failed!");
        setCalculateUsdValue(false);
    });
  }

  function checkBalance() {
    algodClient.accountAssetInformation(accountAddress, assetId).do().then((response) => {
        if (response && (response['asset-holding'] || null)) {
          if ((response['asset-holding']['amount'] / assetFraction) >= chargeAmount) {
              showInfoToast('Account identified, Please sign transaction now!', null);
              signTransaction();
          } else {
              showFailedToast('No enough balance in your account!');
              enableAddBtn();
          }
        } else {
            showFailedToast('No TLP coins in your account!');
            enableAddBtn();
        }
    }).error((error) => {
        showFailedToast('Account not identified, Please try again later!');
        enableAddBtn();
    });
  }

  function enableAddBtn() {
    setDisabledAddBtn(false);
    setAddBtnText("Transfer");
  }

  function disableAddBtn() {
    setDisabledAddBtn(true);
    setAddBtnText("Please wait...");
  }

  function signTransaction() {
    const FROM_ADDRESS = accountAddress;
    const TO_ADDRESS = isDebuging ? FROM_ADDRESS : toAccountAddress;
    const ASSET_ID = assetId;

    try {
        algodClient.getTransactionParams().do().then((suggestedParams) => {
          const optInTxn = algosdk.makeAssetTransferTxnWithSuggestedParamsFromObject({
            from: FROM_ADDRESS,
            to: TO_ADDRESS,
            assetIndex: ASSET_ID,
            amount: parseInt(chargeAmount * assetFraction),
            suggestedParams
          });

          const singleTxnGroups = [{txn: optInTxn, signers: [FROM_ADDRESS]}];

          peraWallet.signTransaction([singleTxnGroups]).then((signedTxn) => {
            showInfoToast("Please wait signing transaction!", null);

            algodClient.sendRawTransaction(signedTxn).do().then(({txId}) => {
                showInfoToast("Transaction signed successfully, Waiting for confirmation!", null);

                algosdk.waitForConfirmation(algodClient, txId, 4).then((response) => {
                    showSuccessToast("Transaction successfully confirmed, Transfer successfully!", 10000);

                    window.$('#transferTLPModal').modal('hide');

                    confirmSuccessfulTransaction();

                    enableAddBtn();
                }).catch((error) => {
                    showFailedToast("Transaction confirmation failed!");
                    enableAddBtn();
                });

            }).catch((error) => {
                showFailedToast("Sign transaction failed!");
                enableAddBtn();
            });

          }).catch((error) => {
            showFailedToast("Transaction cancelled!");
            enableAddBtn();
          });
        });
    } catch (e) {
      enableAddBtn();
    }
  }

  function transferBtnHandleClick() {
    disableAddBtn();
		if (chargeAmount > 0) {
		  handleConnectWalletClick();
		} else {
      showFailedToast("Please check your details!");
		}
    enableAddBtn();
	}

  function showSuccessToast(msg, toasterTime = 5000) {
    window.notify(msg, 'success', toasterTime);
  }

  function showInfoToast(msg, toasterTime = 5000) {
    window.notify(msg, 'info', toasterTime);
  }

  function showFailedToast(msg, toasterTime = 5000) {
    window.notify(msg, 'danger', toasterTime);
  }

  async function getAssetPriceUsd() {
    const response = await fetch(`https://mainnet.api.perawallet.app/v1/assets/${assetId}/`, {
        headers: {
            'x-api-key': peraWalletApiKey,
        }
    });
    const data = await response.json();
    return data.usd_value;
  }

  function confirmSuccessfulTransaction(coinsAmount) {
      fetch(props.confirmTransferUrl, {
        method: "GET"
      }).then((response) => {
          showSuccessToast("Transaction successfully transfered, Please wait for redirection!", 10000);
          setTimeout(function () {
            window.location.reload();
          }, 5000);
      }).catch((error) => {
          showFailedToast("Transcation failed to complete!");
      });
  }

  return (
    <>
      <div id="transferTLPModal" className="modal fade" role="dialog">
        <div className="modal-dialog">
          <div className="modal-content">
            <div className="modal-header">
              <h5 className="modal-title">Transfer TLP</h5>
              <button type="button" className="close" data-dismiss="modal">&times;</button>
            </div>
            <form className="form add-tlp" id="">
            <div className="modal-body">
                <div className="form-group add-amount text-center">
                  <h2>
                    Withdrawal request: <br/><b>${dollarAmount}</b>
                    <br />
                    {dollarAmount && (dollarAmount > 0) && (
                      <div>
                        <b>=</b>
                        <br />
                        {calculateUsdValue && (
                          <span>Calculating TLP Value</span>
                        )}
                        {!calculateUsdValue && (
                          <span>TLP: <small><br/><b>{chargeAmount}</b></small></span>
                        )}
                      </div>
                    )}
                  </h2>
                </div>
                {dollarAmount && (dollarAmount > 0) && !calculateUsdValue && (
                  <div className="text-center">
                    <button className="btn btn-outline-dark"
                            type="button"
                            disabled={disabledAddBtn}
                            onClick={transferBtnHandleClick}>
                      {addBtnText}
                    </button>
                  </div>
                )}
            </div>
            </form>
          </div>
        </div>
      </div>
    </>
  );

  function handleConnectWalletClick() {
    peraWallet
      .connect()
      .then((newAccounts) => {
        peraWallet.connector?.on("disconnect", handleDisconnectWalletClick);

        setAccountAddress(newAccounts[0]);

        showInfoToast('Identifing pera wallet account, please wait!', null);

        disableAddBtn();

        checkBalance();
      })
      .catch((error) => {
        if (error?.data?.type !== "CONNECT_MODAL_CLOSED") {}
      });
  }

  function handleDisconnectWalletClick() {
    peraWallet.disconnect();
    setAccountAddress(null);
  }
}

export default App;
