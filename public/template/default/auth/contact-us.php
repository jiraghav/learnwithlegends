<?php
$page_title = "Contact us";
include 'includes/header.php';
?>

<div class="page-wrapper">

    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Contact us</h3>

                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="">Let us help you solve your issue.</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-center">
                <div class="customize-input float-right">
                    <a class="btn btn-light" href="<?= domain; ?>/user/support">See Tickets</a>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid">


        <div class="card">
            <div class="card-content collapse show">
                <div class="card-body card-dashboard">

                    <form class="contact-form mt-45" id="contact" method="post" action="<?= domain; ?>/ticket_crud/create_ticket">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">

                                <div class="form-field">
                                    <input class="form-control" value="<?= $auth->full_name; ?>" readonly="readonly" id="name" type="hidden" required="" name="full_name" placeholder="Your Name">

                                    <input class="form-control" id="email" value="<?= $auth->email; ?>" readonly="readonly" type="hidden" required="" name="email" placeholder="Email">
                                </div>
                                <div class="form-field">
                                    <input class="form-control" id="sub" value="<?= $auth->phone; ?>" readonly="readonly" type="hidden" required="" name="phone" placeholder="Phone">
                                </div>

                                <input type="hidden" name="from_client" value="true">

                            </div>
                            <div class="col-md-12 col-lg-12">
                                <div class="form-field">

                                    <textarea class="form-control" id="message" rows="7" name="comment" required="" placeholder="Your Message"></textarea>
                                </div>
                            </div>


                            <div class="col-md-12 col-lg-12 mt-2">
                                <div class="form-field">
                                    <label><input type="checkbox" required> I agree with the Privacy policy to receive communications</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-offset-md-2">
                                <br>
                                <?= MIS::use_google_recaptcha(); ?>
                            </div>


                            <div class="col-md-12 col-lg-12 mt-30">
                                <button class=" btn btn-dark" type="submit" id="submit" name="button">
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>



</div>

<?php include 'includes/footer.php'; ?>