<?php

use v2\Shop\Shop;
use v2\Models\Withdrawal;
use v2\Models\UserWithdrawalMethod;
use Illuminate\Database\Capsule\Manager as DB;
use v2\Jobs\Jobs\SendEmailForCompletedWithdrawal;
use v2\Jobs\Jobs\SendEmailForWithdrawalInformationUpdate;
use v2\Models\Wallet\Classes\AccountManager;

/**
 *
 */
class WithdrawalsController extends controller
{

    public function __construct()
    {


        if (!$this->admin()) {
            $this->middleware('current_user')
                ->mustbe_loggedin()
                ->must_have_verified_email();
            // ->must_have_verified_company();
        }
    }



    public function index()
    {
        echo "this isi index";
    }
    public function process_bulk_action()
    {
        $model_register = [
            'withdrawal' => [
                'model' => Withdrawal::class,
            ],
            'stakes' => [
                'model' => 'v2\Models\Stake',
            ],
        ];

        $model = $model_register[$_POST['model']]['model'];

        $response =     $model::bulk_action($_POST['action'], $_POST['records']);

        if ($response || false) {
            Redirect::back();
        }

        Redirect::back();
    }



    public function process($withdraw_id, $gateway)
    {
        if (!$this->admin()) {
            die();
        }

        echo "<pre>";

        $withdrawal = Withdrawal::find($withdraw_id);

        if ($withdrawal == null) {
            Session::putFlash('danger', "Invalid Request.");
            Redirect::back();
        }

        if ($withdrawal->is_complete()) {

            Session::putFlash('danger', "Already  completed.");
            Redirect::back();
        }
    }


    public function pay_with($withdraw_id, $gateway)
    {
        if (!$this->admin()) {
            die();
        }

        $withdrawals = Withdrawal::where('id', $withdraw_id)->Pending()->get();

        if ($withdrawals->isEmpty()) {
            Session::putFlash('danger', "Invalid Request.");
            Redirect::back();
        }

        $shop = new Shop();
        $attempt = $shop
            ->setPaymentMethod('coinpayment')
            ->setWithdrawalRequests($withdrawals);

        Redirect::back();
    }


    public function push($withdraw_id, $status)
    {
        if (!$this->admin()) {
            die();
        }


        $withdrawal = Withdrawal::find($withdraw_id);

        if ($withdrawal == null) {
            Session::putFlash('danger', "Invalid Request.");
            Redirect::back();
        }

        if ($withdrawal->is_complete()) {

            Session::putFlash('danger', "Already  completed.");
            Redirect::back();
        }


        DB::beginTransaction();

        try {

            $withdrawal->update([
                'status' => $status,
                'admin_id' => $this->admin()->id,
            ]);

            DB::commit();
            Session::putFlash('success', "Withdrawal marked as $status");


            //send withdrawal completed email
            if ($withdrawal->is_complete()) {

                SendEmailForCompletedWithdrawal::dispatch(compact('withdrawal'));

                /* 
                $receiver_subject = "Withrawal Request ID:#$withdrawal->id Completed";
                $mailer = new Mailer;

                $receiver_content =  $this->buildView('emails/completed_withdrawal', compact('withdrawal'), true);

                //sender email
                $mailer->sendMail(
                    "{$withdrawal->user->email}",
                    "$receiver_subject",
                    $receiver_content,
                    "{$withdrawal->user->firstname}"
                ); */
            }
        } catch (Exception $e) {
            DB::rollback();
            Session::putFlash('danger', "Something went wrong. Please try again.");
        }


        Redirect::back();
    }

    public function user_push($withdraw_id, $status)
    {
        $allowed = ['declined'];
        if (!in_array($status, $allowed)) {

            Redirect::back();
        }


        $withdrawal = Withdrawal::find($withdraw_id);

        if ($withdrawal == null) {
            Session::putFlash('danger', "Invalid Request.");
            Redirect::back();
        }

        if ($withdrawal->is_complete()) {

            Session::putFlash('danger', "Already  completed.");
            Redirect::back();
        }


        DB::beginTransaction();

        try {

            $withdrawal->update([
                'status' => $status,
            ]);

            DB::commit();
            Session::putFlash('success', "Withdrawal marked as $status");
        } catch (Exception $e) {
            DB::rollback();
            Session::putFlash('danger', "Something went wrong. Please try again.");
        }


        Redirect::back();
    }


    public function submit_withdrawal_request()
    {
        $auth = $this->auth();

        $method_details = UserWithdrawalMethod::where('id', $_POST['method'])->where('user_id', $auth->id)->first();

        $rules_settings =  SiteSettings::find_criteria('rules_settings');
        $min_withdrawal_usd = $rules_settings->settingsArray['min_withdrawal_usd'];


        $this->validator()->check(Input::all(), array(
            'method' => [
                'required' => true,
            ],
            'amount' => [
                'required' => true,
                'positive' => true,
                'min_value' => $method_details->method == 'paypal' ? $min_withdrawal_usd : 1,
            ],
        ));


        if (!$this->validator->passed()) {
            Session::putFlash('danger', Input::inputErrors());
            Redirect::back();
        }

        if ($method_details->method == 'paypal') {
          $this->verify_2fa();
        }


        $amount_requested = $_POST['amount'];

        //ensure method exists and belongs to user
        $method_details->details = $method_details->details_array;

        if ($method_details == null) {
            Session::putFlash('danger', "Withdrawal method does not exist");
            Redirect::back();
        }


        $rules = SiteSettings::find_criteria('rules_settings')->settingsArray;

        DB::beginTransaction();

        $status = false;

        try {

            $withdrawal_account = $auth->getAccount('default');
            $request = AccountManager::withdrawal([
                'withdrawal_account' => $withdrawal_account->id,
                'withdrawal_method' => $withdrawal_account->id,
                'amount' => $amount_requested,
                'status' => 2, //pending
                'collect_withdrawal_fee' => true,
                'narration' => "withdrawal",
                'journal_date' => null,
                'user_id' => $auth->id,
                "method" =>  json_encode($method_details->toArray())
            ]);

            $request->updateDetailsByKey('withdrawal_method', ($method_details->toArray()));

            $withdrawal_fee = $method_details->method == 'paypal' ? $rules['withdrawal_fee_percent'] * 0.01 * $amount_requested : 0;
            $payable = $amount_requested - $withdrawal_fee;

            $payables = [
                "amount" => $amount_requested,
                "fee" => $withdrawal_fee,
                "payable" => $payable,
            ];

            $request->updateDetailsByKey('payables', $payables);

            if (!$request) {
                throw new Exception("Error Processing Request", 1);
            }

            DB::commit();
            Session::putFlash('success', "Withdrawal initiated successfully");
            $status = true;
        } catch (Exception $e) {
            DB::rollback();
            Session::putFlash('danger', "Something went wrong. Please try again.");

        }

        echo json_encode([
            'status' => $status
        ]);
        // Redirect::back();
    }


    private function callPeraWalletApi($url, $purestake = false) {
      $curl = curl_init();

      $xApiKey = $purestake ? '6iq400ijoVaRYvbGTUw6fRN3lFFbKMb1lYZMG1oj' : 'pera-web-Dr98Vnmu-0yFejf-G-A1M7-7cZS6P0d-';

      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          "x-api-key: $xApiKey"
        ),
      ));

      $response = curl_exec($curl);

      $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);

      return [
        'response' => $response,
        'httpcode' => $httpcode
      ];
    }

    private function validPeraWalletAddress() {
      $response = $this->callPeraWalletApi("https://mainnet.api.perawallet.app/v1/accounts/{$_POST['details']['perawallet_address']}/");

      return $response['httpcode'] == 200;
    }

    private function hasTLPCoins() {
      $response = $this->callPeraWalletApi("https://mainnet-algorand.api.purestake.io/ps2/v2/accounts/{$_POST['details']['perawallet_address']}/assets/987374809", true);

      $responseBody = json_decode($response['response'], true);

      return isset($responseBody['asset-holding']['asset-id']);
    }

    public function submit_withdrawal_information($value = '')
    {

        $this->validator()->check(Input::all(), array(
            'method' => [
                'required' => true,
            ],
            'payment_method' => [
                // 'required'=> true,
            ],
        ));

        $auth = $this->auth();


        if (!$this->validator->passed()) {
            Session::putFlash('danger', Input::inputErrors());
            Redirect::back();
        }

        if (isset($_POST['details']['perawallet_address'])) {
          if (!$this->validPeraWalletAddress()) {
            Session::putFlash('danger', 'Invalid pera wallet address!');
            Redirect::back();
          }

          if (!$this->hasTLPCoins()) {
            Session::putFlash('danger', 'TLP Coins are not added in your assets!');
            Redirect::back();
          }
        }
        
        $this->verify_2fa();

        $available_methods = UserWithdrawalMethod::$method_options;
        $decoded_method = MIS::dec_enc('decrypt', $_POST['method']);

        if (!array_key_exists($decoded_method, $available_methods)) {
            Session::putFlash('danger', Input::inputErrors());
            Redirect::back();
        }

        $option = $available_methods[$decoded_method];

        DB::beginTransaction();
        $status = false;
        try {

            $user_withdrawal = UserWithdrawalMethod::updateOrCreate(
                [
                    'user_id' => $auth->id,
                    'method' => $decoded_method,
                ],
                [
                    'details' => json_encode($_POST['details'])
                ]
            );

            DB::commit();
            Session::putFlash('success', "$option[name] changes saved");
            $status = true;
            $withdrawal_method = $user_withdrawal;
            $user = $auth;
            // SendEmailForWithdrawalInformationUpdate::dispatch(compact('user', 'withdrawal_method'));
        } catch (Exception $e) {
            DB::rollback();
            Session::putFlash('success', "Something went wrong. Please try again.");
        }

        echo json_encode([
            'status' => $status
        ]);
        // Redirect::back();
    }
}
